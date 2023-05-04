<?php 

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit;

$optionName = 'daftplug_instantify';
$transients = array('installer_analytics', 'subscriber_analytics');
$options = array('purchase_code', 'installed_devices', 'subscribed_devices', 'settings');

if (get_option("{$optionName}_purchase_code")) {
    $params = array(
        'sslverify' => false,
        'body' => array(
            'action' => 'deactivate',
            'purchase_code' => get_option("{$optionName}_purchase_code")
        ),
        'user-agent' => 'WordPress/'.get_bloginfo('version').'; '.get_bloginfo('url')
    );
        
    wp_remote_post('https://daftplug.com/wp-json/daftplugify/purchase-verify/', $params);
}

if (get_option("{$optionName}_settings")['uninstallSettings'] == 'delete') {   
    foreach ($transients as $transient) {
        delete_transient("{$optionName}_{$transient}");
    }

    foreach ($options as $option) {
        delete_option("{$optionName}_{$option}");
    }
}

if (function_exists('plwp_drop_data')) {
    plwp_drop_data();
}