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
     * Customer receipt:
     * DR Cash/Bank
     * CR Accounts Receivable
     */
    public function receiveCustomerPayment(array $data): int
    {
        $bookingId = (int)($data['booking_id'] ?? 0);
        $amount = (float)($data['amount'] ?? 0);

        if (!$bookingId || $amount <= 0) {
            throw new RuntimeException('booking_id and positive amount are required.');
        }

        $booking = $this->db->table('bookings')->where('id', $bookingId)->get()->getRowArray();

        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }

        $customerId = (int)$booking['customer_id'];
        $cashAccount = (int)($data['account_id'] ?? $this->accounting->accountId('1100'));
        $arAccount = $this->accounting->accountId('1300');

        $paymentNo = 'PAY-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));

        $this->db->transStart();

        $this->db->table('payments')->insert([
            'payment_no' => $paymentNo,
            'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
            'payment_type' => 'customer_receipt',
            'booking_id' => $bookingId,
            'customer_id' => $customerId,
            'supplier_id' => null,
            'account_id' => $cashAccount,
            'amount' => $amount,
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'reference_no' => $data['reference_no'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $paymentId = (int)$this->db->insertID();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to save payment.');
        }

        $entryId = $this->accounting->createJournalEntry([
            'journal_id' => $this->journalId('CASH'),
            'entry_no' => $this->accounting->nextEntryNo('PAY'),
            'entry_date' => $data['payment_date'] ?? date('Y-m-d'),
            'reference_type' => 'payment',
            'reference_id' => $paymentId,
            'description' => 'Customer payment ' . $paymentNo,
            'created_by' => $data['created_by'] ?? null,
        ], [
            [
                'account_id' => $cashAccount,
                'customer_id' => $customerId,
                'booking_id' => $bookingId,
                'description' => 'Customer receipt',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_id' => $arAccount,
                'customer_id' => $customerId,
                'booking_id' => $bookingId,
                'description' => 'Reduce receivable',
                'debit' => 0,
                'credit' => $amount,
            ],
        ]);

        $this->accounting->postJournalEntry($entryId, $data['created_by'] ?? null);

        $this->db->table('payments')->where('id', $paymentId)->update([
            'journal_entry_id' => $entryId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->recalculateBookingPayment($bookingId);

        return $paymentId;
    }

    protected function recalculateBookingPayment(int $bookingId): void
    {
        $booking = $this->db->table('bookings')->where('id', $bookingId)->get()->getRowArray();

        if (!$booking) {
            return;
        }

        $paid = (float)($this->db->table('payments')
            ->selectSum('amount', 'total')
            ->where('booking_id', $bookingId)
            ->where('payment_type', 'customer_receipt')
            ->get()->getRow()->total ?? 0);

        $outstanding = max(0, (float)$booking['total_amount'] - $paid);

        $this->db->table('bookings')->where('id', $bookingId)->update([
            'paid_amount' => $paid,
            'outstanding_amount' => $outstanding,
            'status' => $outstanding <= 0 ? 'paid' : ($paid > 0 ? 'partial_paid' : $booking['status']),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('invoices')
            ->where('booking_id', $bookingId)
            ->update([
                'paid_amount' => $paid,
                'outstanding_amount' => $outstanding,
                'status' => $outstanding <= 0 ? 'paid' : ($paid > 0 ? 'partial_paid' : 'posted'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
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
