<?php

namespace CoworkingMetz;

class CloudFlare
{
    private static $zone_id = '39b23ee772403400e6b2e1805be4ef55';
    private static $api_key_cache = CLOUDFLARE_API_KEY_FOR_CACHE;


    public static function defaultUrls()
    {
        $urls = [
            home_url('/'),
        ];

        return $urls;
    }
    public static function cacheHeaders($max_age = null)
    {
        if (isset($_GET['nocache'])) return;
        if (is_null($max_age)) {
            // $max_age = 3600;
            $max_age = 3600 * 24;
        }

        header_remove('Pragma');
        header_remove('Expires');
        header_remove('Cache-Control');
        // Add cache-headers so that Cloudflare can cache the response.
        header('Cache-Control: public, max-age=60, s-maxage=' . $max_age . '');
    }
    /**
     * Purger les urls passées en paramètres dans CF
     * @param mixed $urls
     * @return bool
     */
    public static function purgeUrls($urls, $prechauffer = false)
    {

        foreach (splitArrayByLength($urls, 10) as $tmp_urls) {
            CloudFlare::doPurgeUrls($tmp_urls);
        }

        if ($prechauffer) {
            wp_schedule_single_event(time(), 'cloudflare_prechauffer_cache', [array_reverse($urls)]);
        }
    }

    public static function doPurgeUrls($urls)
    {

        $files = tableau($urls);
		file_get_contents('https://coworking.requestcatcher.com/wordpress?'.urldecode(http_build_query(['files'=>array_map(function($file) { return str_replace(site_url(), '/', $file);}, $files)])));
        $head = [];
        $head[] = 'Content-Type: application/json';
        $head[] = 'Authorization: Bearer ' . self::$api_key_cache;
        $head[] = 'cache-control: no-cache';

        $endpoint = "https://api.cloudflare.com/client/v4/zones";
        $zoneID = self::$zone_id;

        $url = $endpoint . '/' . $zoneID . '/purge_cache';
        $purge = ['files' => $files];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($purge));
        try {
            $result = curl_exec($ch);
            // m($files, $result);
            if ($result) {
                $data = json_decode($result, true);
                return $data['success'] ?? false;
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Vide le cache CloudFlare pour un domaine donné.
     *
     * @param string $domain Le domaine pour lequel le cache doit être vidé.
     * @param string $authEmail L'adresse email associée au compte CloudFlare.
     * @param string $authKey La clé d'API globale de CloudFlare.
     * @return void
     */
    public static function purgeDomainCache()
    {

        file_get_contents('https://coworking.requestcatcher.com/wordpress?purgeDomainCache');
        $endpoint = "https://api.cloudflare.com/client/v4/zones";
        $zoneID = self::$zone_id;

        $ch = curl_init($endpoint . '/' . $zoneID . '/purge_cache');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $head = [];
        $head[] = 'Content-Type: application/json';
        $head[] = 'Authorization: Bearer ' . self::$api_key_cache;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["purge_everything" => true]));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        return json_decode($response);
    }
}


// $user    = new Cloudflare\API\Endpoints\User($adapter);
// 