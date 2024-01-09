<?php



if (isset($_GET['brevo-notify'])) {

    add_action('init', function () {
        $action = $_GET['brevo-action'] ?? false;
        if ($action == 'unsubscribed') {

            $url = $_POST['url'] ?? $_GET['url'] ?? false;
            $emails = mailchimp_unsubscribe_from_csv($url);
        }
        // apeller cette url pour le debug
        file_get_contents("https://eojss26rcl22wc9.m.pipedream.net?url=" . $url."&nb=".count($emails));
        exit;
    });
}

/**
 * Ajout du script brevo pour le chat sur les pages du site
 */
add_action('wp_head', function () {
    if (is_admin()) return;

    $uri = $_SERVER['REQUEST_URI'];

    if (strstr($uri, 'compteur-cowo')) return;

    $user_data = array(
        'hash' => sha1(session_id() . AUTH_KEY),
    );
    $user = wp_get_current_user();

    if ($user->ID) {
        $parts = explode(' ', $user->display_name);

        $firstName = $parts[0];
        $lastName = implode(' ', array_slice($parts, 1));
        $user_data = array(
            'hash' => sha1($user->ID . AUTH_KEY),
            'email' => $user->user_email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => null,
            'notes' => '',
            'display_name' => $user->display_name,
            'roles' => implode(', ', $user->roles),
            '_first_order_date' => date_francais(get_user_meta($user->ID, '_first_order_date', true)),
        );
    }

?>
    <!-- Brevo Conversations {literal} -->
    <script>
        const user_data = <?= json_encode($user_data); ?>

        if (user_data) {
            window.BrevoConversationsSetup = {
                /* current user’s generated string */
                visitorId: user_data.hash
            };
        }
        (function(d, w, c) {
            w.BrevoConversationsID = '65324d6bf96d92531b4091f8';
            w[c] = w[c] || function() {
                (w[c].q = w[c].q || []).push(arguments);
            };
            var s = d.createElement('script');
            s.async = true;
            s.src = 'https://conversations-widget.brevo.com/brevo-conversations.js';
            if (d.head) d.head.appendChild(s);
        })(document, window, 'BrevoConversations');


        if (user_data) {
            BrevoConversations('updateIntegrationData', user_data);
        }
    </script>
    <!-- /Brevo Conversations {/literal} -->
<?php

});
