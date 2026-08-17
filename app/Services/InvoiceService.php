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
            return (int) $existing->id;
        }

        $items = $this->db->table('booking_items')->where('booking_id', $bookingId)->orderBy('id', 'ASC')->get()->getResultArray();
        if (!$items) {
            throw new RuntimeException('Booking has no commercial items.');
        }

        foreach ($items as $item) {
            if ((float) $item['line_total'] < 0) {
                throw new RuntimeException('Booking contains an invalid negative line total.');
            }
        }

        $invoiceNo = 'INV-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $now = date('Y-m-d H:i:s');

        $this->db->transStart();
        $this->db->table('invoices')->insert([
            'invoice_no' => $invoiceNo,
            'booking_id' => $bookingId,
            'customer_id' => $booking['customer_id'],
            'invoice_date' => date('Y-m-d'),
            'due_date' => $dueDate ?: date('Y-m-d'),
            'status' => 'draft',
            'subtotal' => $booking['subtotal'],
            'tax_amount' => $booking['tax_amount'],
            'total_amount' => $booking['total_amount'],
            'paid_amount' => 0,
            'outstanding_amount' => $booking['total_amount'],
            'journal_entry_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $invoiceId = (int) $this->db->insertID();

        foreach ($items as $item) {
            $this->db->table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'booking_item_id' => $item['id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_sale_price'],
                'tax_amount' => $item['tax_amount'],
                'line_total' => $item['line_total'],
                'revenue_account_id' => $item['revenue_account_id'] ?: $this->accounting->accountId('4100'),
            ]);
        }

        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to create invoice.');
        }

        return $invoiceId;
    }

    public function post(int $invoiceId, ?int $userId = null): int
    {
        $invoice = $this->db->table('invoices')->where('id', $invoiceId)->get()->getRowArray();
        if (!$invoice) {
            throw new RuntimeException('Invoice not found.');
        }
        if ($invoice['status'] === 'cancelled') {
            throw new RuntimeException('Cancelled invoice cannot be posted.');
        }
        if (!empty($invoice['journal_entry_id'])) {
            return (int) $invoice['journal_entry_id'];
        }

        $items = $this->db->table('invoice_items')->where('invoice_id', $invoiceId)->get()->getResultArray();
        if (!$items) {
            throw new RuntimeException('Invoice has no lines.');
        }

        $ar = $this->accounting->accountId('1300');
        $lines = [[
            'account_id' => $ar,
            'customer_id' => $invoice['customer_id'],
            'booking_id' => $invoice['booking_id'],
            'description' => 'Accounts Receivable - ' . $invoice['invoice_no'],
            'debit' => (float) $invoice['total_amount'],
            'credit' => 0,
        ]];

        $revenueTotals = [];
        $taxTotal = 0.0;
        foreach ($items as $item) {
            $revenueAccountId = (int) ($item['revenue_account_id'] ?: $this->accounting->accountId('4100'));
            $tax = (float) $item['tax_amount'];
            $net = max(0, (float) $item['line_total'] - $tax);
            $revenueTotals[$revenueAccountId] = ($revenueTotals[$revenueAccountId] ?? 0) + $net;
            $taxTotal += $tax;
        }

        foreach ($revenueTotals as $accountId => $amount) {
            if ($amount <= 0) continue;
            $lines[] = [
                'account_id' => $accountId,
                'customer_id' => $invoice['customer_id'],
                'booking_id' => $invoice['booking_id'],
                'description' => 'Revenue - ' . $invoice['invoice_no'],
                'debit' => 0,
                'credit' => round($amount, 2),
            ];
        }

        if ($taxTotal > 0) {
            // 2200 is the dedicated tax payable account created by the finance seeder.
            $taxAccount = $this->accounting->accountId('2200');
            $lines[] = [
                'account_id' => $taxAccount,
                'customer_id' => $invoice['customer_id'],
                'booking_id' => $invoice['booking_id'],
                'description' => 'Output Tax - ' . $invoice['invoice_no'],
                'debit' => 0,
                'credit' => round($taxTotal, 2),
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
        $row = $this->db->table('journals')->where('code', $code)->where('is_active', 1)->get()->getRow();
        if (!$row) {
            throw new RuntimeException("Journal {$code} not found or inactive.");
        }
        return (int) $row->id;
    }
}
