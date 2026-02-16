<?php


function tickets($endpoint, $options = [])
{
    $url = TICKET_BASE_URL . $endpoint;
    $payload = $options['payload'] ?? [];
    $cache = $options['cache'] ?? false;

    $key = 'tickets-' . sha1($endpoint . serialize($payload));

	if($cache) {
		$cached = get_transient($key);
		if($cached !==false) return $cached;
	} else {
		if (isset($GLOBALS[$key])) return $GLOBALS[$key];
	}
 
    $url = add_query_arg($payload, $url);
    if (isset($_GET['debug-api'])) m(add_query_arg(['key'=>API_KEY_TICKET],$url));

    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Token " . API_KEY_TICKET
        ]
    ]);

    $return = file_get_json($url, true, $context);
    $GLOBALS[$key] = $return;
	set_transient($key, $return);
    return $return;
}

function isAboEnCours($date)
{
    $dateAbo = strtotime($date);
    $date = date('Y-m-d', $dateAbo);
    if ($date >= date('Y-m-d')) return true;
}


function isOkMembership($date)
{

    $currentYear = date('Y');
    $nextYear = date('Y', strtotime('+1 year'));
    if ($date == $currentYear || $date == $nextYear) return true;
}

function get_jours_presence_par_annee(array $member, int|string $annee, int $ttl = 12 * HOUR_IN_SECONDS): int
{
    $uid = (int) $member['wpUserId'];

    // 🔑 Unique transient key per user + year
    $transient_key = "jours_presence_{$uid}_{$annee}";

    // ✅ Try cache first
    $jours = get_transient($transient_key);
    if ($jours !== false) {
        return (int) $jours;
    }

    // ❌ Cache miss → compute
    $activity = tickets('/members/' . $uid . '/activity');
    $jours = 0;

    foreach ($activity as $presence) {
        if (strstr($presence['date'], (string) $annee)) {
            $jours += (int) $presence['value'];
        }
    }

    // 💾 Store in cache
    set_transient($transient_key, $jours, $ttl);

    return $jours;
}
