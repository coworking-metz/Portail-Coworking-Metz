<?php


if(strstr($_SERVER['HTTP_HOST']??'', '.local')) {

    add_action('init', function() {
        remove_action('wp_version_check', 'wp_version_check');
        remove_action('admin_init', '_maybe_update_core');
      });
      
      // Disable Plugin Updates
      add_filter('site_transient_update_plugins', function($value) {
        return null;
      });
      
      // Optionally, disable Theme Updates as well
      add_filter('site_transient_update_themes', function($value) {
        return null;
      });    

}