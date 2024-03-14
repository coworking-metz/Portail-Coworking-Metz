<?php


add_action('init', function () {
    if (is_admin()) return;

    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/notifications/notifications.css');
    wp_enqueue_style('notifications', '/wp-content/mu-plugins/notifications/notifications.css', array(), $t, false);
    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/notifications/notifications.js');
    wp_enqueue_script('notifications', '/wp-content/mu-plugins/notifications/notifications.js', array(), $t, false);
});



function generateNotification($data)
{

	$GLOBALS['notification'] = $GLOBALS['notification']??0;

	if($GLOBALS['notification']) return;
	$GLOBALS['notification']++;
    $cta = '';
    if ($data['cta']) {
        $cta = '<span><a href="' . $data['cta']['url'] . '" class="button">' . $data['cta']['caption'] . '</a></span>';
    }
    return '<div class="notification" role="alert" data-type="'.($data['type']??'default').'">
    <div>
    <div>
    <figure><img src="' . $data['image'] . '"></figure>
    <p><b class="titre">' . $data['titre'] . '</b><span>' . $data['texte'] . '</span></p>
    </div>
    ' . $cta . '
    </div>
    <button>&#x2716;</button>
  </div>';
}
