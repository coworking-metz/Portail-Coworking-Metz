<?php

/**
 * coworkers_now_tv();
 * Get the number of people connected
 */

function coworkers_now_tv() {

    $hour_now = date('H');
    $hour_now += 1;
    $start_hour = 11;
    $end_hour = 15;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = TICKET_URL.'/coworkersNow?delay=180';
    } else {
        $curl_url = TICKET_URL.'/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $number_coworkers = curl_exec($ch);
    $number_worplaces = 28;
    $remaining_workplaces = $number_worplaces - $number_coworkers;

    if ($number_coworkers == 0) {
        echo 'Pas de coworker !';
    } elseif ($number_coworkers == 1) {
        echo '<span class="highlight-text">' . $number_coworkers . ' </span>coworker actuellement !';
    } else {
        echo 'Nous sommes <span class="highlight-text">' . $number_coworkers . '</span> actuellement !';
    }
}
/**
 * coworkers_now();
 * Get the number of people connected
 */
function coworkers_now($return = false)
{

    $hour_now = date('H');
    $hour_now += 1;
    $start_hour = 11;
    $end_hour = 15;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = TICKET_URL.'/coworkersNow?delay=180';
    } else {
        $curl_url = TICKET_URL.'/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $number_coworkers = curl_exec($ch);
    $number_worplaces = 28;
    $remaining_workplaces = $number_worplaces - $number_coworkers;

    $output = '';
    if ($number_coworkers == 0) {
        $output = 'Pas de coworker actuellement. <span class="highlight-text">';
    } elseif ($number_coworkers == 1) {
        $output = 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworker présent.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
    } else {
        $output = 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworkers présents.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}
/**
 * coworkers_now_app();
 * Get the number of people connected 
 */
function coworkers_now_app()
{

    $hour_now = date('H');
    $hour_now += 1;
    $start_hour = 11;
    $end_hour = 15;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = TICKET_URL.'/coworkersNow?delay=180';
    } else {
        $curl_url = TICKET_URL.'/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    echo curl_exec($ch);
}
/**
 * coworkers_now_app();
 * Get the number of people connected 
 */
function remaining_workplaces_app()
{

    $hour_now = date('H');
    $hour_now += 1;
    $start_hour = 11;
    $end_hour = 15;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = TICKET_URL.'/coworkersNow?delay=180';
    } else {
        $curl_url = TICKET_URL.'/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $number_coworkers = curl_exec($ch);
    $number_worplaces = 28;
    $remaining_workplaces = $number_worplaces - $number_coworkers;

    echo $remaining_workplaces;
}
