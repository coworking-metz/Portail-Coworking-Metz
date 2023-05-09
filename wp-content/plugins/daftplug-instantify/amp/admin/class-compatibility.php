<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpAdminCompatibility')) {
    class daftplugInstantifyAmpAdminCompatibility {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public static $pluginBasename;
        public $pluginUploadDir;

        public $settings;

    	public function __construct($config) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            self::$pluginBasename = $config['plugin_basename'];
            $this->pluginUploadDir = $config['plugin_upload_dir'];

            $this->settings = $config['settings'];
    	}

        public static function getSuppressiblePlugins() {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
            $plugins = get_plugins();
            $activePlugins = get_option('active_plugins');
            $activePluginsWithData = array_filter(
                $plugins,
                function($key) use ($activePlugins) {
                    return in_array($key, $activePlugins);
                },
                ARRAY_FILTER_USE_KEY
            );
            unset($activePluginsWithData[self::$pluginBasename]);
            $suppressiblePlugins = array();

            foreach ($activePluginsWithData as $pluginFile => $pluginData) {
                $suppressiblePlugins[substr($pluginFile, 0, strpos($pluginFile, '/'))] = $pluginData;
            }

            return $suppressiblePlugins;
        }

        public static function getSuppressedPlugins() {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
            include_once(ABSPATH.'wp-includes/pluggable.php');
            $suppressiblePlugins = self::getSuppressiblePlugins();
            $suppressedPluginsOption = (array)daftplugInstantify::getSetting('ampPluginSuppressionList');
            $user = wp_get_current_user();
            $suppressedPlugins = array();
            $suppressedPluginsWithData = array_filter(
                $suppressiblePlugins,
                function($key) use ($suppressedPluginsOption) {
                    return in_array($key, $suppressedPluginsOption);
                },
                ARRAY_FILTER_USE_KEY
            );

            foreach ($suppressedPluginsWithData as $pluginFile => $pluginData) {
                $suppressedPlugins[$pluginFile] = array(
                    'last_version' => $pluginData['Version'],
				    'timestamp' => time(),
					'username' => $user instanceof WP_User ? $user->user_nicename : null,
					'erroring_urls' => array(),
                );
            }

            return $suppressedPlugins;
        }
    }
}