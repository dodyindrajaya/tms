<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class PaymentService
{
    protected BaseConnection $db;
    protected AccountingService $accounting;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->accounting = new AccountingService($this->db);
    }

    /**
     * Receive customer payment against the booking's posted invoice.
     * DR Cash/Bank (payment method clearing account)
     * CR Accounts Receivable
     */
    public function receiveCustomerPayment(array $data): int
    {
        $invoiceId = (int) ($data['invoice_id'] ?? 0);
        $amount = round((float) ($data['amount'] ?? 0), 2);
        if ($invoiceId <= 0 || $amount <= 0) {
            throw new RuntimeException('Invoice and a positive payment amount are required.');
        }

        $invoice = $this->db->table('invoices')->where('id', $invoiceId)->get()->getRowArray();
        if (!$invoice) {
            throw new RuntimeException('Invoice not found.');
        }
        if (!in_array($invoice['status'], ['posted', 'partial', 'overdue'], true)) {
            throw new RuntimeException('Invoice must be posted before receiving payment.');
        }

        $outstanding = max(0, (float) $invoice['total_amount'] - (float) $invoice['paid_amount']);
        if ($outstanding <= 0) {
            throw new RuntimeException('This invoice has no outstanding balance.');
        }
        if ($amount > $outstanding + 0.005) {
            throw new RuntimeException('Payment exceeds invoice outstanding balance of ' . number_format($outstanding, 2) . '.');
        }

        $methodId = (int) ($data['payment_method_id'] ?? 0);
        if ($methodId <= 0) {
            throw new RuntimeException('Please select a valid payment method.');
        }

        $method = $this->db->table('payment_methods')
            ->where('id', $methodId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$method) {
            throw new RuntimeException('Please select a valid active payment method.');
        }

        if (empty($method['clearing_account_id'])) {
            throw new RuntimeException('Selected payment method does not have a configured clearing account. Please configure the payment method account first.');
        }

        $cashBankAccount = (int) $method['clearing_account_id'];
        $arAccount = $this->accounting->accountId('1300');
        $paymentDate = !empty($data['payment_date']) ? date('Y-m-d H:i:s', strtotime($data['payment_date'])) : date('Y-m-d H:i:s');
        $createdBy = !empty($data['created_by']) ? (int) $data['created_by'] : (int) (session('user_id') ?: 1);
        $paymentNo = 'PAY-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $bookingId = (int) $invoice['booking_id'];
        $customerId = (int) $invoice['customer_id'];

        $this->db->transStart();
        $this->db->table('payments')->insert([
            'payment_no' => $paymentNo,
            'payment_date' => $paymentDate,
            'payment_type' => 'customer_receipt',
            'booking_id' => $bookingId,
            'customer_id' => $customerId,
            'supplier_id' => null,
            'account_id' => $cashBankAccount,
            'amount' => $amount,
            'payment_method_id' => $methodId,
            'reference_no' => trim((string) ($data['reference_no'] ?? '')) ?: null,
            'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
            'journal_entry_id' => null,
            'created_by' => $createdBy,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentId = (int) $this->db->insertID();
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to save payment.');
        }

        try {
            $entryId = $this->accounting->createJournalEntry([
                'journal_id' => $this->journalIdForMethod($method['method_type']),
                'entry_no' => $this->accounting->nextEntryNo('PAY'),
                'entry_date' => substr($paymentDate, 0, 10),
                'reference_type' => 'payment',
                'reference_id' => $paymentId,
                'description' => 'Customer receipt ' . $paymentNo,
                'created_by' => $createdBy,
            ], [
                [
                    'account_id' => $cashBankAccount,
                    'customer_id' => $customerId,
                    'booking_id' => $bookingId,
                    'description' => $method['name'] . ' - customer receipt',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_id' => $arAccount,
                    'customer_id' => $customerId,
                    'booking_id' => $bookingId,
                    'description' => 'Reduce receivable - ' . $invoice['invoice_no'],
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ]);

            $this->accounting->postJournalEntry($entryId, $createdBy);
            $this->db->table('payments')->where('id', $paymentId)->update([
                'journal_entry_id' => $entryId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $this->recalculateInvoiceAndBooking($invoiceId, $bookingId);
        } catch (\Throwable $e) {
            // Remove the unposted payment if journal creation fails, so a failed receipt cannot remain orphaned.
            $this->db->table('payments')->where('id', $paymentId)->delete();
            throw $e;
        }

        return $paymentId;
    }

    protected function recalculateInvoiceAndBooking(int $invoiceId, int $bookingId): void
    {
        $invoice = $this->db->table('invoices')->where('id', $invoiceId)->get()->getRowArray();
        if (!$invoice) return;

        $paid = (float) ($this->db->table('payments')
            ->selectSum('amount', 'total')
            ->where('booking_id', $bookingId)
            ->where('payment_type', 'customer_receipt')
            ->get()->getRow()->total ?? 0);

        $outstanding = max(0, (float) $invoice['total_amount'] - $paid);
        $status = $outstanding <= 0.005 ? 'paid' : ($paid > 0 ? 'partial' : 'posted');
        if ($outstanding > 0 && !empty($invoice['due_date']) && $invoice['due_date'] < date('Y-m-d')) {
            $status = $paid > 0 ? 'partial' : 'overdue';
        }

        $this->db->table('invoices')->where('id', $invoiceId)->update([
            'paid_amount' => min((float) $invoice['total_amount'], $paid),
            'outstanding_amount' => $outstanding,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $booking = $this->db->table('bookings')->where('id', $bookingId)->get()->getRowArray();
        if ($booking) {
            $bookingStatus = $outstanding <= 0.005 ? 'paid' : ($paid > 0 ? 'partial_paid' : $booking['status']);
            $this->db->table('bookings')->where('id', $bookingId)->update([
                'paid_amount' => min((float) $booking['total_amount'], $paid),
                'outstanding_amount' => max(0, (float) $booking['total_amount'] - $paid),
                'status' => $bookingStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    protected function journalIdForMethod(string $methodType): int
    {
        $code = in_array($methodType, ['bank', 'transfer', 'card'], true) ? 'BANK' : 'CASH';
        $row = $this->db->table('journals')->where('code', $code)->where('is_active', 1)->get()->getRow();
        if (!$row) {
            throw new RuntimeException("Journal {$code} not found or inactive.");
        }
        return (int) $row->id;
    }
}
