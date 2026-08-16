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
     * Lines must contain account_id, debit, credit and optional references.
     */
    public function createJournalEntry(array $header, array $lines): int
    {
        if (empty($lines)) {
            throw new RuntimeException('Journal entry must contain at least one line.');
        }

        $debit = 0.0;
        $credit = 0.0;

        foreach ($lines as $line) {
            $d = (float) ($line['debit'] ?? 0);
            $c = (float) ($line['credit'] ?? 0);

            if ($d < 0 || $c < 0) {
                throw new RuntimeException('Debit/credit cannot be negative.');
            }

            if ($d > 0 && $c > 0) {
                throw new RuntimeException('A journal line cannot contain both debit and credit.');
            }

            if ($d == 0 && $c == 0) {
                throw new RuntimeException('A journal line must contain debit or credit.');
            }

            $debit += $d;
            $credit += $c;
        }

        if (round($debit, 2) !== round($credit, 2)) {
            throw new RuntimeException(
                sprintf('Unbalanced journal: debit %.2f, credit %.2f.', $debit, $credit)
            );
        }

        if (empty($header['journal_id']) || empty($header['entry_no']) || empty($header['entry_date'])) {
            throw new RuntimeException('journal_id, entry_no and entry_date are required.');
        }

        $this->db->transStart();

        $entryData = [
            'journal_id'      => $header['journal_id'],
            'entry_no'        => $header['entry_no'],
            'entry_date'      => $header['entry_date'],
            'reference_type'  => $header['reference_type'] ?? null,
            'reference_id'    => $header['reference_id'] ?? null,
            'description'     => $header['description'] ?? null,
            'status'          => $header['status'] ?? 'draft',
            'posted_at'       => $header['posted_at'] ?? null,
            'posted_by'       => $header['posted_by'] ?? null,
            'created_by'      => $header['created_by'] ?? null,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->db->table('journal_entries')->insert($entryData);
        $entryId = (int) $this->db->insertID();

        foreach ($lines as $line) {
            $this->db->table('journal_entry_lines')->insert([
                'journal_entry_id' => $entryId,
                'account_id'       => $line['account_id'],
                'customer_id'      => $line['customer_id'] ?? null,
                'supplier_id'      => $line['supplier_id'] ?? null,
                'booking_id'       => $line['booking_id'] ?? null,
                'description'      => $line['description'] ?? null,
                'debit'            => $line['debit'] ?? 0,
                'credit'           => $line['credit'] ?? 0,
                'line_date'        => $line['line_date'] ?? $header['entry_date'],
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to create journal entry.');
        }

        return $entryId;
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

        $lines = $this->db->table('journal_entry_lines')
            ->where('journal_entry_id', $entryId)
            ->get()
            ->getResultArray();

        if (!$lines) {
            throw new RuntimeException('Cannot post an empty journal entry.');
        }

        $debit = array_sum(array_column($lines, 'debit'));
        $credit = array_sum(array_column($lines, 'credit'));

        if (round((float)$debit, 2) !== round((float)$credit, 2)) {
            throw new RuntimeException('Cannot post an unbalanced journal entry.');
        }

        return $this->db->table('journal_entries')
            ->where('id', $entryId)
            ->update([
                'status'    => 'posted',
                'posted_at' => date('Y-m-d H:i:s'),
                'posted_by' => $postedBy,
                'updated_at'=> date('Y-m-d H:i:s'),
            ]);
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

        return (int) $row->id;
    }
}
