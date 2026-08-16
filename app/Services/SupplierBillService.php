<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class SupplierBillService
{
    protected BaseConnection $db;
    protected AccountingService $accounting;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->accounting = new AccountingService($this->db);
    }

    /**
     * Creates supplier bill from booking costs.
     * DR Cost of Sales / Expense
     * CR Accounts Payable
     */
    public function createFromBooking(int $bookingId, int $supplierId, array $costs, ?int $createdBy = null): int
    {
        if (!$costs) {
            throw new RuntimeException('At least one supplier cost is required.');
        }

        $total = 0;

        foreach ($costs as &$cost) {
            $cost['_line_total'] = (float)($cost['amount'] ?? 0);
            if ($cost['_line_total'] <= 0) {
                throw new RuntimeException('Supplier cost must be greater than zero.');
            }
            $total += $cost['_line_total'];
        }
        unset($cost);

        $billNo = 'BILL-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));

        $this->db->transStart();

        $this->db->table('supplier_bills')->insert([
            'bill_no' => $billNo,
            'supplier_id' => $supplierId,
            'booking_id' => $bookingId,
            'bill_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d'),
            'status' => 'draft',
            'total_amount' => $total,
            'paid_amount' => 0,
            'outstanding_amount' => $total,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $billId = (int)$this->db->insertID();

        foreach ($costs as $cost) {
            $this->db->table('supplier_bill_items')->insert([
                'supplier_bill_id' => $billId,
                'booking_cost_id' => $cost['booking_cost_id'] ?? null,
                'description' => $cost['description'] ?? 'Travel cost',
                'quantity' => $cost['quantity'] ?? 1,
                'unit_cost' => $cost['unit_cost'] ?? $cost['_line_total'],
                'line_total' => $cost['_line_total'],
                'expense_account_id' => $cost['expense_account_id'] ?? $this->accounting->accountId('5100'),
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to create supplier bill.');
        }

        return $billId;
    }

    public function post(int $billId, ?int $userId = null): int
    {
        $bill = $this->db->table('supplier_bills')->where('id', $billId)->get()->getRowArray();

        if (!$bill) {
            throw new RuntimeException('Supplier bill not found.');
        }

        if (!empty($bill['journal_entry_id'])) {
            return (int)$bill['journal_entry_id'];
        }

        $ap = $this->accounting->accountId('2100');

        $items = $this->db->table('supplier_bill_items')
            ->where('supplier_bill_id', $billId)
            ->get()->getResultArray();

        $lines = [];
        foreach ($items as $item) {
            $lines[] = [
                'account_id' => $item['expense_account_id'] ?: $this->accounting->accountId('5100'),
                'supplier_id' => $bill['supplier_id'],
                'booking_id' => $bill['booking_id'],
                'description' => $item['description'],
                'debit' => $item['line_total'],
                'credit' => 0,
            ];
        }

        $lines[] = [
            'account_id' => $ap,
            'supplier_id' => $bill['supplier_id'],
            'booking_id' => $bill['booking_id'],
            'description' => 'Accounts Payable - ' . $bill['bill_no'],
            'debit' => 0,
            'credit' => $bill['total_amount'],
        ];

        $entryId = $this->accounting->createJournalEntry([
            'journal_id' => $this->journalId('PURCHASE'),
            'entry_no' => $this->accounting->nextEntryNo('BILL'),
            'entry_date' => $bill['bill_date'],
            'reference_type' => 'supplier_bill',
            'reference_id' => $billId,
            'description' => 'Supplier bill ' . $bill['bill_no'],
            'created_by' => $userId,
        ], $lines);

        $this->accounting->postJournalEntry($entryId, $userId);

        $this->db->table('supplier_bills')->where('id', $billId)->update([
            'status' => 'posted',
            'journal_entry_id' => $entryId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $entryId;
    }

    protected function journalId(string $code): int
    {
        $row = $this->db->table('journals')->where('code', $code)->get()->getRow();

        if (!$row) {
            throw new RuntimeException("Journal {$code} not found.");
        }

        return (int)$row->id;
    }
}
