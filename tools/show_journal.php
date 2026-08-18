<?php
$m=new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if($m->connect_errno){echo 'CONERR '. $m->connect_error . "\n"; exit(1);} 
$paymentId = 2;
$res=$m->query("SELECT * FROM journal_entries WHERE reference_type='payment' AND reference_id=".(int)$paymentId);
if(!$res){echo 'ERR '. $m->error . "\n"; exit(1);} 
$je = $res->fetch_assoc();
if(!$je){ echo "No journal entry found for payment {$paymentId}\n"; $m->close(); exit(0); }
echo "Journal Entry:\n".json_encode($je, JSON_UNESCAPED_UNICODE)."\n\n";
$res2 = $m->query("SELECT * FROM journal_entry_lines WHERE journal_entry_id=".(int)$je['id']." ORDER BY id ASC");
if(!$res2){ echo 'ERR2 '. $m->error . "\n"; $m->close(); exit(1);} 
$lines = [];
while($r=$res2->fetch_assoc()) $lines[] = $r;
echo "Lines:\n".json_encode($lines, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
$m->close();
