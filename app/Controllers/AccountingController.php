<?php

namespace App\Controllers;

class AccountingController extends BaseController
{
    /**
     * Finance Dashboard
     *
     * Keeps the existing TMS finance dashboard logic while making the
     * controller tolerant of empty/null aggregate results.
     */
    public function finance()
    {
        $db = db_connect();

        $monthStart     = date('Y-m-01');
        $nextMonthStart = date('Y-m-01', strtotime('+1 month'));

        // ------------------------------------------------------------------
        // AR / AP summaries
        // ------------------------------------------------------------------
        $arSummary = $db->table('invoices')
            ->select('COUNT(*) AS doc_count, COALESCE(SUM(outstanding_amount),0) AS amount')
            ->where('outstanding_amount >', 0)
            ->where('status !=', 'cancelled')
            ->get()
            ->getRowArray() ?: [];

        $apSummary = $db->table('supplier_bills')
            ->select('COUNT(*) AS doc_count, COALESCE(SUM(outstanding_amount),0) AS amount')
            ->where('outstanding_amount >', 0)
            ->where('status !=', 'cancelled')
            ->get()
            ->getRowArray() ?: [];

        $ar      = (float) ($arSummary['amount'] ?? 0);
        $ap      = (float) ($apSummary['amount'] ?? 0);
        $arCount = (int) ($arSummary['doc_count'] ?? 0);
        $apCount = (int) ($apSummary['doc_count'] ?? 0);

        // ------------------------------------------------------------------
        // AR / AP overdue
        // ------------------------------------------------------------------
        $arOverdue = $db->table('invoices')
            ->select('COUNT(*) AS doc_count, COALESCE(SUM(outstanding_amount),0) AS amount')
            ->where('outstanding_amount >', 0)
            ->where('status !=', 'cancelled')
            ->where('due_date <', date('Y-m-d'))
            ->where('due_date IS NOT NULL', null, false)
            ->get()
            ->getRowArray() ?: [];

        $apOverdue = $db->table('supplier_bills')
            ->select('COUNT(*) AS doc_count, COALESCE(SUM(outstanding_amount),0) AS amount')
            ->where('outstanding_amount >', 0)
            ->where('status !=', 'cancelled')
            ->where('due_date <', date('Y-m-d'))
            ->where('due_date IS NOT NULL', null, false)
            ->get()
            ->getRowArray() ?: [];

        $arOverdueAmount = (float) ($arOverdue['amount'] ?? 0);
        $apOverdueAmount = (float) ($apOverdue['amount'] ?? 0);
        $arOverdueCount  = (int) ($arOverdue['doc_count'] ?? 0);
        $apOverdueCount  = (int) ($apOverdue['doc_count'] ?? 0);

        // ------------------------------------------------------------------
        // Cash / Bank balances
        // 1100 = Cash, 1200 = Bank in the seeded TMS COA.
        // ------------------------------------------------------------------
        $cashBalance   = $this->accountBalance($db, '1100');
        $bankBalance   = $this->accountBalance($db, '1200');
        $liquidBalance = $cashBalance + $bankBalance;
        $netLiquidity  = $liquidBalance + $ar - $ap;

        // ------------------------------------------------------------------
        // Current month posted journals
        // ------------------------------------------------------------------
        $monthJournal = $db->table('journal_entries je')
            ->select(
                'COUNT(DISTINCT je.id) AS entries,
                 COALESCE(SUM(l.debit),0) AS debit,
                 COALESCE(SUM(l.credit),0) AS credit'
            )
            ->join('journal_entry_lines l', 'l.journal_entry_id=je.id', 'left')
            ->where('je.status', 'posted')
            ->where('je.entry_date >=', $monthStart)
            ->where('je.entry_date <', $nextMonthStart)
            ->get()
            ->getRowArray() ?: [];

        $monthJournal = array_merge(
            ['entries' => 0, 'debit' => 0, 'credit' => 0],
            $monthJournal
        );

        // ------------------------------------------------------------------
        // Current month cash movement
        // For asset accounts, debit is money in and credit is money out.
        // ------------------------------------------------------------------
        $cashflow = $db->table('journal_entry_lines l')
            ->select(
                'COALESCE(SUM(l.debit),0) AS inflow,
                 COALESCE(SUM(l.credit),0) AS outflow'
            )
            ->join('journal_entries je', 'je.id=l.journal_entry_id', 'inner')
            ->join('accounts a', 'a.id=l.account_id', 'inner')
            ->where('je.status', 'posted')
            ->whereIn('a.code', ['1100', '1200'])
            ->where('je.entry_date >=', $monthStart)
            ->where('je.entry_date <', $nextMonthStart)
            ->get()
            ->getRowArray() ?: [];

        $cashflow = array_merge(
            ['inflow' => 0, 'outflow' => 0],
            $cashflow
        );

        $cashflow['net'] = (float) $cashflow['inflow'] - (float) $cashflow['outflow'];

        // ------------------------------------------------------------------
        // Six-month cashflow series
        // ------------------------------------------------------------------
        $sixMonthStart = date('Y-m-01', strtotime('-5 months'));

        $monthlyCashflow = $db->query(
            "SELECT DATE_FORMAT(je.entry_date, '%Y-%m') AS month,
                    COALESCE(SUM(l.debit),0) AS inflow,
                    COALESCE(SUM(l.credit),0) AS outflow
             FROM journal_entry_lines l
             INNER JOIN journal_entries je
                    ON je.id=l.journal_entry_id
                   AND je.status='posted'
             INNER JOIN accounts a ON a.id=l.account_id
             WHERE a.code IN ('1100','1200')
               AND je.entry_date >= ?
               AND je.entry_date < ?
             GROUP BY month
             ORDER BY month ASC",
            [$sixMonthStart, $nextMonthStart]
        )->getResultArray();

        $series = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime('-' . $i . ' months'));

