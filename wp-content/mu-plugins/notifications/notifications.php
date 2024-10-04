<?php


add_action('init', function () {
    if (is_admin()) return;

    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/notifications/notifications.css');
    wp_enqueue_style('notifications', '/wp-content/mu-plugins/notifications/notifications.css', array(), $t, false);
    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/notifications/notifications.js');
    wp_enqueue_script('notifications', '/wp-content/mu-plugins/notifications/notifications.js', array(), $t, false);
});


if ($_GET['notification'] ?? false) {
    add_action('wp_footer', function () {
        $data = json_decode(stripslashes($_GET['notification']), true);
        $data['time'] = time();
        echo generateNotification($data);
    });
}
function generateNotification($data)
{

    $GLOBALS['notification'] = $GLOBALS['notification'] ?? 0;

    if ($GLOBALS['notification']) return;
    $GLOBALS['notification']++;
    $id = sha1(json_encode($data));
    $cta = '';
    if ($data['cta'] ?? false) {
        $cta = '<span class="cta"><a href="' . $data['cta']['url'] . '" class="button">' . $data['cta']['caption'] . '</a></span>';
    }
    if ($data['temporaire'] ?? false) {
        $data['duree'] = 5;
    }
    return '<div class="notification" role="alert" data-id="' . $id . '" data-type="' . ($data['type'] ?? 'default') . '" data-once="' . (($data['once'] ?? false) ? 'true' : 'false') . '" data-duration="' . ($data['duree'] ?? false) . '" data-storage="' . ($data['storage'] ?? 'session') . '">
    <div>
    <div>
    <figure><img src="' . ($data['image'] ?? '') . '"></figure>
    <p><b class="titre">' . $data['titre'] . '</b><span>' . $data['texte'] . '</span></p>
    </div>
    ' . $cta . '
    </div>
    <button>&#x2716;</button>
  </div>';
}


function wp_redirect_notification($uri, $notification)
{
    wp_redirect($uri . '?notification=' . urlencode(json_encode($notification)));
    exit;
}
