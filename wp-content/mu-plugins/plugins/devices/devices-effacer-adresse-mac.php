<?php


$effacerAdreseMac = $_GET['effacer-adresse-mac']??false;
if($effacerAdreseMac) {

    add_action('init', function() use($effacerAdreseMac) {

        $user_id = get_current_user_id();
        if(!$user_id) return;

        $devices = getDevices();

        $adressesMac = array_column($devices, 'macAddress');

        if(!in_array($effacerAdreseMac, $adressesMac)) {
            wp_redirect('/mon-compte/appareils/?erreur=mac-inconnue');
            exit;
        }

        $adressesMac = array_values(array_filter($adressesMac, static function ($element) use($effacerAdreseMac){
            return $element !== $effacerAdreseMac;
        }));

        $response = updateMemberMacAddresses($user_id, $adressesMac);

        if(!$response) {
            wp_redirect('/mon-compte/appareils/?erreur=erreur-inconnue');
            exit;
        } 
        wp_redirect('/mon-compte/appareils/?status=device-removed');
        exit;
    });
}