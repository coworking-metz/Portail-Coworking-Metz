<?php


function tickets($endpoint, $options = [])
{
    $url = TICKET_BASE_URL . $endpoint;
    $payload = $options['payload'] ?? [];

    $key = 'tickets-' . sha1($endpoint . serialize($payload));

    if (isset($GLOBALS[$key])) return $GLOBALS[$key];
    // $payload['key'] = API_KEY_TICKET;

    $url = add_query_arg($payload, $url);

    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Token " . API_KEY_TICKET
        ]
    ]);

    $return = file_get_json($url, true, $context);
    $GLOBALS[$key] = $return;
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
