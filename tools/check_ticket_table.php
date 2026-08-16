<?php
$mysqli = new mysqli('127.0.0.1','root','123qweasd','tms',3309);
if ($mysqli->connect_errno) { echo 'connect_error:'.$mysqli->connect_error; exit(1); }
$res = $mysqli->query("SHOW TABLES LIKE 'ticket_bookings'");
if ($res && $res->num_rows) {
    $c = $mysqli->query("SHOW COLUMNS FROM ticket_bookings");
    while ($row = $c->fetch_assoc()) {
        echo $row['Field'] . "\t" . $row['Type'] . PHP_EOL;
    }
} else {
    echo 'MISSING';
}
$mysqli->close();
