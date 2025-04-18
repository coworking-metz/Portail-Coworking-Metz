<?php

if (isset($_GET['logout'])) {
    // Fonction pour déconnecter l'utilisateur si $_GET['logout'] est présent
    add_action('init', function () {
        // Déconnecte l'utilisateur courant
        wp_logout();

		$protocols = wp_allowed_protocols();
		$protocols[]= 'poulailler';
        // Redirige vers l'URL spécifiée dans 'redirect_to' ou vers la page d'accueil
        $redirect_to = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to'],$protocols) : home_url('/');

        wp_redirect($redirect_to);
        exit;
    });
}
