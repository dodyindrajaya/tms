<?php
$mysqli=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($mysqli->connect_errno){ echo 'connect_error'; exit(1); }
$res=$mysqli->query('SELECT id,booking_id,passenger_id,ticket_number,booking_code FROM ticket_bookings ORDER BY id DESC LIMIT 20');
if(!$res){ echo 'ERR: '.$mysqli->error; exit(1); }
while($r=$res->fetch_assoc()){ echo implode('\t',[$r['id'],$r['booking_id'],$r['passenger_id'],$r['ticket_number'],$r['booking_code']])."\n"; }
$mysqli->close();
