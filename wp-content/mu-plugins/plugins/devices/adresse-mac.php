<?php


$adresseMac = $_POST['adresse-mac'] ?? false;
if ($adresseMac) {
    add_action('init', function () use ($adresseMac) {

        $user_id = get_current_user_id();
        if(!$user_id) return;
        $devices = getDevices();
        
        $adressesMac = array_column($devices, 'macAddress');
        if(in_array($adresseMac, $adressesMac)) {
            wp_redirect('/mon-compte/appareils/?erreur=mac-deja-enregistree&adresse-mac=' . urlencode($adresseMac));
            exit;
        }
        $adressesMac[]= $adresseMac;
        $response = updateMemberMacAddresses($user_id, $adressesMac);
        if(!$response) {
            wp_redirect('/mon-compte/appareils/?erreur=ajout-mac-impossible&adresse-mac=' . urlencode($adresseMac));
            exit;
        }
        wp_redirect('/mon-compte/appareils/?status=device-added');
        exit;
    });
}
