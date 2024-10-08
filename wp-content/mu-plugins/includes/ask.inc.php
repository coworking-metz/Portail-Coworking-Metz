<?php



// Fonction pour interroger l'API GPT d'OpenAI via une requête POST
function askGPT($prompt, $args = [], $cache = true)
{

    if ($cache) {
        $key = 'ask-' . sha1($prompt . serialize($args));

        $result = get_transient($key);
        if (explode(':', $result)[0] == 'ERREUR') {
            return '';
        }
        if ($result) return $result;
    }

    $data = $args['data'] ?? false;
    $tokens = $args['tokens'] ?? false;
    $image = $args['image'] ?? false;
    $temperature = $args['temperature'] ?? 0.2;
    // $prompt .= 'Le texte fera environ 400 caractères maximum.';
    // $prompt .= PHP_EOL . 'Le texte sera rédigé sur un ton neutre et factuel.';
    // ms($prompt, ['data' => $data, 'image' => $image]);
    $prompt .= PHP_EOL . 'Ta réponse sera uniquement composée de la réponse à la question du prompt, sans introduction ni commentaire de ta part.';
    $prompt .= PHP_EOL . 'En cas d\'erreur, commence ta réponse par le mot "ERREUR:" puis détaille le problème que tu as rencontré.';
    // me($prompt);
    $url = 'https://tools.sopress.net/ask/';

    $query = [
        'temperature' => $temperature,
        'w' => $prompt,
    ];

    if ($image) {
        $query['image'] = $image;
    }
    if ($tokens) {
        $query['tokens'] = $tokens;
    }
    if ($data) {
        $query['data'] = json_encode($data, JSON_PRETTY_PRINT);
    }

    $postData = http_build_query($query);
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $postData,
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    if ($result['error'] ?? false) {
        mse($result);
    }
    $reponse = $result['message'] ?? false;
    set_transient($key, $reponse);

    if (explode(':', $reponse)[0] == 'ERREUR') {
        return '';
    }
    return $reponse;
}

