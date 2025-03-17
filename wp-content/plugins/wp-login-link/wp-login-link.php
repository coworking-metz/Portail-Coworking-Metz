<?php

/**
 * Plugin Name: WP Login link
 * Description: Permet aux utilisateurs de se connecter via un lien unique envoyé par email.
 * Version: 1.0
 */

add_action('wp_head', function () { ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a.login-link').forEach(link => link.addEventListener('click', function(e) {
                e.preventDefault();
                const email = prompt('Adresse e-mail de votre compte');

                if (!email) return

                const href = link.href;
                window.open(href + encodeURIComponent(email), '_self');
            }));
        })
    </script>
<?php });
// // Ajouter le lien "Recevoir un lien de connexion par mail" sur la page de connexion WP et WooCommerce
// add_action('login_form', function () {
//     echo '<div><a class="login-link woocommerce-button button woocommerce-form-login__submit wp-element-button" href="/wp-login.php?login-link-email=">🔓 Recevoir un lien de connexion par mail</a></div><div style="clear:both"></div><hr>';
// });

add_action('woocommerce_login_form_start', function () {
    echo '<div><a class="login-link woocommerce-button button woocommerce-form-login__submit wp-element-button" href="/wp-login.php?login-link-email=">🔓 Recevoir un lien de connexion par mail</a></div><div style="clear:both"></div><hr>';
});

// // Affichage du formulaire pour entrer l'email
// add_action('login_form', function() {
//     if (isset($_GET['email_login'])) {
//         echo '!!!<form class="woocommerce-form woocommerce-form-login login" method="post">';
//         echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
//         echo '<label for="user_email">Email&nbsp;<span class="required">*</span></label>';
//         echo '<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="user_email" id="user_email" autocomplete="email" required>';
//         echo '</p>';
//         wp_nonce_field('send_login_link', 'email_login_nonce');
//         echo '<p class="form-row">';
//         echo '<button type="submit" class="woocommerce-button button woocommerce-form-login__submit wp-element-button">Envoyer le lien de connexion</button>';
//         echo '</p>';
//         echo '</form>';
//         exit;
//     }
// });

// Traitement du formulaire et envoi du mail
if ($_GET['login-link-email'] ?? false) {
    add_action('init', function () {
        $user_email = sanitize_email($_GET['login-link-email']);
        $user = get_user_by('email', $user_email);

        if (!$user) {
            wp_redirect_notification('/mon-compte/', ['type' => 'error', 'titre' => 'Aucun utilisateur trouvé avec cet email.', 'texte' => 'En cas de question, vous pouvez <a href="#ouvrir-brevo">nous contacter ici</a>']);
        }
        $token = bin2hex(random_bytes(16));
        $expire = time() + HOUR_IN_SECONDS;

        update_user_meta($user->ID, '_email_login_token', $token);
        update_user_meta($user->ID, '_email_login_expire', $expire);

        $login_url = add_query_arg([
            'user_id' => $user->ID,
            'token' => $token
        ], wp_login_url());
        $to = $user->user_email;

        if (wp_get_environment_type() == 'local')
            $to = DEFAULT_TO_EMAIL;
        if (wp_mail($to, 'Votre lien de connexion', 'Cliquez sur ce lien pour vous connecter : <a href="' . $login_url.'">Se connecter<a><br><br>Vous pouvez aussi copier/coller ce lien dans votre navigateur : ' . $login_url.'')) {

            wp_redirect_notification('/mon-compte/', ['type' => 'success', 'titre' => 'Un lien de connexion a été envoyé à votre email.', 'texte' => 'Pensez à consulter votre dossier spam si vous ne trouvez pas le mail dans votre boîte de réception.']);
        }
    });
}

// Traitement du lien de connexion
add_action('init', function () {
    if (isset($_GET['user_id'], $_GET['token'])) {
        $user_id = intval($_GET['user_id']);
        $token = sanitize_text_field($_GET['token']);

        $stored_token = get_user_meta($user_id, '_email_login_token', true);
        $expire = get_user_meta($user_id, '_email_login_expire', true);

        if ($token === $stored_token && time() < $expire) {
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            delete_user_meta($user_id, '_email_login_token');
            delete_user_meta($user_id, '_email_login_expire');

            wp_redirect_notification('/mon-compte/', ['type' => 'success', 'titre' => 'Connexion effectuée.', 'texte' => 'Vous êtes à présent connecté avec le compte <b>'.get_userdata($user_id)->user_email.'</b>.']);

            exit;
        } else {
            wp_die('Lien invalide ou expiré.');
        }
    }
});
