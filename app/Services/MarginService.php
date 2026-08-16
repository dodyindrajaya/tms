<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class MarginService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function bookingMargin(int $bookingId): array
    {
        $booking = $this->db->table('bookings')
            ->where('id', $bookingId)
            ->get()->getRowArray();

        if (!$booking) {
            return [];
        }

        $cost = (float)($this->db->table('booking_costs')
            ->selectSum('amount', 'total')
            ->where('booking_id', $bookingId)
            ->get()->getRow()->total ?? 0);

        $revenue = (float)$booking['total_amount'];
        $margin = $revenue - $cost;
        $percentage = $revenue > 0 ? ($margin / $revenue) * 100 : 0;

        return [
            'booking_id' => $bookingId,
            'revenue' => round($revenue, 2),
            'cost' => round($cost, 2),
            'margin' => round($margin, 2),
            'margin_percentage' => round($percentage, 2),
        ];
    }
}
