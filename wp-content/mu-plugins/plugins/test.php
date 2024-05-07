<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {
        
        $ret = get_transient('getNbVisites');
			var_dump($ret);
			exit;
        if ($ret === false) {
            m('save');
            $ret = count(fetch_users_with_future_visite());
            set_transient('getNbVisites', $ret, HOUR_IN_SECONDS);
        }
        m($ret);
        exit;
    });
}
