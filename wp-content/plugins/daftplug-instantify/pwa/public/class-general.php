<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyPwaPublicGeneral')) {
    class daftplugInstantifyPwaPublicGeneral {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $settings;
        public $installedDevices;

        public $daftplugInstantifyPwaPublic;

    	public function __construct($config, $daftplugInstantifyPwaPublic) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->settings = $config['settings'];
            $this->installedDevices = get_option("{$this->optionName}_installed_devices", true);

            $this->daftplugInstantifyPwaPublic = $daftplugInstantifyPwaPublic;

            add_action("wp_ajax_{$this->optionName}_save_installer_data", array($this, 'saveInstallerData'));
            add_action("wp_ajax_nopriv_{$this->optionName}_save_installer_data", array($this, 'saveInstallerData'));
    	}

        public function saveInstallerData() {
            $installedDevices = $this->installedDevices;
            $id = uniqid();
            $browser = $_REQUEST['browser'];
            $device = $_REQUEST['device'];
            $date = date('j M Y');
            $country = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.$_SERVER['REMOTE_ADDR']), true);
            $user = (is_user_logged_in() ? get_current_user_id() : 'Unregistered');
            $data = get_transient("{$this->optionName}_installer_analytics");
            $data[(date('j M Y'))] += 1;
            set_transient("{$this->optionName}_installer_analytics", $data, 31556926);

            $installedDevices[$id] = array(
                'id' => $id,
                'browser' => $browser,
                'device' => $device,
                'date' => $date,
                'country' => @$country['geoplugin_countryName'],
                'user' => $user,
            );

            $handled = update_option("{$this->optionName}_installed_devices", $installedDevices);

            if ($handled) {
                wp_die('1');
            } else {
                wp_die('0');
            }
        }
    }
}