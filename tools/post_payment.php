<?php
$url = 'http://localhost:8081/index.php/payments/store';
$data = [
    'invoice_id' => 2,
    'payment_method_id' => 1,
    'payment_date' => date('Y-m-d H:i'),
    'amount' => '100.00',
    'reference_no' => 'test-invoice-2',
    'notes' => 'test via script',
];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$res = curl_exec($ch);
if ($res === false) {
    echo 'CURL_ERR: '.curl_error($ch)."\n";
} else {
    $info = curl_getinfo($ch);
    echo "HTTP/".$info['http_version']." " . $info['http_code'] . "\n";
    echo $res;
}
curl_close($ch);
