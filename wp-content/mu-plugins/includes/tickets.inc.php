<?php


function isAboEnCours($date)
{
    $dateAbo = strtotime($date);
    if ($dateAbo > time()) return true;
}


function isOkMembership($date)
{

    $currentYear = date('Y');
    $nextYear = date('Y', strtotime('+1 year'));
    if ($date == $currentYear || $date == $nextYear) return true;
}
