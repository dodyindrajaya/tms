<?php
$mysqli=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($mysqli->connect_errno){ echo 'connect_error'; exit(1); }
$q = "SELECT ticket_bookings.*, bookings.booking_no, passengers.full_name AS passenger_name FROM ticket_bookings LEFT JOIN bookings ON bookings.id=ticket_bookings.booking_id LEFT JOIN passengers ON passengers.id=ticket_bookings.passenger_id ORDER BY ticket_bookings.id DESC LIMIT 5";
$res=$mysqli->query($q);
if(!$res){ echo 'ERR: '.$mysqli->error; exit(1);} 
while($r=$res->fetch_assoc()){ print_r($r); }
$mysqli->close();
