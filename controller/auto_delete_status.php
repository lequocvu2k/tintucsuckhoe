<?php
require '../php/db.php';
$pdo->query("DELETE FROM status WHERE ngay_dang < NOW() - INTERVAL 1 DAY");
echo "OK - deleted old status";
