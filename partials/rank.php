<?php
if (!isset($user))
    return; // đảm bảo có user

$xp = isset($user['xp']) ? (int) $user['xp'] : 0;
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
