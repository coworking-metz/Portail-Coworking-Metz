<?php
function formatMac($mac) {
    $mac = trim($mac);
    $mac =str_replace(' ',':', $mac);
    $mac =str_replace('-',':', $mac);
    $mac = strtoupper($mac);
    return $mac;
}
function devices_get_erreur($id_erreur)
{
    if ($id_erreur == 'mac-deja-enregistree') {
        return "Cette adresse MAC est déjà associée à votre compte";
    }
    if ($id_erreur == 'ajout-mac-impossible') {
        return "AJout de l'adresse MAC impossible";
    }
}


function updateMemberMacAddresses($userId, $macAddresses) {
    $url = TICKET_BASE_URL."/members/{$userId}/mac-addresses";
    // Remplacer par votre clé API
    $apiKey = "API_KEY_TICKET"; 

    // Préparer les données à envoyer dans le corps de la requête
    $data = json_encode($macAddresses);

    // Initialiser une session cURL
    $ch = curl_init($url);
    
    // Définir les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data),
        'Authorization: Token ' . API_KEY_TICKET  
    ]);

    // Exécuter la requête cURL et obtenir la réponse
    $response = curl_exec($ch);

    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        // echo 'Erreur cURL: ' . curl_error($ch);
        return false;
    } else {
        // Afficher la réponse
        return json_decode($response, true);
    }
    // Fermer la session cURL
    curl_close($ch);
}

function getDevices($user_id=null){
    $user_id = $user_id ?: get_current_user_id();
    $devices = tickets('/members/'.$user_id.'/mac-addresses');
    return $devices;
}