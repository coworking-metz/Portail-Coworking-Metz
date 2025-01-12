<?php
/**
 * Supprime un paramètre spécifique d'une URI.
 *
 * @param string $uri   L'URI d'entrée.
 * @param string $param Le nom du paramètre à supprimer.
 * @return string       L'URI mise à jour.
 */
function remove_url_parameter(string $url, string $param): string {
    $urlParts = parse_url($url);
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
        unset($queryParams[$param]);
        $urlParts['query'] = http_build_query($queryParams);
    }

    $newUrl = (isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '')
            . (isset($urlParts['host']) ? $urlParts['host'] : '')
            . (isset($urlParts['path']) ? $urlParts['path'] : '')
            . (isset($urlParts['query']) && $urlParts['query'] ? '?' . $urlParts['query'] : '')
            . (isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : '');
    return $newUrl;
}


/**
 * Redirects to a specified URL using a meta refresh tag.
 *
 * @param string $url The URL to redirect to.
 * @param int $delay Delay in seconds before the redirection takes effect.
 */
function custom_redirect($url, $delay = 0)
{
    echo "<style>body >* {display:none !important} body:before {position:fixed;top:50%;left:50%;content:'Redirection en cours...';transform:translate(-50%,-50%);}</style><meta http-equiv='refresh' content='{$delay};url={$url}'>";
    exit;
}


/**
 * Fetches and decodes JSON data from a URL.
 *
 * @param string $url URL to fetch the JSON from.
 * @param bool $assoc Whether to return an associative array (true) or an object (false).
 * @param resource $context Stream context for HTTP headers (optional).
 * @return mixed Returns the decoded JSON as an array or object, or null on error.
 */
function file_get_json($url, $assoc = true, $context = null)
{
    if (empty($url)) {
        return null;
    }

    // Use the context if provided
    $jsonContent = file_get_contents($url, false, $context);
    if ($jsonContent === false) {
        return null;
    }

    $data = json_decode($jsonContent, $assoc);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    return $data;
}


/**
 * Récupère et interprète les erreurs d'upload de fichiers.
 *
 * @param int $errorCode Code d'erreur de l'upload.
 * @return string Description de l'erreur.
 */
function getFileUploadError($errorCode)
{
    $errors = [
        UPLOAD_ERR_OK         => false,
        UPLOAD_ERR_INI_SIZE   => 'Le fichier téléchargé dépasse maximale de ' . ini_get('upload_max_filesize'),
        UPLOAD_ERR_FORM_SIZE  => 'Le fichier téléchargé dépasse la directive MAX_FILE_SIZE spécifiée dans le formulaire HTML.',
        UPLOAD_ERR_PARTIAL    => 'Le fichier n\'a été que partiellement téléchargé.',
        UPLOAD_ERR_NO_FILE    => 'Aucun fichier n\'a été téléchargé.',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
        UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque.',
        UPLOAD_ERR_EXTENSION  => 'Une extension PHP a arrêté le téléchargement du fichier.'
    ];

    return $errors[$errorCode] ?? 'Erreur inconnue lors du téléchargement du fichier.';
}


/**
 * Vérifie si une date donnée est un jour ouvrable.
 *
 * @param string $date Date à vérifier au format YYYY-MM-DD.
 * @return bool Retourne true si c'est un jour ouvrable, sinon false.
 */
function isWorkDay($date)
{
    // Convert the date string into a DateTime object
    $dateTime = new DateTime($date);

    // Get the day of the week (1 = Monday, ..., 7 = Sunday)
    $dayOfWeek = $dateTime->format('N');

    // Check if the day is a Saturday (6), Sunday (7), or a defined holiday
    if ($dayOfWeek == 6 || $dayOfWeek == 7) {
        return false;
    }

    return true;
}


