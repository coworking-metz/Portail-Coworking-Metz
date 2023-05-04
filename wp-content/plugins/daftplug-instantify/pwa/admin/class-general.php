<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyPwaAdminGeneral')) {
    class daftplugInstantifyPwaAdminGeneral {
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

    	public function __construct($config) {
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

            add_action('add_meta_boxes', array($this, 'addMetaBoxes'), 10, 2);
            add_action('save_post', array($this, 'saveMetaBox'), 10, 2);
            add_action("wp_ajax_{$this->optionName}_get_installer_analytics", array($this, 'getInstallerAnalytics'));
            add_action("wp_ajax_{$this->optionName}_get_installer_stats", array($this, 'getInstallerStats'));
    	}

        public function renderMetaBoxContent($post, $callbackArgs) {
            $pwa = get_post_meta($post->ID, 'pwa', true);
            $pwaName = get_post_meta($post->ID, 'pwaName', true);
            $pwaShortName = get_post_meta($post->ID, 'pwaShortName', true);
            $pwaDescription = get_post_meta($post->ID, 'pwaDescription', true);
            $pwaIcon = get_post_meta($post->ID, 'pwaIcon', true);
            wp_nonce_field("{$this->optionName}_pwa_meta_nonce", "{$this->optionName}_pwa_meta_nonce");
            ?>
            <div class="daftplugAdminMetabox">
                <div class="daftplugAdminField">
                    <label for="pwa" class="daftplugAdminField_label -flex8"><?php esc_html_e('Disable PWA on this post', $this->textDomain); ?></label>
                    <label class="daftplugAdminInputCheckbox -flexAuto">
                        <input type="checkbox" name="pwa" id="pwa" class="daftplugAdminInputCheckbox_field" value="disable" <?php checked($pwa, 'disable'); ?>>
                    </label>
                </div>
                <div class="daftplugAdminField -pwaDependentDisableE">
                    <div class="daftplugAdminInputText -flexAuto">
                        <input type="text" name="pwaName" id="pwaName" class="daftplugAdminInputText_field" value="<?php echo $pwaName; ?>" data-placeholder="<?php esc_html_e('Name', $this->textDomain); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="daftplugAdminField -pwaDependentDisableE">
                    <div class="daftplugAdminInputText -flexAuto">
                        <input type="text" name="pwaShortName" id="pwaShortName" class="daftplugAdminInputText_field" maxlength="12" value="<?php echo $pwaShortName; ?>" data-placeholder="<?php esc_html_e('Short Name', $this->textDomain); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="daftplugAdminField -pwaDependentDisableE">
                    <div class="daftplugAdminInputTextarea -flexAuto">
                        <textarea name="pwaDescription" id="pwaDescription" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Description', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4"><?php echo $pwaDescription; ?></textarea>
                    </div>
                </div>
                <div class="daftplugAdminField -pwaDependentDisableE">
                    <div class="daftplugAdminInputUpload -flexAuto">
                        <input type="text" name="pwaIcon" id="pwaIcon" class="daftplugAdminInputUpload_field" value="<?php echo $pwaIcon; ?>" data-mimes="png" data-min-width="512" data-max-width="" data-min-height="512" data-max-height="" data-attach-url="<?php echo wp_get_attachment_image_src($pwaIcon, array(512, 512))[0]; ?>">
                    </div>
                </div>
            </div>
            <?php
        }

        public function addMetaBoxes($postType, $post)  {
            if (daftplugInstantify::getSetting('pwaOnAll') == 'on' || in_array($post->post_type, (array)daftplugInstantify::getSetting('pwaOnPostTypes'))) {
                add_meta_box("{$this->optionName}_pwa_meta_box", esc_html__('PWA', $this->textDomain), array($this, 'renderMetaBoxContent'), $postType, 'side', 'default', array());
            }
        }

        public function saveMetaBox($postId) {
            $isAutosave = wp_is_post_autosave($postId);
            $isRevision = wp_is_post_revision($postId);
            $isValidNonce = (isset($_POST["{$this->optionName}_pwa_meta_nonce"]) && wp_verify_nonce($_POST["{$this->optionName}_pwa_meta_nonce"], $this->pluginBasename)) ? 'true' : 'false';

            if ($isAutosave || $isRevision || !$isValidNonce) {
                return;
            }

            $pwa = (isset($_POST['pwa'])) ? $_POST['pwa'] : 'enable';
            $pwaName = (isset($_POST['pwaName'])) ? $_POST['pwaName'] : '';
            $pwaShortName = (isset($_POST['pwaShortName'])) ? $_POST['pwaShortName'] : '';
            $pwaDescription = (isset($_POST['pwaDescription'])) ? $_POST['pwaDescription'] : '';
            $pwaIcon = (isset($_POST['pwaIcon'])) ? $_POST['pwaIcon'] : '';
            
            update_post_meta($postId, 'pwa', $pwa);
            update_post_meta($postId, 'pwaName', $pwaName);
            update_post_meta($postId, 'pwaShortName', $pwaShortName);
            update_post_meta($postId, 'pwaDescription', $pwaDescription);
            update_post_meta($postId, 'pwaIcon', $pwaIcon);
        }
        
        public function getPostTypes() {
            return array_values(
                        get_post_types(
                            array(
                               'public' => true,
                            ),
                            'names'
                        )
                    );
        }

        public function getPageTypes() {
            if (get_option('show_on_front') === 'page') {
                $pageTypes['is_front_page'] = array(
                    'label'  => __('Homepage', $this->textDomain),
                );

                $pageTypes['is_home'] = array(
                    'label' => __('Blog', $this->textDomain),
                );
            } else {
                $pageTypes['is_home'] = array(
                    'label' => __('Homepage', $this->textDomain),
                );
            }
    
            $pageTypes = array_merge(
                $pageTypes,
                array(
                    'is_author'  => array(
                        'label'  => __('Author', $this->textDomain),
                        'parent' => 'is_archive',
                    ),
                    'is_search'  => array(
                        'label' => __('Search', $this->textDomain),
                    ),
                    'is_404'     => array(
                        'label' => __('Not Found (404)', $this->textDomain),
                    ),
                )
            );
    
            if (taxonomy_exists('category')) {
                $pageTypes['is_category'] = array(
                    'label'  => get_taxonomy('category')->labels->name,
                );
            }

            if (taxonomy_exists('post_tag')) {
                $pageTypes['is_tag'] = array(
                    'label'  => get_taxonomy('post_tag')->labels->name,
                );
            }

            return $pageTypes;
        }

        public function getInstallerAnalytics() {
            $dates = $this->getLastNDays(365);
            $installs = get_transient("{$this->optionName}_installer_analytics");
            $data = array();

            foreach ($dates as $date) {
                if (isset($installs[$date])) {
                    $data[] = $installs[$date];
                } else {
                    $data[] = 0;
                }
            }

            wp_send_json_success(array(
                'data' => $data,
                'dates' => $dates,
            ));
        }

        public function getInstallerStats() {
            $browser = array();
            $device = array();
            $country = array();
            $status = array();

			foreach ($this->installedDevices as $key => $value) {
			    $browser[] = $this->installedDevices[$key]['browser'];
                $device[] = $this->installedDevices[$key]['device'];
                $country[] = $this->installedDevices[$key]['country'];
                $status[] = $this->installedDevices[$key]['user'];
			}

            $browserData = array_count_values($browser);
            $deviceData = array_count_values($device);
            $countryData = array_count_values($country);
            $statusData = array_count_values($status);
            $statusNames = array();
            $statusCount = array();

            if (!empty($statusData)) {
                if (array_key_exists('Unregistered', $statusData) && count($statusData) > 1) {
                    $statusNames[] = 'Unregistered';
                    $statusNames[] = 'Registered';
                    $statusCount[] = $statusData['Unregistered'];
                    $statusCount[] = array_sum(array_diff_key($statusData, array_flip(array('Unregistered'))));
                } elseif (array_key_exists('Unregistered', $statusData) && count($statusData) > 0) {
                    $statusNames[] = 'Unregistered';
                    $statusCount[] = $statusData['Unregistered'];
                } elseif (!array_key_exists('Unregistered', $statusData) && count($statusData) > 0) {
                    $statusNames[] = 'Registered';
                    $statusCount[] = array_sum($statusData);
                }
            }

            wp_send_json_success(array(
                'browserNames' => array_keys($browserData),
                'browserCount' => array_values($browserData),
                'deviceNames' => array_keys($deviceData),
                'deviceCount' => array_values($deviceData),
                'countryNames' => array_keys($countryData),
                'countryCount' => array_values($countryData),
                'statusNames' => $statusNames,
                'statusCount' => $statusCount,
            ));
		}

        public function getLastNDays($days, $format = 'j M Y') {
            $m = date("m"); $de= date("d"); $y= date("Y");
            $dateArray = array();
            for ($i=0; $i<=$days-1; $i++) {
                $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y)); 
            }
            
            return array_reverse($dateArray);
        }
    }
}