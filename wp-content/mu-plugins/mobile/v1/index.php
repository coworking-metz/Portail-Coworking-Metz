<?php

/**
 * Modify the payload/ token's data before being encoded & signed.
 *
 * @param array $payload The default payload
 * @param WP_User $user The authenticated user.
 * .
 * @return array The payload/ token's data.
 */
add_filter(
	'jwt_auth_payload',
	function ( $payload, $user ) {
		// Modify the payload here.
    $payload['data']['user'] = array(
      'id' => $user->ID,
      'email' => $user->user_email
    );
		return $payload;
	},
	10,
	2
);

include 'intercom.php';
include 'presence.php';
include 'mobile-profile.php';
