<?php
/*
if (isset($_GET['sync-brevo'])) {
    add_action('admin_init', function () {


        // Fetch all users
        $users = get_users(array('fields' => 'user_email'));

        // Convert emails to the format expected by SendinBlue
        $data = array(
            'emails' => $users
        );

        // SendinBlue API endpoint and your API key
        $url = 'https://api.brevo.com/v3/contacts';
        $api_key = BREVO_KEY;

        // API request setup
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'api-key' => $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($data),
        );

        // Make the API call
        $response = wp_remote_request($url, $args);

        // Check for errors
        if (is_wp_error($response)) {
            // Handle error
            error_log($response->get_error_message());
        }
    });
}
*/
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
