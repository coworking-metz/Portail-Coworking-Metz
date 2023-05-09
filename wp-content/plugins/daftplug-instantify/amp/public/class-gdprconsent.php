<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpPublicGdprconsent')) {
    class daftplugInstantifyAmpPublicGdprconsent {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $settings;

        public $daftplugInstantifyAmpPublic;

    	public function __construct($config, $daftplugInstantifyAmpPublic) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->settings = $config['settings'];

            $this->daftplugInstantifyAmpPublic = $daftplugInstantifyAmpPublic;

            if (daftplugInstantify::getSetting('ampCookieNotice') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectCookieNotice'));
            }
    	}

		public function injectCookieNotice() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['cookieNotice']);
            } else {
                return false;
            }
		}
    }
}