<?php

function get_user_photos($user_id)
{
    $key = 'get_user_photos_' . $user_id;
    $photos = get_transient($key);
    if ($photos) return $photos;

    $api = 'https://photos.coworking-metz.fr/' . $user_id . '.json';
    $photos = file_get_json($api);
    set_transient($key, $photos, 3600 * 24);
    return $photos;
}


function get_user_photo($user_id, $size = 'small')
{
    return get_user_photos($user_id)['photo'][$size] ?? false;
}

function get_user_polaroid($user_id, $size = 'small')
{
    return get_user_photos($user_id)['polaroid'][$size] ?? false;
}
