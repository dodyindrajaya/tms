<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        // Counts
        $totalBookings = (int)$db->table('bookings')->countAllResults();
        $totalCustomers = (int)$db->table('customers')->countAllResults();

        // Invoices summary
        $invoiceRow = $db->table('invoices')
            ->select('COUNT(*) AS cnt, COALESCE(SUM(total_amount),0) AS total')
            ->get()->getRowArray();
        $totalInvoicesCount = (int)($invoiceRow['cnt'] ?? 0);
        $totalInvoiced = (float)($invoiceRow['total'] ?? 0);

        // Payments / revenue
        $paymentRow = $db->table('payments')
            ->select('COALESCE(SUM(amount),0) AS total')
            ->get()->getRowArray();
        $totalPaid = (float)($paymentRow['total'] ?? 0);

        // Prefer outstanding based on bookings' outstanding_amount
        $bookingRow = $db->table('bookings')
            ->select('COALESCE(SUM(outstanding_amount),0) AS total')
            ->get()->getRowArray();
        $outstanding = (float)($bookingRow['total'] ?? 0);

        // Monthly revenue (last 6 months) - best effort SQL, may require DB date field `payment_date`
        $months = [];
        $revenueData = [];
        $rows = $db->query("SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, COALESCE(SUM(amount),0) AS total FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY month ORDER BY month ASC")->getResultArray();
        foreach ($rows as $r) {
            $months[] = $r['month'];
            $revenueData[] = (float)$r['total'];
        }

        return view('dashboard', [
            'totalBookings' => $totalBookings,
            'totalCustomers' => $totalCustomers,
            'totalInvoicesCount' => $totalInvoicesCount,
            'totalInvoiced' => $totalInvoiced,
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'chartMonths' => $months,
            'chartRevenue' => $revenueData,
        ]);
    }
}
