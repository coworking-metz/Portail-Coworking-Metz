<?php
function urlToPath($url) {
    $siteUrl = site_url();
    $absPath = ABSPATH;
    
    if (strpos($url, $siteUrl) === 0) {
        $relativeUrl = substr($url, strlen($siteUrl));
        $filePath = $absPath . ltrim($relativeUrl, '/');
        return $filePath;
    }

    return false;
}

function url_get_contents($url) {
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
    if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
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
