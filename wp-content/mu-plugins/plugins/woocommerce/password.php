<?php

/**
 * Met à jour le mot de passe d’un utilisateur et maintient sa session.
 *
 * Cette fonction :
 * - met à jour le mot de passe via wp_set_password ;
 * - régénère le cookie d’authentification pour éviter la déconnexion ;
 * - retourne true en cas de succès, false sinon.
 *
 * @param int    $user_id       ID de l’utilisateur.
 * @param string $new_password  Nouveau mot de passe à définir.
 *
 * @return bool
 */
function my_update_user_password( $user_id, $new_password ) {
    if ( ! $user_id || '' === (string) $new_password ) {
        return false;
    }

    wp_set_password( $new_password, $user_id );
    wp_set_auth_cookie( $user_id, true );

    return true;
}

// Prétraite la soumission, met à jour le mot de passe si les deux champs coïncident,
// purge les champs de mot de passe du POST, puis appelle le handler WooCommerce.
add_action( 'template_redirect', function () {
    if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
        return;
    }
    if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] ) {
        return;
    }

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return;
    }

	if(!empty($_POST['password_current'])) return;
    $pass1 = isset( $_POST['password_1'] ) ? (string) $_POST['password_1'] : '';
    $pass2 = isset( $_POST['password_2'] ) ? (string) $_POST['password_2'] : '';

    if ( '' !== $pass1 && $pass1 === $pass2 ) {
        my_update_user_password( $user_id, $pass1 );

        unset( $_POST['password_1'], $_POST['password_2'], $_POST['password_current'] );
    }

    remove_action( 'template_redirect', array( 'WC_Form_Handler', 'save_account_details' ) );
    WC_Form_Handler::save_account_details();


}, 9 );
