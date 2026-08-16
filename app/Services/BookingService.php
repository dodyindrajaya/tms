<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class BookingService
{
    protected BaseConnection $db;
    protected AccountingService $accounting;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->accounting = new AccountingService($this->db);
    }

    /**
     * Create a booking and its items in one transaction.
     *
     * Expected:
     * [
     *   customer_id,
     *   booking_date,
     *   travel_start_date,
     *   travel_end_date,
     *   source,
     *   currency_code,
     *   notes,
     *   created_by,
     *   items => [
     *      [
     *        product_id, description, quantity,
     *        unit_sale_price, discount_amount, tax_amount,
     *        cost_amount
     *      ]
     *   ],
     *   passenger_ids => [1,2,3]
     * ]
     */
    public function create(array $data): int
    {
        if (empty($data['customer_id'])) {
            throw new RuntimeException('customer_id is required.');
        }

        if (empty($data['items'])) {
            throw new RuntimeException('At least one booking item is required.');
        }

        $items = $data['items'];
        $subtotal = 0;
        $discount = 0;
        $tax = 0;
        $total = 0;

        foreach ($items as &$item) {
            $qty = (float)($item['quantity'] ?? 1);
            $price = (float)($item['unit_sale_price'] ?? 0);
            $disc = (float)($item['discount_amount'] ?? 0);
            $itemTax = (float)($item['tax_amount'] ?? 0);

            if ($qty <= 0 || $price < 0) {
                throw new RuntimeException('Invalid booking item quantity/price.');
            }

            $lineTotal = ($qty * $price) - $disc + $itemTax;

            if ($lineTotal < 0) {
                throw new RuntimeException('Booking item total cannot be negative.');
            }

            $item['_line_total'] = round($lineTotal, 2);

            $subtotal += $qty * $price;
            $discount += $disc;
            $tax += $itemTax;
            $total += $lineTotal;
        }
        unset($item);

        $bookingNo = $this->nextBookingNo();

        $this->db->transStart();

        $this->db->table('bookings')->insert([
            'booking_no'        => $bookingNo,
            'customer_id'       => $data['customer_id'],
            'booking_date'      => $data['booking_date'] ?? date('Y-m-d'),
            'travel_start_date' => $data['travel_start_date'] ?? null,
            'travel_end_date'   => $data['travel_end_date'] ?? null,
            'source'            => $data['source'] ?? 'office',
            'status'            => $data['status'] ?? 'draft',
            'currency_code'     => $data['currency_code'] ?? 'IDR',
            'subtotal'          => $subtotal,
            'discount_amount'   => $discount,
            'tax_amount'        => $tax,
            'total_amount'      => $total,
            'paid_amount'       => 0,
            'outstanding_amount'=> $total,
            'notes'             => $data['notes'] ?? null,
            'created_by'        => $data['created_by'] ?? null,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        $bookingId = (int)$this->db->insertID();

        foreach ($items as $item) {
            $this->db->table('booking_items')->insert([
                'booking_id'        => $bookingId,
                'product_id'        => $item['product_id'],
                'description'       => $item['description'] ?? '',
                'quantity'          => $item['quantity'] ?? 1,
                'unit_sale_price'   => $item['unit_sale_price'] ?? 0,
                'discount_amount'   => $item['discount_amount'] ?? 0,
                'tax_amount'        => $item['tax_amount'] ?? 0,
                'line_total'        => $item['_line_total'],
                'revenue_account_id'=> $item['revenue_account_id'] ?? null,
                'cost_account_id'   => $item['cost_account_id'] ?? null,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        if (!empty($data['passenger_ids'])) {
            foreach ($data['passenger_ids'] as $i => $passengerId) {
                $this->db->table('booking_passengers')->insert([
                    'booking_id'    => $bookingId,
                    'passenger_id'  => $passengerId,
                    'passenger_type'=> $data['passenger_type'] ?? 'adult',
                    'is_primary'    => $i === 0 ? 1 : 0,
                ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Failed to create booking.');
        }

        return $bookingId;
    }

    public function recalculate(int $bookingId): array
    {
        $items = $this->db->table('booking_items')
            ->where('booking_id', $bookingId)
            ->get()
            ->getResultArray();

        $subtotal = 0;
        $discount = 0;
        $tax = 0;
        $total = 0;

        foreach ($items as $item) {
            $subtotal += (float)$item['quantity'] * (float)$item['unit_sale_price'];
            $discount += (float)$item['discount_amount'];
            $tax += (float)$item['tax_amount'];
            $total += (float)$item['line_total'];
        }

        $paid = (float)($this->db->table('payments')
            ->selectSum('amount', 'total')
            ->where('booking_id', $bookingId)
            ->where('payment_type', 'customer_receipt')
            ->get()->getRow()->total ?? 0);

        $outstanding = max(0, $total - $paid);

        $this->db->table('bookings')->where('id', $bookingId)->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'outstanding_amount' => $outstanding,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'outstanding_amount' => $outstanding,
        ];
    }

    protected function nextBookingNo(): string
    {
        return 'BK-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }
}
