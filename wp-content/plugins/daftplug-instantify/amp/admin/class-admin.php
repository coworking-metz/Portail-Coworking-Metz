<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpAdmin')) {
    class daftplugInstantifyAmpAdmin {
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

        public $daftplugInstantifyAmpAdminGeneral;
        public $daftplugInstantifyAmpAdminCompatibility;
        public $daftplugInstantifyAmpAdminAdvertisements;
        public $daftplugInstantifyAmpAdminAnalytics;
        public $daftplugInstantifyAmpAdminGdprconsent;

    	public function __construct($config, $daftplugInstantifyAmpAdminGeneral, $daftplugInstantifyAmpAdminCompatibility, $daftplugInstantifyAmpAdminAdvertisements, $daftplugInstantifyAmpAdminAnalytics, $daftplugInstantifyAmpAdminGdprconsent) {
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

            $this->daftplugInstantifyAmpAdminGeneral = $daftplugInstantifyAmpAdminGeneral;
            $this->daftplugInstantifyAmpAdminCompatibility = $daftplugInstantifyAmpAdminCompatibility;
            $this->daftplugInstantifyAmpAdminAdvertisements = $daftplugInstantifyAmpAdminAdvertisements;
            $this->daftplugInstantifyAmpAdminAnalytics = $daftplugInstantifyAmpAdminAnalytics;
            $this->daftplugInstantifyAmpAdminGdprconsent = $daftplugInstantifyAmpAdminGdprconsent;

            add_action('admin_enqueue_scripts', array($this, 'loadAssets'));
            remove_action('admin_notices', 'amp_enhancer_admin_notice');
            AMP_Options_Manager::update_option('plugin_configured', 'true');
    	}

        public function loadAssets($hook) {
            if ($hook && $hook == $this->menuId) {
                $this->dependencies[] = 'jquery';
                $this->dependencies[] = "{$this->slug}-admin";

                wp_enqueue_code_editor(array('type' => 'text/css'));

                wp_enqueue_style("{$this->slug}-amp-admin", plugins_url('amp/admin/assets/css/style-amp.min.css', $this->pluginFile), array(), $this->version);
                wp_enqueue_script("{$this->slug}-amp-admin", plugins_url('amp/admin/assets/js/script-amp.min.js', $this->pluginFile), $this->dependencies, $this->version, true);
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
                    'id' => 'compatibility',
                    'title' => esc_html__('Compatibility', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-compatibility.php')),
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
                ),
                array(
                    'id' => 'gdprconsent',
                    'title' => esc_html__('GDPR Consent', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-gdprconsent.php'))
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
                        <a class="daftplugAdminSubmenu_link" href="#/amp-<?php esc_html_e($subpage['id']); ?>/" data-subpage="<?php esc_html_e($subpage['id']); ?>">
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