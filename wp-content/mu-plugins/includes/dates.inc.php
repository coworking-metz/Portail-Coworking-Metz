<?php
define('ONE_MINUTE', 60);
define('FIVE_MINUTES', 5 * ONE_MINUTE);
define('ONE_HOUR', ONE_MINUTE * 60);
define('HALF_HOUR', ONE_HOUR / 2);
define('ONE_DAY', ONE_HOUR * 24);
define('ONE_WEEK', ONE_DAY * 7);
define('ONE_MONTH', ONE_DAY * 31);

/**
 * Converts a date string to the format YYYY-MM-DD.
 *
 * @param string $dateStr The date string in YYYY-MM-DD or DD/MM/YYYY format.
 * @return string The date in YYYY-MM-DD format.
 */
function nettoyerDate($dateStr)
{
    foreach (['Y-m-d', 'd/m/Y', 'Y/m/d','Ymd'] as $format) {
        $date = DateTime::createFromFormat($format, $dateStr);
        if ($date) {
            break;
        }
    }
    if (!$date) return $dateStr;


    return $date->format('Y-m-d');
}


/**
 * Récupère les jours fériés et les vacances scolaires.
 *
 * Utilise des URL externes pour obtenir les données au format ICS. Le résultat est sauvegardé dans un transient pour améliorer les performances.
 *
 * @return array Les jours fériés et les vacances sous forme de tableau. Chaque élément est une chaîne formatée comme "d/m/Y > d/m/Y".
 */
function fetch_holidays()
{
    // Vérification du transient
    $key = 'dates-vacances';
    $holidays = get_transient($key);
    // $holidays = false;
    if (!$holidays) {
        $holidays = [];

        $urls = [
            'https://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-B.ics',
            // 'https://etalab.github.io/jours-feries-france-data/ics/jours_feries_metropole.ics'
        ];
        foreach ($urls as $url) {
            $response = wp_remote_get($url);

            // Vérification de l'état de la réponse
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return [];
            }

            $ics_data = wp_remote_retrieve_body($response);
            preg_match_all('/DTSTART;VALUE=DATE:(\d+)\r\nDTEND;VALUE=DATE:(\d+)/', $ics_data, $matches);
            $today = new DateTime();
            $six_months_later = (clone $today)->modify('+6 months');

            foreach ($matches[1] as $index => $start) {
                $end_date = DateTime::createFromFormat('Ymd', $matches[2][$index]);

                // Filtrage des dates passées et au-delà de 6 mois
                if ($end_date >= $today && $end_date <= $six_months_later) {
                    $formatted_start = DateTime::createFromFormat('Ymd', $start)->format('d/m/Y');
                    $formatted_end = $end_date->format('d/m/Y');
                    $holidays[] = "$formatted_start > $formatted_end";
                }
            }
        }
        // Sauvegarde du résultat dans un transient pendant un mois
        set_transient($key, $holidays, 30 * DAY_IN_SECONDS);
    }

    return $holidays;
}




/**
 * Vérifie si une date donnée est dans le passé.
 *
 * @param string $date La date à vérifier, au format 'Y-m-d'.
 * @return bool True si la date est dans le passé, sinon false.
 */
function isPast($date)
{
    $currentDate = date('Y-m-d');
    return ($date < $currentDate);
}


/**
 * Vérifie si la date est dans le futur
 *
 * @param string $date Date au format 'Y-m-d'
 * @return bool
 */
function isFuture($date)
{
    $currentDate = date('Y-m-d');
    return ($date > $currentDate);
}
/**
 * Vérifie si la date correspond à aujourd'hui
 *
 * @param string $date Date au format 'Y-m-d'
 * @return bool
 */
function isToday($date)
{
    $date = date('Y-m-d', strtotime($date));
    $currentDate = date('Y-m-d');
    return ($date === $currentDate);
}
/**
 * Vérifie si la date correspond à demain
 *
 * @param string $date Date au format 'Y-m-d'
 * @return bool
 */
function isTomorrow($date)
{
    $date = date('Y-m-d', strtotime($date));
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    return ($date === $tomorrow);
}

/**
 * Convertit un timestamp en date et heure formatées selon la locale française.
 *
 * @param string $t Le timestamp à convertir.
 * @return string La date formatée.
 */
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

/**
 * Convertit un timestamp en date formatée selon la locale française, avec ou sans l'heure.
 *
 * @param string $timestamp Le timestamp à convertir.
 * @param bool $heure Indique si l'heure doit être incluse dans la date formatée.
 * @return string|null La date formatée, ou null en cas d'erreur.
 */
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