function get_json($url)
{
    $key = 'json-' . $url;
    $data = get_transient($key);
    if (!$data) {
        $data = file_get_contents($url);
        $data = json_decode($data, true);
        set_transient($key, $data, DAY_IN_SECONDS);
    }
    return $data;
}
/**
 * Renvoie le dernier nom de dossier dans un chemin de fichier, en gérant les noms de fichiers à la fin.
 * @param string $path Le chemin du fichier.
 * @return string Le dernier nom de dossier.
 */
function getLastFolderName($path)
{
    $path = rtrim($path, "/");  // Supprime le slash de fin si présent
    $parts = explode("/", $path);  // Divise le chemin en parties

    // Si le dernier élément contient un point, il est considéré comme un fichier
    if (strpos(end($parts), '.') !== false) {
        array_pop($parts);  // Retire le fichier pour accéder au dernier dossier
    }
    return end($parts);  // Retourne la dernière partie
}




/**
 * Fetches a Markdown file from a URL and returns the HTML content.
 * 
 * @param string $url The URL of the markdown file.
 * @return string The parsed HTML content.
 */
function fetchAndParseMarkdown($url)
{
    require_once ABSPATH . MUPLUGINDIR . '/Classes/Parsedown.php';
    $markdownContent = file_get_contents($url);
    if ($markdownContent === false) {
        return;
    }
    $parsedown = new Parsedown();
    $html = $parsedown->text($markdownContent);
    return $html;
}



function replace_first_occurrence($search, $replace, $subject)
{
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject; // No change if the search string is not found
}

/**
 * Retourne l'uri de la page, sans query string ni slash final
 *
 */
function get_current_uri()
{
    return rtrim(explode('?', $_SERVER['REQUEST_URI'] ?? '')[0], '/');
}
/**
 * retourne une phrase indiquant une quantité accompagnée d'un libellé texte mis au pluriel
 *
 * @param  mixed $qte La quantité déterminant le pluriel ou nom
 * @param  mixed $lib Le ou les mots suivants à mettre au pluriel (séparés par des espaces)
 * @param  mixed $pluriel La marque du pluriel à appliquer au libellé (défaut: s)
 * @return void
 */
function pluriel($qte, $lib, $pluriel = 's', $separateur = ' ')
{
    if ($qte > 1) {
        $lib = explode($separateur, $lib);
        foreach ($lib as $a => $b) {
            $lib[$a] = $b . $pluriel;
        }
        return $qte . ' ' . implode($separateur, $lib);
    } else {
        return $qte . ' ' . $lib;
    }
}
/**
 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
 *  origin.
 *
 *  In a production environment, you probably want to be more restrictive, but this gives you
 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
 *
 *  - https://developer.mozilla.org/en/HTTP_access_control
 *  - https://fetch.spec.whatwg.org/#http-cors-protocol
 *
 */
function allow_cors()
{

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}
/**
 * Obtenir l'URL du Gravatar pour une adresse e-mail donnée.
 *
 * @param string $email L'adresse e-mail pour laquelle obtenir le Gravatar.
 * @param int    $size  La taille de l'image du Gravatar en pixels. Par défaut à 80.
 * @return string L'URL du Gravatar.
 */
function get_gravatar_url($email, $size = 300)
{
    $email_hash = md5(strtolower(trim($email)));
    $gravatar_url = "https://www.gravatar.com/avatar/$email_hash?size=$size";
    return $gravatar_url;
}
/**
 * Diviser un tableau en sous-tableaux de taille fixe.
 *
 * @param array $input_array Le tableau à diviser.
 * @param int $size La taille de chaque sous-tableau.
 * @return array Un tableau de sous-tableaux.
 */
function slice_array_to_chunks($input_array, $size = 100)
{
    $output_array = [];
    $array_length = count($input_array);

    for ($i = 0; $i < $array_length; $i += $size) {
        $output_array[] = array_slice($input_array, $i, $size);
    }

    return $output_array;
}



