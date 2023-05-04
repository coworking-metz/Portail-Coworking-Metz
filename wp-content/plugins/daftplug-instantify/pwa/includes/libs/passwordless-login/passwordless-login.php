<?php

if (!defined('ABSPATH')) {
  die;
}

include_once 'vendor/autoload.php';

define('PLWP_SLUG', 'passwordless-wp');
define('PLWP_FOLDER', plugin_dir_path(__FILE__));
define('PLWP_URL', plugin_dir_url(__FILE__));
define('PLWP_PLUGIN', plugin_basename(__FILE__));

include_once PLWP_FOLDER . 'includes/db.php';
include_once PLWP_FOLDER . 'includes/common.php';
include_once PLWP_FOLDER . 'includes/scripts-login.php';
include_once PLWP_FOLDER . 'includes/editing.php';
include_once PLWP_FOLDER . 'includes/ajax.php';