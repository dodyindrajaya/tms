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

echo "-- payment_methods --\n";
$res = $mysqli->query("SELECT id,name,clearing_account_id,is_active FROM payment_methods ORDER BY id ASC LIMIT 100");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "QUERY_ERROR payment_methods: " . $mysqli->error . "\n";
}

echo "\n-- recent payments --\n";
$res = $mysqli->query("SELECT id,payment_no,payment_date,booking_id,customer_id,account_id,payment_method_id,amount,reference_no,created_by,created_at FROM payments ORDER BY id DESC LIMIT 20");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "QUERY_ERROR payments: " . $mysqli->error . "\n";
}

$mysqli->close();
