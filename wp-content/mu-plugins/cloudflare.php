<?php

class CF
{

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
     * Purger les urls passées en paramètres dans CF
     * @param mixed $urls
     * @return bool
     */
    static function purgeUrls($urls)
    {

        foreach (splitArrayByLength($urls, 5) as $tmp_urls) {
            Cf::doPurgeUrls($tmp_urls);
        }
    }

    static function doPurgeUrls($urls)
    {

        $files = !is_array($urls) ? [$urls] : $urls;

        $head = [];
        $head[] = 'Content-Type: application/json';
        $head[] = 'Authorization: Bearer ' . CF_KEY;
        $head[] = 'cache-control: no-cache';
        $zoneId='39b23ee772403400e6b2e1805be4ef55';

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

