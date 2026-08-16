<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class InvoiceService
{
    protected BaseConnection $db;
    protected AccountingService $accounting;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->accounting = new AccountingService($this->db);
    }

    public function createFromBooking(int $bookingId, ?int $createdBy = null, ?string $dueDate = null): int
    {
        $booking = $this->db->table('bookings')->where('id', $bookingId)->get()->getRowArray();

        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }

        $existing = $this->db->table('invoices')->where('booking_id', $bookingId)->get()->getRow();

        if ($existing) {
            return (int)$existing->id;
        }

        $items = $this->db->table('booking_items')
            ->where('booking_id', $bookingId)
            ->get()->getResultArray();

        if (!$items) {
            throw new RuntimeException('Booking has no items.');
        }

        $invoiceNo = 'INV-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));

        $this->db->transStart();

        $this->db->table('invoices')->insert([
            'invoice_no' => $invoiceNo,
            'booking_id' => $bookingId,
            'customer_id' => $booking['customer_id'],
            'invoice_date' => date('Y-m-d'),
            'due_date' => $dueDate ?? date('Y-m-d'),
            'status' => 'draft',
            'subtotal' => $booking['subtotal'],
            'tax_amount' => $booking['tax_amount'],
            'total_amount' => $booking['total_amount'],
            'paid_amount' => 0,
            'outstanding_amount' => $booking['total_amount'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $invoiceId = (int)$this->db->insertID();

        foreach ($items as $item) {
            $this->db->table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'booking_item_id' => $item['id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_sale_price'],
                'tax_amount' => $item['tax_amount'],
                'line_total' => $item['line_total'],
                'revenue_account_id' => $item['revenue_account_id'],
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to create invoice.');
        }

        return $invoiceId;
    }

    /**
     * Post invoice:
     * DR Accounts Receivable
     * CR Revenue
     */
    public function post(int $invoiceId, ?int $userId = null): int
    {
        $invoice = $this->db->table('invoices')->where('id', $invoiceId)->get()->getRowArray();

        if (!$invoice) {
            throw new RuntimeException('Invoice not found.');
        }

        if (!empty($invoice['journal_entry_id'])) {
            return (int)$invoice['journal_entry_id'];
        }

        $ar = $this->accounting->accountId('1300');

        $items = $this->db->table('invoice_items')
            ->where('invoice_id', $invoiceId)
            ->get()->getResultArray();

        $lines = [[
            'account_id' => $ar,
            'customer_id' => $invoice['customer_id'],
            'booking_id' => $invoice['booking_id'],
            'description' => 'Accounts Receivable - ' . $invoice['invoice_no'],
            'debit' => $invoice['total_amount'],
            'credit' => 0,
        ]];

        foreach ($items as $item) {
            $accountId = $item['revenue_account_id']
                ? (int)$item['revenue_account_id']
                : $this->accounting->accountId('4100');

            $lines[] = [
                'account_id' => $accountId,
                'customer_id' => $invoice['customer_id'],
                'booking_id' => $invoice['booking_id'],
                'description' => $item['description'],
                'debit' => 0,
                'credit' => $item['line_total'],
            ];
        }

        $entryId = $this->accounting->createJournalEntry([
            'journal_id' => $this->journalId('SALES'),
            'entry_no' => $this->accounting->nextEntryNo('INV'),
            'entry_date' => $invoice['invoice_date'],
            'reference_type' => 'invoice',
            'reference_id' => $invoiceId,
            'description' => 'Invoice ' . $invoice['invoice_no'],
            'created_by' => $userId,
        ], $lines);

        $this->accounting->postJournalEntry($entryId, $userId);

        $this->db->table('invoices')->where('id', $invoiceId)->update([
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
