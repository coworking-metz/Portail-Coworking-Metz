<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyFbia')) {
    class daftplugInstantifyFbia {
        public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;
        public $pluginFile;
        public $pluginBasename;

        public $feedUrl;

        public $daftplugInstantifyFbiaTransformer;

        public $daftplugInstantifyFbiaPublic;
        public $daftplugInstantifyFbiaPublicGeneral;
        public $daftplugInstantifyFbiaPublicFeed;
        public $daftplugInstantifyFbiaPublicAdvertisements;
        public $daftplugInstantifyFbiaPublicAnalytics;

        public $daftplugInstantifyFbiaAdmin;
        public $daftplugInstantifyFbiaAdminGeneral;
        public $daftplugInstantifyFbiaAdminFeed;
        public $daftplugInstantifyFbiaAdminAdvertisements;
        public $daftplugInstantifyFbiaAdminAnalytics;

        public function __construct($config) {
            $this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];
            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->feedUrl = trailingslashit(strtok(home_url('/', 'https'), '?')).'feed/instant-articles';

            require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/libs/vendor/autoload.php');

            if (daftplugInstantify::isPublic()) {
                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-public.php');
                $this->daftplugInstantifyFbiaPublic = new daftplugInstantifyFbiaPublic($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-general.php');
                $this->daftplugInstantifyFbiaPublicGeneral = new daftplugInstantifyFbiaPublicGeneral($config, $this->daftplugInstantifyFbiaPublic);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-feed.php');
                $this->daftplugInstantifyFbiaPublicFeed = new daftplugInstantifyFbiaPublicFeed($config, $this->daftplugInstantifyFbiaPublic);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-advertisements.php');
                $this->daftplugInstantifyFbiaPublicAdvertisements = new daftplugInstantifyFbiaPublicAdvertisements($config, $this->daftplugInstantifyFbiaPublic);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-analytics.php');
                $this->daftplugInstantifyFbiaPublicAnalytics = new daftplugInstantifyFbiaPublicAnalytics($config, $this->daftplugInstantifyFbiaPublic);
            }

            if (daftplugInstantify::isAdmin()) {
                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-general.php');
                $this->daftplugInstantifyFbiaAdminGeneral = new daftplugInstantifyFbiaAdminGeneral($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-feed.php');
                $this->daftplugInstantifyFbiaAdminFeed = new daftplugInstantifyFbiaAdminFeed($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-advertisements.php');
                $this->daftplugInstantifyFbiaAdminAdvertisements = new daftplugInstantifyFbiaAdminAdvertisements($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-analytics.php');
                $this->daftplugInstantifyFbiaAdminAnalytics = new daftplugInstantifyFbiaAdminAnalytics($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php');
                $this->daftplugInstantifyFbiaAdmin = new daftplugInstantifyFbiaAdmin($config, $this->daftplugInstantifyFbiaAdminGeneral, $this->daftplugInstantifyFbiaAdminFeed, $this->daftplugInstantifyFbiaAdminAdvertisements, $this->daftplugInstantifyFbiaAdminAnalytics);
            }
        }

        public static function returnNotEmptyHtml($first, $html, $second) {
            if (isset($html) && !empty($html)) {
                return $first.$html.$second;
            }
        }

        public function getArticleCount() {
			if (filter_var($this->feedUrl, FILTER_VALIDATE_URL) !== false) {
				$feed = @simplexml_load_file($this->feedUrl, null, LIBXML_NOBLANKS);
				$articlesCount = count($feed->channel->item);
			} else {
			    $articlesCount = '0';
			}

            return $articlesCount;
        }
    }
}