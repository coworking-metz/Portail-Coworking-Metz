<?php

/**
 * Page de connexion « Mon compte » (WooCommerce) : mise en page dédiée,
 * affichée uniquement aux visiteurs non connectés sur /mon-compte/.
 * Les écrans mot de passe perdu / réinitialisation (endpoints WooCommerce)
 * gardent le rendu standard du thème.
 *
 * Retour à l'ancien rendu : ajouter ?login=classique à l'URL.
 */

// Capacité de l'espace (nombre de postes de travail)
if (!defined('COWORKING_NB_PLACES')) {
    define('COWORKING_NB_PLACES', 40);
}

add_filter('template_include', function ($template) {
    if (is_user_logged_in() || is_admin() || !function_exists('is_account_page') || !is_account_page()) {
        return $template;
    }
    if (is_wc_endpoint_url() || ($_GET['login'] ?? '') === 'classique') {
        return $template;
    }
    if (!defined('DONOTCACHEPAGE')) {
        define('DONOTCACHEPAGE', true);
    }
    nocache_headers();
    return dirname(__DIR__) . '/templates/login-page.php';
}, 99);
