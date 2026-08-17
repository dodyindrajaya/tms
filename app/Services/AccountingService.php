<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class AccountingService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /**
     * Create an unposted journal entry.
     * When $manageTransaction is false the caller owns the surrounding DB transaction.
     */
    public function createJournalEntry(array $header, array $lines, bool $manageTransaction = true): int
    {
        if (empty($lines)) {
            throw new RuntimeException('Journal entry must contain at least one line.');
        }

        $debit = 0.0;
        $credit = 0.0;

        foreach ($lines as $line) {
            $accountId = (int)($line['account_id'] ?? 0);
            $d = round((float)($line['debit'] ?? 0), 2);
            $c = round((float)($line['credit'] ?? 0), 2);

            if ($accountId <= 0) {
                throw new RuntimeException('Each journal line requires a valid account.');
            }
            if ($d < 0 || $c < 0) {
                throw new RuntimeException('Debit/credit cannot be negative.');
            }
            if ($d > 0 && $c > 0) {
                throw new RuntimeException('A journal line cannot contain both debit and credit.');
            }
            if ($d == 0.0 && $c == 0.0) {
                throw new RuntimeException('A journal line must contain debit or credit.');
            }

            $debit += $d;
            $credit += $c;
        }

        if (round($debit, 2) !== round($credit, 2)) {
            throw new RuntimeException(sprintf(
                'Unbalanced journal: debit %.2f, credit %.2f.',
                $debit,
                $credit
            ));
        }

        if (empty($header['journal_id']) || empty($header['entry_no']) || empty($header['entry_date'])) {
            throw new RuntimeException('journal_id, entry_no and entry_date are required.');
        }

        $this->assertPostingDateOpen((string)$header['entry_date']);

        if ($manageTransaction) {
            $this->db->transBegin();
        }

        try {
            $now = date('Y-m-d H:i:s');
            $this->db->table('journal_entries')->insert([
                'journal_id'     => (int)$header['journal_id'],
                'entry_no'       => $header['entry_no'],
                'entry_date'     => $header['entry_date'],
                'reference_type' => $header['reference_type'] ?? null,
                'reference_id'   => $header['reference_id'] ?? null,
                'description'    => $header['description'] ?? null,
                'status'         => $header['status'] ?? 'draft',
                'posted_at'      => $header['posted_at'] ?? null,
                'posted_by'      => $header['posted_by'] ?? null,
                'created_by'     => $header['created_by'] ?? null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $entryId = (int)$this->db->insertID();
            if ($entryId <= 0) {
                throw new RuntimeException('Failed to create journal header.');
            }

            foreach ($lines as $line) {
                $this->db->table('journal_entry_lines')->insert([
                    'journal_entry_id' => $entryId,
                    'account_id'       => (int)$line['account_id'],
                    'customer_id'      => $line['customer_id'] ?? null,
                    'supplier_id'      => $line['supplier_id'] ?? null,
                    'booking_id'       => $line['booking_id'] ?? null,
                    'description'      => $line['description'] ?? null,
                    'debit'            => round((float)($line['debit'] ?? 0), 2),
                    'credit'           => round((float)($line['credit'] ?? 0), 2),
                    'line_date'        => $line['line_date'] ?? $header['entry_date'],
                ]);
            }

            if ($manageTransaction) {
                $this->db->transCommit();
                if ($this->db->transStatus() === false) {
                    throw new RuntimeException('Failed to create journal entry.');
                }
            }

            return $entryId;
        } catch (\Throwable $e) {
            if ($manageTransaction) {
                $this->db->transRollback();
            }
            throw $e;
        }
    }

    public function postJournalEntry(int $entryId, ?int $postedBy = null): bool
    {
        $entry = $this->db->table('journal_entries')
            ->where('id', $entryId)
            ->get()
            ->getRowArray();

        if (!$entry) {
            throw new RuntimeException('Journal entry not found.');
        }

        if ($entry['status'] === 'posted') {
            return true;
        }

        $this->assertPostingDateOpen((string)$entry['entry_date']);

        $lines = $this->db->table('journal_entry_lines')
            ->where('journal_entry_id', $entryId)
            ->get()
            ->getResultArray();

        if (!$lines) {
            throw new RuntimeException('Cannot post an empty journal entry.');
        }

        $debit = array_sum(array_map(static fn($r) => (float)$r['debit'], $lines));
        $credit = array_sum(array_map(static fn($r) => (float)$r['credit'], $lines));

        if (round($debit, 2) !== round($credit, 2)) {
            throw new RuntimeException(sprintf(
                'Cannot post an unbalanced journal entry: debit %.2f, credit %.2f.',
                $debit,
                $credit
            ));
        }

        $ok = $this->db->table('journal_entries')
            ->where('id', $entryId)
            ->update([
                'status'     => 'posted',
                'posted_at'  => date('Y-m-d H:i:s'),
                'posted_by'  => $postedBy,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (!$ok) {
            throw new RuntimeException('Failed to post journal entry.');
        }

        return true;
    }

    public function nextEntryNo(string $prefix = 'JE'): string
    {
        return $prefix . '-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }

    public function accountId(string $code): int
    {
        $row = $this->db->table('accounts')->where('code', $code)->get()->getRow();
        if (!$row) {
            throw new RuntimeException("Account code {$code} not found.");
        }
        return (int)$row->id;
    }

    public function journalId(string $code): int
    {
        $row = $this->db->table('journals')->where('code', $code)->where('is_active', 1)->get()->getRow();
        if (!$row) {
            throw new RuntimeException("Journal {$code} not found or inactive.");
        }
        return (int)$row->id;
    }

    public function findPostedByReference(string $referenceType, int $referenceId): ?array
    {
        $row = $this->db->table('journal_entries')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('status', 'posted')
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();
        return $row ?: null;
    }

    public function assertPostingDateOpen(string $date): void
    {
        $date = substr($date, 0, 10);
        $period = $this->db->table('fiscal_periods')
            ->where('start_date <=', $date)
            ->where('end_date >=', $date)
            ->get()->getRowArray();

        if ($period && strtolower((string)$period['status']) !== 'open') {
            throw new RuntimeException("Fiscal period for {$date} is closed.");
        }
    }
}
