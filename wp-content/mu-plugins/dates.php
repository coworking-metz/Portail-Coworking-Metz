<?php


function date_francais($timestamp)
{
    if (!is_numeric($timestamp)) $timestamp = strtotime($timestamp);
    if (!$timestamp) return null;
    setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr', 'fr');
    $french_date = strftime("%d %B %Y à %H:%M", $timestamp);
    $y = date('Y');

    $french_date = str_replace(' '.$y,'',$french_date);

    return $french_date;
}
