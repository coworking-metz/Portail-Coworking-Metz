<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyPwaAdmin')) {
    class daftplugInstantifyPwaAdmin {
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

        public $daftplugInstantifyPwaAdminGeneral;
        public $daftplugInstantifyPwaAdminAddtohomescreen;
        public $daftplugInstantifyPwaAdminOfflineusage;
        public $daftplugInstantifyPwaAdminAccessibility;
        public $daftplugInstantifyPwaAdminEnhancements;
        public $daftplugInstantifyPwaAdminPushnotifications;

    	public function __construct($config, $daftplugInstantifyPwaAdminGeneral, $daftplugInstantifyPwaAdminAddtohomescreen, $daftplugInstantifyPwaAdminOfflineusage, $daftplugInstantifyPwaAdminAccessibility, $daftplugInstantifyPwaAdminEnhancements, $daftplugInstantifyPwaAdminPushnotifications) {
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

            $this->daftplugInstantifyPwaAdminGeneral = $daftplugInstantifyPwaAdminGeneral;
            $this->daftplugInstantifyPwaAdminAddtohomescreen = $daftplugInstantifyPwaAdminAddtohomescreen;
            $this->daftplugInstantifyPwaAdminOfflineusage = $daftplugInstantifyPwaAdminOfflineusage;
            $this->daftplugInstantifyPwaAdminAccessibility = $daftplugInstantifyPwaAdminAccessibility;
            $this->daftplugInstantifyPwaAdminEnhancements = $daftplugInstantifyPwaAdminEnhancements;
            $this->daftplugInstantifyPwaAdminPushnotifications = $daftplugInstantifyPwaAdminPushnotifications;

            add_action('admin_enqueue_scripts', array($this, 'loadAssets'));
    	}

        public function loadAssets($hook) {
            if ($hook && $hook == $this->menuId) {
                $this->dependencies[] = 'jquery';
                $this->dependencies[] = "{$this->slug}-admin";

                wp_enqueue_style("{$this->slug}-pwa-admin", plugins_url('pwa/admin/assets/css/style-pwa.min.css', $this->pluginFile), array(), $this->version);
                wp_enqueue_script("{$this->slug}-pwa-admin", plugins_url('pwa/admin/assets/js/script-pwa.min.js', $this->pluginFile), $this->dependencies, $this->version, true);
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
                    'id' => 'addtohomescreen',
                    'title' => esc_html__('Add To Home Screen', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-addtohomescreen.php'))
                ),
                array(
                    'id' => 'offlineusage',
                    'title' => esc_html__('Offline Usage', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-offlineusage.php')),
                ),
                array(
                    'id' => 'accessibility',
                    'title' => esc_html__('Accessibility', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-accessibility.php')),
                ),
                array(
                    'id' => 'enhancements',
                    'title' => esc_html__('Enhancements', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-enhancements.php'))
                ),
                array(
                    'id' => 'pushnotifications',
                    'title' => esc_html__('Push Notifications', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'subpage-pushnotifications.php'))
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
                        <a class="daftplugAdminSubmenu_link" href="#/pwa-<?php esc_html_e($subpage['id']); ?>/" data-subpage="<?php esc_html_e($subpage['id']); ?>">
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