            $series[$m] = [
                'month'   => $m,
                'inflow'  => 0,
                'outflow' => 0,
            ];
        }

        foreach ($monthlyCashflow as $row) {
            $m = $row['month'];

            if (isset($series[$m])) {
                $series[$m]['inflow']  = (float) ($row['inflow'] ?? 0);
                $series[$m]['outflow'] = (float) ($row['outflow'] ?? 0);
            }
        }

        $monthlyCashflow = array_values($series);

        // ------------------------------------------------------------------
        // Recent posted journals
        // ------------------------------------------------------------------
        $recent = $db->table('journal_entries je')
            ->select(
                "je.id,
                 je.entry_no,
                 je.entry_date,
                 je.description,
                 je.status,
                 j.code AS journal_code,
                 COALESCE(SUM(l.debit),0) AS debit,
                 COALESCE(SUM(l.credit),0) AS credit"
            )
            ->join('journals j', 'j.id=je.journal_id', 'left')
            ->join('journal_entry_lines l', 'l.journal_entry_id=je.id', 'left')
            ->where('je.status', 'posted')
            ->groupBy(
                'je.id, je.entry_no, je.entry_date, je.description,
                 je.status, j.code'
            )
            ->orderBy('je.entry_date', 'DESC')
            ->orderBy('je.id', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        // ------------------------------------------------------------------
        // Top outstanding AR / AP documents
        // ------------------------------------------------------------------
        $arItems = $db->table('invoices i')
            ->select(
                'i.id,
                 i.invoice_no,
                 i.invoice_date,
                 i.due_date,
                 i.outstanding_amount,
                 c.name AS customer_name'
            )
            ->join('customers c', 'c.id=i.customer_id', 'left')
            ->where('i.outstanding_amount >', 0)
            ->where('i.status !=', 'cancelled')
            ->orderBy('i.due_date', 'ASC')
            ->orderBy('i.id', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        $apItems = $db->table('supplier_bills b')
            ->select(
                'b.id,
                 b.bill_no,
                 b.bill_date,
                 b.due_date,
                 b.outstanding_amount,
                 s.name AS supplier_name'
            )
            ->join('suppliers s', 's.id=b.supplier_id', 'left')
            ->where('b.outstanding_amount >', 0)
            ->where('b.status !=', 'cancelled')
            ->orderBy('b.due_date', 'ASC')
            ->orderBy('b.id', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        return view('accounting/dashboard', [
            'title'            => 'Finance Dashboard',
            'ar'               => $ar,
            'ap'               => $ap,
            'arCount'          => $arCount,
            'apCount'          => $apCount,
            'arOverdueAmount'  => $arOverdueAmount,
            'apOverdueAmount'  => $apOverdueAmount,
            'arOverdueCount'   => $arOverdueCount,
            'apOverdueCount'   => $apOverdueCount,
            'cashBalance'      => $cashBalance,
            'bankBalance'      => $bankBalance,
            'liquidBalance'    => $liquidBalance,
            'netLiquidity'     => $netLiquidity,
            'monthJournal'     => $monthJournal,
            'cashflow'         => $cashflow,
            'monthlyCashflow'  => $monthlyCashflow,
            'recent'           => $recent,
            'arItems'          => $arItems,
            'apItems'          => $apItems,
            'monthLabel'       => date('F Y'),
        ]);
    }

    /**
     * Accounts Receivable
     *
     * IMPORTANT:
     * The current ar.php view expects $rows, not only $items.
     * We therefore provide both variables for backward compatibility.
     */
    public function ar()
    {
        $db = db_connect();

        $items = $db->table('invoices i')
            ->select(
                'i.*,
                 c.name AS customer_name,
                 b.booking_no'
            )
            ->join('customers c', 'c.id=i.customer_id', 'left')
            ->join('bookings b', 'b.id=i.booking_id', 'left')
            ->where('i.outstanding_amount >', 0)
            ->where('i.status !=', 'cancelled')
            ->orderBy('i.due_date', 'ASC')
            ->orderBy('i.id', 'DESC')
            ->get()
            ->getResultArray();

        $rows = [];

        foreach ($items as $item) {
            $dueDate = $item['due_date'] ?? null;

            $status = 'open';

            if (!empty($dueDate) && $dueDate < date('Y-m-d')) {
                $status = 'overdue';
            } elseif (($item['status'] ?? '') === 'partial_paid') {
                $status = 'partial';
            }

            $rows[] = [
                'id'                 => (int) ($item['id'] ?? 0),
                'invoice_no'         => $item['invoice_no'] ?? '',
                'customer_name'      => $item['customer_name'] ?? '-',
                'booking_no'         => $item['booking_no'] ?? '-',
                'due_date'           => $dueDate,
                'status'             => $status,
                'total_amount'       => (float) ($item['total_amount'] ?? 0),
                'paid_amount'        => (float) ($item['paid_amount'] ?? 0),
                'outstanding_amount' => (float) ($item['outstanding_amount'] ?? 0),
            ];
        }

        $total = array_sum(
            array_map(
                static fn ($row) => (float) ($row['outstanding_amount'] ?? 0),
                $rows
            )
        );

        return view('accounting/ar', [
            'title' => 'Receivable',
            'rows'  => $rows,
            'items' => $items, // backward compatibility
            'total' => $total,
        ]);
    }

    /**
     * Accounts Payable
     *
     * IMPORTANT:
     * The current ap.php view expects $rows, not only $items.
     * We therefore provide both variables for backward compatibility.
     */
    public function ap()
    {
        $db = db_connect();

        $items = $db->table('supplier_bills b')
            ->select(
                'b.*,
                 s.name AS supplier_name'
            )
            ->join('suppliers s', 's.id=b.supplier_id', 'left')
            ->where('b.outstanding_amount >', 0)
            ->where('b.status !=', 'cancelled')
            ->orderBy('b.due_date', 'ASC')
            ->orderBy('b.id', 'DESC')
            ->get()
            ->getResultArray();

        $rows = [];

        foreach ($items as $item) {
            $dueDate = $item['due_date'] ?? null;

            $status = 'open';

            if (!empty($dueDate) && $dueDate < date('Y-m-d')) {
                $status = 'overdue';
            } elseif (($item['status'] ?? '') === 'partial_paid') {
                $status = 'partial';
            }

            $rows[] = [
                'id'                 => (int) ($item['id'] ?? 0),
                'bill_no'            => $item['bill_no'] ?? '',
                'supplier_name'      => $item['supplier_name'] ?? '-',
                'booking_no'         => $item['booking_no'] ?? '-',
                'due_date'           => $dueDate,
                'status'             => $status,
                'total_amount'       => (float) ($item['total_amount'] ?? 0),
                'paid_amount'        => (float) ($item['paid_amount'] ?? 0),
                'outstanding_amount' => (float) ($item['outstanding_amount'] ?? 0),
            ];
        }

        $total = array_sum(
            array_map(
                static fn ($row) => (float) ($row['outstanding_amount'] ?? 0),
                $rows
            )
        );

        return view('accounting/ap', [
            'title' => 'Payable',
            'rows'  => $rows,
            'items' => $items, // backward compatibility
            'total' => $total,
        ]);
    }

    /**
     * Return posted balance for an account.
     *
     * For the current TMS COA, 1100 Cash and 1200 Bank are asset accounts,
     * therefore debit - credit gives their running balance.
     */
    protected function accountBalance($db, string $code): float
    {
        $row = $db->table('journal_entry_lines l')
            ->select('COALESCE(SUM(l.debit - l.credit),0) AS balance')
            ->join(
                'journal_entries je',
                'je.id=l.journal_entry_id',
                'inner'
            )
            ->join(
                'accounts a',
                'a.id=l.account_id',
                'inner'
            )
            ->where('je.status', 'posted')
            ->where('a.code', $code)
            ->get()
            ->getRowArray();

        return (float) ($row['balance'] ?? 0);
    }

    /**
     * Journal Entries
     */
    public function journal()
    {
        $db = db_connect();

        $entries = $db->table('journal_entries je')
            ->select(
                "je.id,
                 je.entry_no,
                 je.entry_date,
                 je.reference_type,
                 je.reference_id,
                 je.description,
                 je.status,
                 je.posted_at,
                 j.code AS journal_code,
                 j.name AS journal_name,
                 COALESCE(SUM(l.debit), 0) AS total_debit,
                 COALESCE(SUM(l.credit), 0) AS total_credit"
            )
            ->join('journals j', 'j.id = je.journal_id', 'left')
            ->join(
                'journal_entry_lines l',
                'l.journal_entry_id = je.id',
                'left'
            )
            ->groupBy(
                'je.id, je.entry_no, je.entry_date, je.reference_type,
                 je.reference_id, je.description, je.status, je.posted_at,
                 j.code, j.name'
            )
            ->orderBy('je.entry_date', 'DESC')
            ->orderBy('je.id', 'DESC')
            ->get()
            ->getResultArray();

        $totalDebit  = 0.0;
        $totalCredit = 0.0;

        foreach ($entries as $entry) {
            $totalDebit  += (float) ($entry['total_debit'] ?? 0);
            $totalCredit += (float) ($entry['total_credit'] ?? 0);
        }

        return view('accounting/journal', [
            'title'       => 'Journal Entries',
            'entries'     => $entries,
            'totalDebit'  => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }

    /**
     * General Ledger
     */
    public function gl()
    {
        $db = db_connect();

        $lines = $db->table('journal_entry_lines l')
            ->select(
                'l.*,
                 je.entry_no,
                 je.entry_date,
                 a.code AS account_code,
                 a.name AS account_name'
            )
            ->join(
                'journal_entries je',
                'je.id=l.journal_entry_id',
                'left'
            )
            ->join(
                'accounts a',
                'a.id=l.account_id',
                'left'
            )
            ->where('je.status', 'posted')
            ->orderBy('je.entry_date', 'DESC')
            ->orderBy('l.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('accounting/gl', [
            'title' => 'General Ledger',
            'lines' => $lines,
        ]);
    }
}