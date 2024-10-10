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
    if ($id_erreur == 'mac-random') {
        return 'Cette adresse MAC semble être une adresse aléatoire, incompatible avec le système de détection des présences du coworking. Vous pouvez <a href="https://support.osmozis.com/je-suis-deja-connectee/comment-desactiver-les-adresses-mac-aleatoires/" target="_blank">consulter cette page pour en savoir plus</a> ou bien <a href="#ouvrir-brevo">nous contacter pour demander de l\'aide</a>';
    }
    if ($id_erreur == 'ajout-mac-impossible') {
        return "Ajout de l'adresse MAC impossible";
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

/**
 * Vérifie si une adresse MAC est probablement randomisée
 * @param string $mac L'adresse MAC à vérifier
 * @return bool True si l'adresse MAC est probablement randomisée, false sinon
 */
function isMacAddressRandomized($mac) {
    // Normalise l'adresse MAC en supprimant les caractères non hexadécimaux
    $cleanedMac = strtoupper(preg_replace('/[^a-fA-F0-9]/', '', $mac));

    // Récupère le deuxième caractère de l'adresse MAC nettoyée
    $secondChar = $cleanedMac[1];

    // Définit les caractères qui indiquent une adresse randomisée
    $randomizedChars = ['2', '6', 'A', 'E'];

    // Vérifie si le deuxième caractère est un de ceux qui indiquent une randomisation
    return in_array($secondChar, $randomizedChars);
}
