<?php
$mysqli=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($mysqli->connect_errno){ echo 'connect_error'; exit(1); }
$res=$mysqli->query('SHOW COLUMNS FROM passengers');
if(!$res){ echo 'MISSING'; exit; }
while($r=$res->fetch_assoc()){ echo $r['Field']."\t".$r['Type']."\n"; }
$mysqli->close();
