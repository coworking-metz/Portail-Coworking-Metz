<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyFbiaPublicAnalytics')) {
    class daftplugInstantifyFbiaPublicAnalytics {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $settings;

        public $daftplugInstantifyFbiaPublic;

    	public function __construct($config, $daftplugInstantifyFbiaPublic) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->settings = $config['settings'];

            $this->daftplugInstantifyFbiaPublic = $daftplugInstantifyFbiaPublic;

            if (daftplugInstantify::getSetting('fbiaAnalytics') == 'on') {
                add_filter("{$this->optionName}_articles_content", array($this, 'injectAnalyticsTag'), 10, 2);
            }
    	}

        public function injectAnalyticsTag($content, $postId) {
            $analyticsCode = daftplugInstantify::getSetting('fbiaAnalyticsCode');
            $content .= '<figure class="op-tracker">
                            <iframe>'.$analyticsCode.'</iframe>
                        </figure>';

            return $content;
        }
    }
}