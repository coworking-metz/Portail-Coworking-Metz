<?php
// 24 - Display number tv
(function () {

  

        $hour_now = date('H');
        $hour_now += 1;
        $start_hour = 11;
        $end_hour = 15;

        if ($hour_now >= $start_hour && $hour_now < $end_hour) {
            $curl_url = TICKET_URL . '/coworkersNow?delay=180';
        } else {
            $curl_url = TICKET_URL . '/coworkersNow?delay=15';
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
    
})();