function pathTourl($path)
{
    if (!$path) return;
    return str_replace(ABSPATH, site_url() . '/', $path);
}
/**
 * Ajoute un fichier JavaScript à la queue des scripts de WordPress.
 *
 * La fonction vérifie d'abord l'existence du fichier dans le répertoire spécifié.
 * Si le fichier existe, il est ajouté à la queue avec les dépendances fournies.
 * Sinon, un message d'erreur est affiché.
 *
 * @param string $w           Nom du fichier JavaScript (sans l'extension .js).
 * @param array  $dependances Liste des dépendances du script. Par défaut, jQuery est la seule dépendance.
 *
 * @return void
 * 
 * @throws Exception Si le fichier n'existe pas.
 */
function ajouter_js($w, $dependances = ['jquery'])
{
    $file = WPMU_PLUGIN_DIR . '/js/' . $w . '.js';
    if (!file_exists($file)) {
        die($file . ' n\'existe pas');
    }
    $url = '/wp-content/' . explode('wp-content/', $file)[1];
    $v = filemtime($file);
    return wp_enqueue_script('js-' . $w,  $url, $dependances, $v);
}


/**
 * Ajoute un fichier css à la queue des scripts de WordPress.
 *
 * La fonction vérifie d'abord l'existence du fichier dans le répertoire spécifié.
 * Si le fichier existe, il est ajouté à la queue avec les dépendances fournies.
 * Sinon, un message d'erreur est affiché.
 *
 * @param string $w           Nom du fichier JavaScript (sans l'extension .js).
 * @param array  $dependances Liste des dépendances du script. Par défaut, jQuery est la seule dépendance.
 *
 * @return void
 * 
 * @throws Exception Si le fichier n'existe pas.
 */
function ajouter_css($w, $dependances = [])
{
    $file = WPMU_PLUGIN_DIR . '/css/' . $w . '.css';
    if (!file_exists($file)) {
        die($file . ' n\'existe pas');
    }
    $url = '/wp-content/' . explode('wp-content/', $file)[1];
    $v = filemtime($file);
    wp_enqueue_style($w,  $url, $dependances, $v);
}

/**
 * Extrait les dates et exclut celles qui sont passées
 *
 * @return array Retourne les dates futures
 */
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
/**
 * Convertit une URL en chemin absolu du fichier
 *
 * @param string $url L'URL du fichier
 * @return string|false Retourne le chemin absolu ou false si non trouvable
 */
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
/**
 * Récupère le contenu d'une URL
 *
 * @param string $url L'URL à récupérer
 * @return mixed Retourne le contenu de l'URL ou false en cas d'échec
 */
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
/**
 * Obtient l'URL du site actuel
 *
 * @param bool|string $host Le nom d'hôte
 * @param bool $request Inclure la chaîne de requête ou non
 * @return string Retourne l'URL complète
 */
function current_site_url($host = false, $request = false)
{
    if (php_sapi_name() == 'cli') return;
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

/**
 * Divise un tableau en sous-tableaux de longueur spécifiée
 *
 * @param array $array Le tableau à diviser
 * @param int $length La longueur des sous-tableaux
 * @return array Retourne un tableau de sous-tableaux
 */
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
 * retourne une valeur de data si l'index `$keys` existe. Fonctionne aussi bien avec un tableau qu'un objet
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



/**
 * Crée une ressource d'image à partir d'un fichier.
 *
 * @param string $filepath Chemin d'accès au fichier image.
 *
 * @return GDImage|resource|false Ressource d'image ou false en cas d'échec.
 */
function imagecreatefromfile($filepath)
{
    // Check if the file exists
    if (!file_exists($filepath)) {
        return false;
    }

    // Determine the type of image
    $type = exif_imagetype($filepath);

    switch ($type) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($filepath);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($filepath);
        case IMAGETYPE_WEBP:
            return imagecreatefromwebp($filepath);
            // Add more cases as needed, like for GIF, BMP, etc.
        default:
            return false; // Or throw an exception, based on your needs.
    }
}
