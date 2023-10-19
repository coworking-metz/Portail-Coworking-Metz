<?php


function extractDatesExcludePast()
{
    $args = func_get_args();
    $today = new DateTime();
    $dates = [];
    foreach ($args as $arg) {

        if (is_array($arg)) {
            $lines = $arg;
        } else {
            $lines = explode("\n", $arg);
        }

        foreach ($lines as $line) {
            if (strpos($line, '>')) {
                // Plage de dates
                list($startDate, $endDate) = explode('>', $line);
                $startDate = trim($startDate);
                $endDate = trim($endDate);

                $currentDate = DateTime::createFromFormat('d/m/Y', $startDate);
                $end = DateTime::createFromFormat('d/m/Y', $endDate);

                while ($currentDate <= $end) {
                    if ($currentDate >= $today) {
                        $dates[] = $currentDate->format('Y-m-d');
                    }
                    $currentDate->modify('+1 day');
                }
            } else {
                // Date unique
                $currentDate = DateTime::createFromFormat('d/m/Y', trim($line));
                if ($currentDate >= $today) {
                    $dates[] = $currentDate->format('Y-m-d');
                }
            }
        }
    }

    $dates = array_values(array_unique($dates));
    sort($dates);
    return $dates;
}

function urlToPath($url)
{
    $siteUrl = site_url();
    $absPath = ABSPATH;

    if (strpos($url, $siteUrl) === 0) {
        $relativeUrl = substr($url, strlen($siteUrl));
        $filePath = $absPath . ltrim($relativeUrl, '/');
        return $filePath;
    }

    return false;
}

function url_get_contents($url)
{
    // initialize curl session
    $ch = curl_init();

    $url = str_replace(' ', '%20', $url);

    // set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // execute curl session
    $output = curl_exec($ch);

    // convert the response from JSON to a PHP object
    $output = json_decode($output);

    // check if HTTP response code is not 200 (OK)
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
        // dump the output for debugging purposes
        // var_dump($output);
        return false; // or you might want to throw an exception or return an error message
    }

    echo curl_error($ch);
    // close the curl session
    curl_close($ch);

    // return the output
    return $output;
}

function current_site_url($host = false, $request = false)
{
    $host = $host ? $host : $_SERVER['HTTP_HOST'];
    $https = false;
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $https = true;
    }

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $https = true;
    }
    $url = ($https ? 'https' : 'http') . '://' . $host;
    if ($request) {
        $url .= $_SERVER['REQUEST_URI'];
    }
    return $url;
}


function splitArrayByLength($array, $length = 5)
{
    if (!$array) return [];
    if (!is_array($array)) return [];
    $subArrays = [];
    $currentIndex = 0;
    $arrayLength = count($array);

    while ($currentIndex < $arrayLength) {
        $subArray = array_slice($array, $currentIndex, $length);
        $subArrays[] = $subArray;
        $currentIndex += $length;
    }

    return $subArrays;
}

/**
 * retourne une valeur de data si l'index `$keys` existe
 *
 * @param  mixed $data La donnée à analyser
 * @param  mixed $keys Liste des champs à rechercher dans $data. Le premier trouvé sera retourné
 * @param  mixed $default Valeur par défaut si rien n'a été trouvé dans $data
 * @return mixed La valeur qui a été trouvée
 * @package Utils
 * @subpackage Utilitaires
 */
function si($data, $keys, $default = null)
{
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (is_array($data) || $data instanceof stdClass) {
        foreach (tableau($keys) as $key) {
            if (isset($data[$key])) {
                return $data[$key];
            }
        }
    }
    return $default;
}

/**
 * tableau - Fait en sorte que la donnée d'entrée soit transformée en tableau si elle ne l'est pas déjà
 *
 * @example 
 * tableau(1) retourne [1]
 * tableau([1]) retourne [1]
 * @param  mixed $data La donnée envoyée
 * @return array
 * @package Utils
 * @subpackage Utilitaires
 */
function tableau($data)
{
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (!is_array($data)) {
        $data = [$data];
    }

    return array_filter($data);
}
