<?php


add_action('init', function () {
    if (is_admin()) return;

    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/notifications/notifications.css');
    wp_enqueue_style('notifications', '/wp-content/mu-plugins/notifications/notifications.css', array(), $t, false);
    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/notifications/notifications.js');
    wp_enqueue_script('notifications', '/wp-content/mu-plugins/notifications/notifications.js', array(), $t, false);
});

add_action('wp_footer', function () {
    if (is_user_logged_in()) {
        $uid = get_current_user_id();
        if (!polaroid_get($uid, false)) {
            echo generateNotification(['titre'=>'Nouveau polaroïd disponible !', 'texte'=>'<a href="/mon-compte/polaroid/?modifier">Ajoutez une photo pour profiter du nouveau format de polaroïd</a>.', 'image'=>'images/pola-poule-vide.jpg']);
        }
    }
});

function generateNotification($data): string
{
    return '<div class="notification" role="alert">
    <figure><img src="' . $data['image'] . '"></figure>
    <p><b>' . $data['titre'] . '</b><span>' . $data['texte'] . '</span></p>
    <button>&#x2716;</button>
  </div>';
}
