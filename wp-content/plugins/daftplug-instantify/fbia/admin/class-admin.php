<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyFbiaAdmin')) {
    class daftplugInstantifyFbiaAdmin {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $menuId;

        protected $dependencies;

        public $settings;

        public $subpages;

        public $daftplugInstantifyFbiaAdminGeneral;
        public $daftplugInstantifyFbiaAdminFeed;
        public $daftplugInstantifyFbiaAdminAdvertisements;
        public $daftplugInstantifyFbiaAdminAnalytics;

    	public function __construct($config, $daftplugInstantifyFbiaAdminGeneral, $daftplugInstantifyFbiaAdminFeed, $daftplugInstantifyFbiaAdminAdvertisements, $daftplugInstantifyFbiaAdminAnalytics) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->menuId = 'toplevel_page_'.$config['slug'];

            $this->dependencies = array();

            $this->settings = $config['settings'];

            $this->subpages = $this->generateSubpages();

            $this->daftplugInstantifyFbiaAdminGeneral = $daftplugInstantifyFbiaAdminGeneral;
            $this->daftplugInstantifyFbiaAdminFeed = $daftplugInstantifyFbiaAdminFeed;
            $this->daftplugInstantifyFbiaAdminAdvertisements = $daftplugInstantifyFbiaAdminAdvertisements;
            $this->daftplugInstantifyFbiaAdminAnalytics = $daftplugInstantifyFbiaAdminAnalytics;

            add_action('admin_enqueue_scripts', array($this, 'loadAssets'));
    	}

        public function loadAssets($hook) {
            if ($hook && $hook == $this->menuId) {
                $this->dependencies[] = 'jquery';
                $this->dependencies[] = "{$this->slug}-admin";

                wp_enqueue_code_editor(array('type' => 'application/json'));
                
                wp_enqueue_style("{$this->slug}-fbia-admin", plugins_url('fbia/admin/assets/css/style-fbia.min.css', $this->pluginFile), array(), $this->version);
                wp_enqueue_script("{$this->slug}-fbia-admin", plugins_url('fbia/admin/assets/js/script-fbia.min.js', $this->pluginFile), $this->dependencies, $this->version, true);
            }
        }

        public function generateSubpages() {
            $subpages = array(
                array(
                    'id' => 'general',
                    'title' => esc_html__('General', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-general.php'))
                ),
                array(
                    'id' => 'feed',
                    'title' => esc_html__('Feed', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-feed.php'))
                ),
                array(
                    'id' => 'advertisements',
                    'title' => esc_html__('Advertisements', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-advertisements.php')),
                ),
                array(
                    'id' => 'analytics',
                    'title' => esc_html__('Analytics', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-analytics.php'))
                )
            );

            return $subpages;
        }

        public function getSubpages() {
            ?>
            <div class="daftplugAdminSubmenu">
                <ul class="daftplugAdminSubmenu_list">
                <?php
                foreach ($this->subpages as $subpage) {
                    ?>
                    <li class="daftplugAdminSubmenu_item -<?php esc_html_e($subpage['id']); ?>">
                        <a class="daftplugAdminSubmenu_link" href="#/fbia-<?php esc_html_e($subpage['id']); ?>/" data-subpage="<?php esc_html_e($subpage['id']); ?>">
                            <?php esc_html_e($subpage['title']); ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
                </ul>
            </div>
            <?php
            foreach ($this->subpages as $subpage) {
                include_once($subpage['template']);
            }
        }
    }
}