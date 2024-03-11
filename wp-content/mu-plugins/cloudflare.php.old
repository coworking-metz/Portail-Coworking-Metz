<?php

class CF
{

    /**
     * Configure les en-têtes de cache pour la réponse HTTP.
     *
     * @param int|null $max_age Durée maximale de mise en cache en secondes. Par défaut, c'est 3600.
     */

    static function cacheHeaders($max_age = null)
    {
        if (is_null($max_age)) {
            $max_age = 3600;
        }

        header_remove('Pragma');
        header_remove('Expires');
        header_remove('Cache-Control');
        // Add cache-headers so that Cloudflare can cache the response.
        header('Cache-Control: public, max-age=60, s-maxage=' . $max_age . '');
    }
    /**
     * Purge une liste d'URLs dans Cloudflare (CF) par lots de 5 urls.
     *
     * @param mixed $urls Liste d'URLs à purger.
     * @return void
     */

    static function purgeUrls($urls)
    {

        foreach (splitArrayByLength($urls, 5) as $tmp_urls) {
            Cf::doPurgeUrls($tmp_urls);
        }
    }

    /**
     * Exécute la purge d'URLs dans Cloudflare en utilisant l'API.
     *
     * @param array|string $urls Liste d'URLs à purger.
     * @return bool|void True si la purge a réussi, sinon false.
     */
    static function doPurgeUrls($urls)
    {

        $files = !is_array($urls) ? [$urls] : $urls;
		file_get_contents('https://coworking.requestcatcher.com/wordpress?'.urldecode(http_build_query(['files'=>array_map(function($file) { return str_replace(site_url(), '/', $file);}, $files)])));
        $head = [];
        $head[] = 'Content-Type: application/json';
        $head[] = 'Authorization: Bearer ' . CF_KEY;
        $head[] = 'cache-control: no-cache';
        $zoneId = '39b23ee772403400e6b2e1805be4ef55';

        $url = "https://api.cloudflare.com/client/v4/zones/$zoneId/purge_cache";
        $purge = ['files' => $files];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($purge));
        try {
            $result = curl_exec($ch);
            if ($result) {
                $data = json_decode($result, true);
                return $data['success'] ?? false;
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (Exception $e) {
            return false;
        }
    }
}
