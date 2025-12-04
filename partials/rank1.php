<?php

function getRankClassFromXP($xp) {

    $level = floor($xp / 100);

    if ($level >= 100) return 'rank-mythic';
    if ($level >= 60) return 'rank-diamond';
    if ($level >= 40) return 'rank-gold';
    if ($level >= 20) return 'rank-silver';
    if ($level >= 10) return 'rank-bronze';

    return 'rank-normal';
}
