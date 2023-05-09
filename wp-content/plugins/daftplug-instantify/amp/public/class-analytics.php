<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpPublicAnalytics')) {
    class daftplugInstantifyAmpPublicAnalytics {
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

            if (daftplugInstantify::getSetting('ampGoogleAnalytics') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectGoogleAnalytics'));
            }

            if (daftplugInstantify::getSetting('ampFacebookPixel') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectFacebookPixel'));
            }

            if (daftplugInstantify::getSetting('ampSegmentAnalytics') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectSegmentAnalytics'));
            }

            if (daftplugInstantify::getSetting('ampStatCounter') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectStatCounter'));
            }

            if (daftplugInstantify::getSetting('ampHistatsAnalytics') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectHistatsAnalytics'));
            }

            if (daftplugInstantify::getSetting('ampYandexMetrika') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectYandexMetrika'));
            }

            if (daftplugInstantify::getSetting('ampChartbeatAnalytics') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectChartbeatAnalytics'));
            }

            if (daftplugInstantify::getSetting('ampClickyAnalytics') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectClickyAnalytics'));
            }

            if (daftplugInstantify::getSetting('ampAlexaMetrics') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectAlexaMetrics'));
            }
    	}

		public function injectGoogleAnalytics() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['googleAnalytics']);
            } else {
                return false;
            }
		}

        public function injectFacebookPixel() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['facebookPixel']);
            } else {
                return false;
            }
        }

        public function injectSegmentAnalytics() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['segmentAnalytics']);
            } else {
                return false;
            }
        }

        public function injectStatCounter() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['statCounter']);
            } else {
                return false;
            }
        }

        public function injectHistatsAnalytics() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['histatsAnalytics']);
            } else {
                return false;
            }
        }

        public function injectYandexMetrika() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['yandexMetrika']);
            } else {
                return false;
            }
        }

        public function injectChartbeatAnalytics() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['chartbeatAnalytics']);
            } else {
                return false;
            }
        }

        public function injectClickyAnalytics() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['clickyAnalytics']);
            } else {
                return false;
            }
        }

        public function injectAlexaMetrics() {
            if (daftplugInstantify::isAmpPage()) {
                include_once($this->daftplugInstantifyAmpPublic->partials['alexaMetrics']);
            } else {
                return false;
            }
        }
    }
}