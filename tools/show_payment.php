<?php
$m=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($m->connect_errno){echo 'CONERR '. $m->connect_error . "\n"; exit(1);} 
$res=$m->query("SELECT * FROM payments WHERE id=2");
if(!$res){echo 'ERR '. $m->error . "\n"; exit(1);} 
$r=$res->fetch_assoc();
echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
$m->close();
