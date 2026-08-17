<?php
$m=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($m->connect_errno){echo 'CONERR '. $m->connect_error . "\n"; exit(1);} 
$res=$m->query("SELECT id,invoice_no,total_amount,paid_amount,outstanding_amount,booking_id,customer_id,status FROM invoices ORDER BY id DESC LIMIT 10");
if(!$res){echo 'ERR '. $m->error . "\n"; exit(1);} 
while($r=$res->fetch_assoc()){
    echo json_encode($r, JSON_UNESCAPED_UNICODE)."\n";
}
$m->close();
