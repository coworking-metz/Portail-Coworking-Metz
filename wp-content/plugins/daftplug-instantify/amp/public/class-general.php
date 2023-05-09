<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpPublicGeneral')) {
    class daftplugInstantifyAmpPublicGeneral {
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

            add_action('admin_bar_menu', array($this, 'removeAdminBarMenuItems'), 200);

            if ((!in_array('desktop', (array)daftplugInstantify::getSetting('ampPlatforms')) && daftplugInstantify::isPlatform('desktop'))
            || (!in_array('mobile', (array)daftplugInstantify::getSetting('ampPlatforms')) && daftplugInstantify::isPlatform('mobile'))
            || (!in_array('tablet', (array)daftplugInstantify::getSetting('ampPlatforms')) && daftplugInstantify::isPlatform('tablet'))) {
                add_action('template_redirect', array($this, 'disableAmpPlatforms'));
                add_action('admin_bar_menu', array($this, 'removeAmpAdminBarMenu'), 200);
            }
    	}

        public function removeAdminBarMenuItems($adminBar) {
            if (amp_is_canonical() || !amp_is_available()) {
                return;
            }

            $adminBar->remove_node('amp-settings');
            $adminBar->remove_node('amp-support');
        }

        public function disableAmpPlatforms() {
            if (function_exists('amp_is_request') && amp_is_request()) {
                $ampUrl = amp_get_current_url();
                if ($ampUrl) {
                    $url = amp_remove_endpoint($ampUrl);
                    wp_safe_redirect($url);
                    exit;
                }
            }
        }

        public function removeAmpAdminBarMenu($adminBar) {
            if (amp_is_canonical() || !amp_is_available()) {
                return;
            }

            $adminBar->remove_node('amp');
        }
    }
}