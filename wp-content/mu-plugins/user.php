<?php


add_action('profile_update', function ($user_id, $old_user_data) {
    $webhook_url = 'https://tickets.coworking-metz.fr/api/sync-user-webhook?key=bupNanriCit1&wpUserId=' . $user_id;

    $response = wp_remote_post($webhook_url, array(
        'method'    => 'POST',
    ));
    if (is_wp_error($response)) {
        // Handle error accordingly
        error_log('Error calling webhook: ' . $response->get_error_message());
    }
}, 99, 2);
