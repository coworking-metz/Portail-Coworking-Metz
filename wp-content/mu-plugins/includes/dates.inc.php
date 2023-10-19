<?php
function fetch_holidays() {
    // Vérification du transient
    if ( false === ($holidays = get_transient('dates-vacances-zone-b')) ) {
        $url = 'https://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-B.ics';
        $response = wp_remote_get($url);

        // Vérification de l'état de la réponse
        if ( is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200 ) {
            return [];
        }

        $ics_data = wp_remote_retrieve_body($response);
        preg_match_all('/DTSTART;VALUE=DATE:(\d+)\r\nDTEND;VALUE=DATE:(\d+)/', $ics_data, $matches);
        $holidays = [];
        $today = new DateTime();
        $six_months_later = (clone $today)->modify('+6 months');

        foreach($matches[1] as $index => $start) {
            $end_date = DateTime::createFromFormat('Ymd', $matches[2][$index]);
            
            // Filtrage des dates passées et au-delà de 6 mois
            if ($end_date >= $today && $end_date <= $six_months_later) {
                $formatted_start = DateTime::createFromFormat('Ymd', $start)->format('d/m/Y');
                $formatted_end = $end_date->format('d/m/Y');
                $holidays[] = "$formatted_start > $formatted_end";
            }
        }

        // Sauvegarde du résultat dans un transient pendant un mois
        set_transient('dates-vacances-zone-b', $holidays, 30 * DAY_IN_SECONDS);
    }

    return $holidays;
}






/**
 * Vérifie si la date est dans le passé
 *
 * @param string $date Date au format 'Y-m-d'
 * @return bool
 */
function isPast($date) {
    $currentDate = date('Y-m-d');
    return ($date < $currentDate);
}


/**
 * Vérifie si la date est dans le futur
 *
 * @param string $date Date au format 'Y-m-d'
 * @return bool
 */
function isFuture($date) {
    $currentDate = date('Y-m-d');
    return ($date > $currentDate);
}
/**
 * Vérifie si la date correspond à aujourd'hui
 *
 * @param string $date Date au format 'Y-m-d'
 * @return bool
 */
function isToday($date) {
    $currentDate = date('Y-m-d');
    return ($date === $currentDate);
}

function date_maline($t)
{

    $date = new DateTime($t, new DateTimeZone('Europe/Paris'));

    // Crée l'objet IntlDateFormatter
    $formatter = new IntlDateFormatter(
        'fr_FR',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Europe/Paris',
        IntlDateFormatter::GREGORIAN
    );
    $formatter->setPattern("yyyy-MM-dd HH:mm");
    return $formatter->format($date);
}
function date_francais($timestamp, $heure = false)
{
    try {
        if (!$timestamp) return;
        $date = new DateTime($timestamp, new DateTimeZone('Europe/Paris'));

        // Crée l'objet IntlDateFormatter
        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN
        );
        if ($heure) {
            $formatter->setPattern("EEEE d MMMM y à H:mm");
        } else {
            $formatter->setPattern("EEEE d MMMM y");
        }


        // Affiche la date formatée en français
        $ret = $formatter->format($date);

        $ret = str_replace(' ' . date('Y') . ' ', ' ', $ret);
        return $ret;
    } catch (Exception $e) {
        return $timestamp;
    }

/*
    $french_date = strftime('%A %d %B %Y %H:%M:%S', $date->getTimestamp());

    if (!is_numeric($timestamp)) $timestamp = strtotime($timestamp);
    if (!$timestamp) return null;
    setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr', 'fr');
    $french_date = strftime("%d %B %Y à %H:%M", $timestamp);
    $y = date('Y');

    $french_date = str_replace(' ' . $y, '', $french_date);

    return $french_date;*/
}
