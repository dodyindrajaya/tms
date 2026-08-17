<?php

namespace App\Controllers;

class AccountingController extends BaseController
{
    public function journal()
    {
        $db = db_connect();
        $entries = $db->table('journal_entries je')
            ->select('je.*, j.code AS journal_code, j.name AS journal_name, COALESCE(SUM(l.debit),0) AS total_debit, COALESCE(SUM(l.credit),0) AS total_credit')
            ->join('journals j', 'j.id=je.journal_id', 'left')
            ->join('journal_entry_lines l', 'l.journal_entry_id=je.id', 'left')
            ->groupBy('je.id')
            ->orderBy('je.entry_date', 'DESC')->orderBy('je.id', 'DESC')
            ->get()->getResultArray();

        return view('accounting/journal', [
            'title' => 'Journal Entries',
            'entries' => $entries,
        ]);
    }

    public function gl()
    {
        $db = db_connect();
        $lines = $db->table('journal_entry_lines l')
            ->select('l.*, je.entry_no, je.entry_date, je.description AS entry_description, a.code AS account_code, a.name AS account_name')
            ->join('journal_entries je', 'je.id=l.journal_entry_id', 'left')
            ->join('accounts a', 'a.id=l.account_id', 'left')
            ->where('je.status', 'posted')
            ->orderBy('je.entry_date', 'DESC')->orderBy('l.id', 'DESC')
            ->get()->getResultArray();

        return view('accounting/gl', [
            'title' => 'General Ledger',
            'lines' => $lines,
        ]);
    }

    /**
     * Accounts Receivable.
     * Only posted/partial/overdue customer invoices with a positive
     * outstanding balance are shown.
     */
    public function ar()
    {
        $db = db_connect();

        $rows = $db->table('invoices i')
            ->select('i.*, c.name AS customer_name, c.customer_code, b.booking_no')
            ->join('customers c', 'c.id=i.customer_id', 'left')
            ->join('bookings b', 'b.id=i.booking_id', 'left')
            ->whereIn('i.status', ['posted', 'partial', 'overdue'])
            ->where('i.outstanding_amount >', 0)
            ->orderBy('i.due_date', 'ASC')
            ->orderBy('i.id', 'DESC')
            ->get()
            ->getResultArray();

        // Always pass an array to the view, even when there are no records.
        $rows = is_array($rows) ? $rows : [];
        $total = 0.0;

        foreach ($rows as $row) {
            $total += (float) ($row['outstanding_amount'] ?? 0);
        }

        return view('accounting/ar', [
            'title' => 'Receivable',
            'rows' => $rows,
            'total' => $total,
        ]);
    }

    /**
     * Accounts Payable.
     * Only posted/partial/overdue supplier bills with a positive
     * outstanding balance are shown.
     */
    public function ap()
    {
        $db = db_connect();

        $rows = $db->table('supplier_bills sb')
            ->select('sb.*, s.name AS supplier_name, s.supplier_code, b.booking_no')
            ->join('suppliers s', 's.id=sb.supplier_id', 'left')
            ->join('bookings b', 'b.id=sb.booking_id', 'left')
            ->whereIn('sb.status', ['posted', 'partial', 'overdue'])
            ->where('sb.outstanding_amount >', 0)
            ->orderBy('sb.due_date', 'ASC')
            ->orderBy('sb.id', 'DESC')
            ->get()
            ->getResultArray();

        // Always pass an array to the view, even when there are no records.
        $rows = is_array($rows) ? $rows : [];
        $total = 0.0;

        foreach ($rows as $row) {
            $total += (float) ($row['outstanding_amount'] ?? 0);
        }

        return view('accounting/ap', [
            'title' => 'Payable',
            'rows' => $rows,
            'total' => $total,
        ]);
    }
}
