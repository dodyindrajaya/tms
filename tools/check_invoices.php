<?php
$m=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($m->connect_errno){echo 'CONERR '. $m->connect_error . "\n"; exit(1);} 
$res=$m->query("SHOW TABLES LIKE 'invoices'");
if(!$res){echo 'ERR '. $m->error . "\n";}
else if($res->num_rows){echo 'HAS_INVOICES\n';} else {echo 'NO_INVOICES\n';}
$m->close();
