<?php

add_filter('get_avatar', function ($avatar, $id_or_email, $size, $default, $alt) {
    // Try to get user ID from the $id_or_email variable
    $user_id = is_numeric($id_or_email) ? $id_or_email : false;
    if (!$user_id && is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $user_id = $id_or_email->user_id;
        }
    } elseif (is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        $user_id = $user ? $user->ID : false;
    }

    if ($user_id) {
        $avatar_url = get_user_photo($user_id);
        $avatar = "<img src='{$avatar_url}' alt='{$alt}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    }

    return $avatar;
}, 10, 5);
