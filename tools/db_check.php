<?php
$host = '127.0.0.1';
$port = 3309;
$user = 'root';
$pass = '123qweasd';
$db = 'tms';

$mysqli = @new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Connect error: ' . $mysqli->connect_error]);
    exit(1);
}
$mysqli->set_charset('utf8mb4');

function run($sql) {
    global $mysqli;
    $res = $mysqli->query($sql);
    if ($res === false) return ['error' => $mysqli->error, 'sql' => $sql];
    $row = $res->fetch_assoc();
    return $row ?: [];
}

$out = [];
// Counts
$tb = run("SELECT COUNT(*) AS cnt FROM bookings");
$out['totalBookings'] = isset($tb['cnt']) ? (int)$tb['cnt'] : null;
$tc = run("SELECT COUNT(*) AS cnt FROM customers");
$out['totalCustomers'] = isset($tc['cnt']) ? (int)$tc['cnt'] : null;

// Invoices summary
$inv = run("SELECT COUNT(*) AS cnt, COALESCE(SUM(total_amount),0) AS total FROM invoices");
$out['totalInvoicesCount'] = isset($inv['cnt']) ? (int)$inv['cnt'] : null;
$out['totalInvoiced'] = isset($inv['total']) ? (float)$inv['total'] : null;

// Payments / revenue
$pay = run("SELECT COALESCE(SUM(amount),0) AS total FROM payments");
$out['totalPaid'] = isset($pay['total']) ? (float)$pay['total'] : null;

// Use bookings outstanding_amount as authoritative outstanding
$ob = run("SELECT COALESCE(SUM(outstanding_amount),0) AS total FROM bookings");
$out['outstanding'] = isset($ob['total']) ? (float)$ob['total'] : null;

// Monthly revenue (last 6 months)
$rows = $mysqli->query("SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, COALESCE(SUM(amount),0) AS total FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY month ORDER BY month ASC");
if ($rows === false) {
    $out['monthly_error'] = $mysqli->error;
    $out['chartMonths'] = [];
    $out['chartRevenue'] = [];
} else {
    $months = [];
    $revenue = [];
    while ($r = $rows->fetch_assoc()) {
        $months[] = $r['month'];
        $revenue[] = (float)$r['total'];
    }
    $out['chartMonths'] = $months;
    $out['chartRevenue'] = $revenue;
}

// Sample schema checks (first row from each table)
$sample = [];
$tables = ['bookings','invoices','payments','customers'];
foreach ($tables as $t) {
    $res = $mysqli->query("SELECT * FROM `".$t."` LIMIT 1");
    if ($res === false) {
        $sample[$t] = ['error' => $mysqli->error];
    } else {
        $sample[$t] = $res->fetch_assoc() ?: [];
    }
}
$out['sampleRow'] = $sample;

echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

$mysqli->close();
