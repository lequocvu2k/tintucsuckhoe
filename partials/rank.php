<?php 

// Nếu không có dữ liệu XP thì rank mặc định
if (!isset($xp)) {
    $nameClass = 'rank-normal';
    return;
}

$level = floor($xp / 100);

// Gán class rank theo level
if ($level >= 100) {
    $nameClass = 'rank-mythic';
} elseif ($level >= 60) {
    $nameClass = 'rank-diamond';
} elseif ($level >= 40) {
    $nameClass = 'rank-gold';
} elseif ($level >= 20) {
    $nameClass = 'rank-silver';
} elseif ($level >= 10) {
    $nameClass = 'rank-bronze';
} else {
    $nameClass = 'rank-normal';
}
