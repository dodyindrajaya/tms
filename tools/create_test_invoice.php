<?php
$m=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($m->connect_errno){echo 'CONERR '. $m->connect_error . "\n"; exit(1);} 
$invoiceNo = 'INV-TEST-'.date('YmdHis');
$bookingId = 16;
$customerId = 2;
$total = 100.00;
$paid = 0.00;
$outstanding = $total - $paid;
$now = date('Y-m-d H:i:s');
$invDate = date('Y-m-d');
$due = date('Y-m-d', strtotime('+7 days'));
$status = 'posted';

$sql = sprintf(
	"INSERT INTO invoices (invoice_no, invoice_date, due_date, booking_id, customer_id, total_amount, paid_amount, outstanding_amount, status, created_at, updated_at) VALUES ('%s','%s','%s',%d,%d,%.2f,%.2f,%.2f,'%s','%s','%s')",
	$m->real_escape_string($invoiceNo),
	$m->real_escape_string($invDate),
	$m->real_escape_string($due),
	(int)$bookingId,
	(int)$customerId,
	$total,
	$paid,
	$outstanding,
	$m->real_escape_string($status),
	$m->real_escape_string($now),
	$m->real_escape_string($now)
);

if (!$m->query($sql)) {
	echo 'ERR '. $m->error . "\n";
	exit(1);
}

$id = $m->insert_id;
echo json_encode(['id'=>$id,'invoice_no'=>$invoiceNo,'outstanding'=>$outstanding]) . "\n";
$m->close();
