<?php


add_action('admin_bar_menu', function ($admin_bar) {
    if (is_admin()) {

        $hide = ['villatheme_hide_toolbar', 'wp-mail-smtp-menu', 'wp-logo-external'];
        foreach ($hide as $id) {
            $admin_bar->remove_menu($id);
        }
    }
}, PHP_INT_MAX);
