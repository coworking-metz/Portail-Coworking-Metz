<?php


function addMemberMacAddress($user_id, $adresseMac) {
    if (!$user_id || !$adresseMac) {
        return false;
    }

    $devices = getDevices(); 
    $adressesMac = array_column($devices, 'macAddress');

    if (in_array($adresseMac, $adressesMac)) {
        return;
    }

    // Add the new MAC address and update the user's devices
    $adressesMac[] = $adresseMac;
    $response = updateMemberMacAddresses($user_id, $adressesMac);

	return true;
}


function formatMac($mac)
{
    $mac = trim($mac);
    $mac = str_replace(' ', ':', $mac);
    $mac = str_replace('-', ':', $mac);
    $mac = strtoupper($mac);
    return $mac;
}
function devices_get_erreur($id_erreur)
{
    if ($id_erreur == 'mac-deja-enregistree') {
        return "Cette adresse MAC est déjà associée à votre compte";
    }
    if (strstr($id_erreur, 'mac-random')) {
        $erreur = ['erreur' => 'Adresse MAC virtuelle', 'type' => 'warning', 'titre' => 'Appareil potentiellement incompatible', 'texte' => "L'adresse MAC de l'appareil semble être une adresse virtuelle, potentiellement incompatible avec la système de détection du coworking. Vérifiez que cette adresse MAc est bien fixe. <a href='https://www.coworking-metz.fr/comment-desactiver-les-adresses-mac-aleatoires/' target='_blank'>En savoir plus</a>", 'cta' => ['url' => '#ouvrir-brevo', 'caption' => 'Demander de l\'aide'], 'id' => 'random'];
        if ($id_erreur == 'mac-random') {
            return $erreur;
        }
        return '<b>' . $erreur['erreur'] . '</b> ' . $erreur['texte'];
    }
    if ($id_erreur == 'ajout-mac-impossible') {
        return "Ajout de l'adresse MAC impossible";
    }
}


function updateMemberMacAddresses($userId, $macAddresses)
{
    $url = TICKET_BASE_URL . "/members/{$userId}/mac-addresses";
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
        $data = json_decode($response, true);
		if($data['message']??false) return false;
		return true;
    }
    // Fermer la session cURL
    curl_close($ch);
}

function getDevices($user_id = null)
{
    $user_id = $user_id ?: get_current_user_id();
    $devices = tickets('/members/' . $user_id . '/mac-addresses');
    return $devices;
}

/**
 * Vérifie si une adresse MAC est probablement randomisée
 * @param string $mac L'adresse MAC à vérifier
 * @return bool True si l'adresse MAC est probablement randomisée, false sinon
 */
function isMacAddressRandomized($mac)
{
    // Normalise l'adresse MAC en supprimant les caractères non hexadécimaux
    $cleanedMac = strtoupper(preg_replace('/[^a-fA-F0-9]/', '', $mac));

    // Récupère le deuxième caractère de l'adresse MAC nettoyée
    $secondChar = $cleanedMac[1];

    // Définit les caractères qui indiquent une adresse randomisée
    $randomizedChars = ['2', '6', 'A', 'E'];

    // Vérifie si le deuxième caractère est un de ceux qui indiquent une randomisation
    return in_array($secondChar, $randomizedChars);
}
