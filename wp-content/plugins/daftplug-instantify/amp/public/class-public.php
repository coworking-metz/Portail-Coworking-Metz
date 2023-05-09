<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpPublic')) {
    class daftplugInstantifyAmpPublic {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        protected $dependencies;

        public $settings;

        public $partials;

    	public function __construct($config) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->dependencies = array();

            $this->settings = $config['settings'];

            $this->partials = $this->generatePartials();

            add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
    	}

        public function loadAssets() {
            wp_enqueue_style("{$this->slug}-amp-public", plugins_url('amp/public/assets/css/style-amp.min.css', $this->pluginFile), array(), $this->version);
        }

    	public function generatePartials() {
            $partials = array(
                'googleAnalytics' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-googleanalytics.php')),
                'facebookPixel' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-facebookpixel.php')),
                'segmentAnalytics' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-segmentanalytics.php')),
                'statCounter' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-statcounter.php')),
                'histatsAnalytics' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-histatsanalytics.php')),
                'yandexMetrika' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-yandexmetrika.php')),
                'chartbeatAnalytics' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-chartbeatanalytics.php')),
                'clickyAnalytics' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-clickyanalytics.php')),
                'alexaMetrics' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-alexametrics.php')),
                'cookieNotice' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-cookienotice.php')),
            );

            return $partials;
    	}
    }
}