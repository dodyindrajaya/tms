<?php
$host='127.0.0.1';
$user='root';
$pass='123qweasd';
$db='tms';
$port=3309;
$mysqli = new mysqli($host,$user,$pass,$db,$port);
if ($mysqli->connect_errno) {
    echo "CONNECT_ERROR: " . $mysqli->connect_error . "\n";
    exit(1);
}

$res = $mysqli->query("SELECT i.id,i.invoice_no,i.outstanding_amount,i.booking_id,i.customer_id FROM invoices i WHERE i.outstanding_amount>0 AND i.status!='cancelled' ORDER BY i.id DESC LIMIT 10");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "QUERY_ERROR invoices: " . $mysqli->error . "\n";
}
$mysqli->close();
