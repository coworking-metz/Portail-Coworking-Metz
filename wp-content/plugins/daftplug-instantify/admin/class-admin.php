<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAdmin')) {
    class daftplugInstantifyAdmin {
        public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;
        public $pluginFile;
        public $pluginBasename;
        public $verifyUrl;
        public $itemId;
        public $website;
        public $menuTitle;
        public $menuIcon;
        public $menuId;
        protected $dependencies;
        public $purchaseCode;
        public $capability;
        public $settings;
        public $defaultSettings;
        public $daftplugInstantifyPwa;
        public $daftplugInstantifyAmp;
        public $daftplugInstantifyFbia;
        public $pages;

        public function __construct($config, $defaultSettings, $daftplugInstantifyPwa, $daftplugInstantifyAmp, $daftplugInstantifyFbia) {
            $this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];
            
            $this->verifyUrl = $config['verify_url'];
            $this->itemId = $config['item_id'];

            $this->website = parse_url(site_url(), PHP_URL_HOST);

            $this->menuTitle = $config['menu_title'];
            $this->menuIcon = $config['menu_icon'];

            $this->dependencies = array();

            $this->purchaseCode = get_option("{$this->optionName}_purchase_code");

            $this->capability = 'manage_options';

            $this->settings = $config['settings'];

            $this->defaultSettings = $defaultSettings;

            $this->daftplugInstantifyPwa = $daftplugInstantifyPwa;
            $this->daftplugInstantifyAmp = $daftplugInstantifyAmp;
            $this->daftplugInstantifyFbia = $daftplugInstantifyFbia;

            $this->pages = $this->generatePages();

            add_action('admin_menu', array($this, 'addMenuPage'));
            add_action('admin_enqueue_scripts', array($this, 'loadAssets'));
            add_action('admin_print_scripts-post-new.php', array($this, 'loadMetaboxAssets'), 11);
			add_action('admin_print_scripts-post.php', array($this, 'loadMetaboxAssets'), 11);
            add_action("wp_ajax_{$this->optionName}_activate_license", array($this, 'activateLicense'));
            add_action("wp_ajax_{$this->optionName}_deactivate_license", array($this, 'deactivateLicense'));
            add_action("wp_ajax_{$this->optionName}_send_ticket", array($this, 'sendTicket'));
            add_action("wp_ajax_{$this->optionName}_save_settings", array($this, 'saveSettings'));
            add_action("admin_post_{$this->optionName}_export_settings", array($this, 'exportSettings'));
            add_action("wp_ajax_{$this->optionName}_import_settings", array($this, 'importSettings'));
            add_action("wp_ajax_{$this->optionName}_reset_settings", array($this, 'resetSettings'));
            add_filter('pre_set_site_transient_update_plugins', array($this, 'checkUpdate'));
            add_filter('plugins_api', array($this, 'checkInfo'), 10, 3);
        }

        public function addMenuPage() {
            $this->menuId = add_menu_page($this->menuTitle, $this->menuTitle, $this->capability, $this->slug, array($this, 'getPage'), $this->menuIcon);
        }

        public function loadAssets($hook) {
            if ($hook && $hook == $this->menuId) {
                $this->dependencies[] = 'jquery';

                wp_enqueue_script("{$this->slug}-chart", plugins_url('admin/assets/js/script-chart.js', $this->pluginFile), array(), $this->version, true);
                $this->dependencies[] = "{$this->slug}-chart";

                wp_enqueue_style("{$this->slug}-intro", plugins_url('admin/assets/css/style-intro.css', $this->pluginFile), array(), $this->version);
                wp_enqueue_script("{$this->slug}-intro", plugins_url('admin/assets/js/script-intro.js', $this->pluginFile), $this->dependencies, $this->version, true);
                $this->dependencies[] = "{$this->slug}-intro";

                wp_enqueue_script("{$this->slug}-jscolor", plugins_url('admin/assets/js/script-jscolor.js', $this->pluginFile), $this->dependencies, $this->version, true);
                $this->dependencies[] = "{$this->slug}-jscolor";

                wp_enqueue_style("{$this->slug}-admin", plugins_url('admin/assets/css/style-admin.min.css', $this->pluginFile), array(), $this->version);
                wp_enqueue_script("{$this->slug}-admin", plugins_url('admin/assets/js/script-admin.min.js', $this->pluginFile), $this->dependencies, $this->version, true);

                // WP media
                wp_enqueue_media();

                // Remove emoji
                remove_action('admin_print_scripts', 'print_emoji_detection_script');
                remove_action('admin_print_styles', 'print_emoji_styles');

                // Pass PHP variables in JS
                wp_localize_script("{$this->slug}-admin", "{$this->optionName}_admin_js_vars", apply_filters("{$this->optionName}_admin_js_vars", array(
                    'generalError' => esc_html__('An unexpected error occurred', $this->textDomain),
                    'homeUrl' => trailingslashit(strtok(home_url('/', 'https'), '?')),
                    'adminUrl' => trailingslashit(strtok(admin_url('/', 'https'), '?')),
                    'fileIcon' => plugins_url('admin/assets/img/icon-file.png', $this->pluginFile),
                    'settings' => $this->settings,
                )));
            }
        }

        public function loadMetaboxAssets() {
            global $post;

            if ((daftplugInstantify::getSetting('pwa') == 'on' && (daftplugInstantify::getSetting('pwaOnAll') == 'on' || in_array($post->post_type, (array)daftplugInstantify::getSetting('pwaOnPostTypes')))) || (daftplugInstantify::getSetting('fbia') == 'on' && in_array($post->post_type, (array)daftplugInstantify::getSetting('fbiaOnPostTypes')))) {
                wp_enqueue_editor();
                wp_enqueue_style("{$this->slug}-metabox", plugins_url('/admin/assets/css/style-metabox.min.css', $this->pluginFile), array(), $this->version);
                wp_enqueue_script("{$this->slug}-metabox", plugins_url('/admin/assets/js/script-metabox.min.js', $this->pluginFile), array('jquery'), $this->version, true);
            }
    	}

        public function generatePages() {
            $pages = array(
                array(
                    'id' => 'overview',
                    'title' => esc_html__('Overview', $this->textDomain),
                    'menuTitle' => esc_html__('Overview', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-overview.php'))
                ),
                array(
                    'id' => 'pwa',
                    'title' => esc_html__('Progressive Web Apps', $this->textDomain),
                    'menuTitle' => esc_html__('PWA', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-pwa.php')),
                ),
                array(
                    'id' => 'amp',
                    'title' => esc_html__('Google AMP', $this->textDomain),
                    'menuTitle' => esc_html__('AMP', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-amp.php')),
                ),
                array(
                    'id' => 'fbia',
                    'title' => esc_html__('Facebook Instant Articles', $this->textDomain),
                    'menuTitle' => esc_html__('FBIA', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-fbia.php')),
                ),
                array(
                    'id' => 'settings',
                    'title' => __('Settings', $this->textDomain),
                    'menuTitle' => esc_html__('Settings', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-settings.php'))
                ),
                array(
                    'id' => 'support',
                    'title' => esc_html__('Support', $this->textDomain),
                    'menuTitle' => esc_html__('Support', $this->textDomain),
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-support.php'))
                ),
                array(
                    'id' => 'activation',
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-activation.php'))
                ),
                array(
                    'id' => 'error',
                    'template' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'page-error.php'))
                )
            );

            return $pages;
        }

        public function getPage() {
            ?>
            <div class="daftplugAdmin" data-daftplug-plugin="<?php echo $this->optionName ?>">
                <div class="daftplugAdminLoader" data-size="50px" data-duration="700ms"></div>
                <?php
                include_once(plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'header.php')));
                if ($this->purchaseCode) {
                ?>
                <main class="daftplugAdminMain -disabled" role="main">
                    <nav class="daftplugAdminMenu">
                        <ul class="daftplugAdminMenu_list">
                            <?php
                            foreach ($this->pages as $page) {
                                if (!empty($page['menuTitle'])) {
                                    ?>
                                    <li class="daftplugAdminMenu_item -<?php esc_html_e($page['id']); ?>">
                                        <a class="daftplugAdminMenu_link" href="#/<?php esc_html_e($page['id']); ?>/" data-page="<?php esc_html_e($page['id']); ?>">
                                            <svg class="daftplugAdminMenu_icon">
                                                <use href="#icon<?php echo ucfirst($page['id']); ?>"></use>
                                            </svg>
                                            <?php esc_html_e($page['menuTitle']); ?>
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </nav>
                    <section class="daftplugAdminPages">
                        <?php
                        foreach ($this->pages as $page) {
                            if (!in_array('activation', $page)) {
                                include_once($page['template']);
                            }
                        }
                        ?> 
                    </section>
                </main>
                <?php
                } else {
                    foreach ($this->pages as $page) {
                        if (in_array('activation', $page)) {
                            include_once($page['template']);   
                        }
                    }
                }
                include_once(plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('templates', 'footer.php'))); 
                ?>
            </div>
            <?php
        }

        public function activateLicense() {
            $nonce = $_POST['nonce'];
            $purchaseCode = trim($_POST['purchaseCode']);

            if (!wp_verify_nonce($nonce, "{$this->optionName}_activate_license_nonce")) {
                exit;
            }
            
            $verify = daftplugInstantify::handleLicense($purchaseCode, 'verify');

            if ($verify->verification->valid) {
                update_option("{$this->optionName}_purchase_code", $purchaseCode);
                wp_die('1');
            } else {
                delete_option("{$this->optionName}_purchase_code");
                wp_die($verify->error);
            }
        }

        public function deactivateLicense() {
            $nonce = $_POST['nonce'];
            $purchaseCode = trim($this->purchaseCode);

            if (!wp_verify_nonce($nonce, "{$this->optionName}_deactivate_license_nonce")) {
                exit;
            }

            $verify = daftplugInstantify::handleLicense($purchaseCode, 'deactivate');

            if ($verify->verification->valid) {
                delete_option("{$this->optionName}_purchase_code");
                wp_die('1');
            } else {
                wp_die($verify->error);
            }
        }

        public function sendTicket() {
            $nonce = $_POST['nonce'];
            $purchaseCode = trim($_POST['purchaseCode']);
            $firstName = $_POST['firstName'];
            $contactEmail = $_POST['contactEmail'];
            $problemDescription = $_POST['problemDescription'];
            $attachment1 = $_POST['attachment1'];
            $attachment2 = $_POST['attachment2'];
            $attachment3 = $_POST['attachment3'];
            $attachment4 = $_POST['attachment4'];
            $attachment5 = $_POST['attachment5'];
            $wordpressUsername = $_POST['wordpressUsername'];
            $wordpressPassword = $_POST['wordpressPassword'];
            $website = $this->website;

            $headers = 'From: Support Ticket <'.$contactEmail.'>'. "\r\n";
            $headers .= 'Reply-To: '.$firstName.' <'.$contactEmail.'>'. "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

            $message = 'This email is the response of the submitted support ticket from '.$this->name.' Plugin with the following details:<br><br>';

            $message .= 'PHP Version: '.PHP_VERSION.'<br>';

            $message .= 'Plugin Version: '.$this->version.'<br>';

            if (!empty($purchaseCode)) {
                $message .= 'Purchase Code: '.$purchaseCode.'<br>';
            }

            if (!empty($website)) {
                $message .= 'Website: '.$website.'<br>';
            }

            if (!empty($firstName)) {
                $message .= 'First Name: '.$firstName.'<br>';
            }

            if (!empty($contactEmail)) {
                $message .= 'Contact Email: '.$contactEmail.'<br>';
            }

            if (!empty($problemDescription)) {
                $message .= 'Problem Description: '.$problemDescription.'<br>';
            }

            $message .= 'Login URL: '. wp_login_url().'<br>';

            if (!empty($wordpressUsername)) {
                $message .= 'WordPress Username: '.$wordpressUsername.'<br>';
            }

            if (!empty($wordpressPassword)) {
                $message .= 'WordPress Password: '.$wordpressPassword.'<br>';
            }

            $attachments = array();
            if (!empty($attachment1)) {
                $attachments[] = array(
                    'string' => base64_decode(explode(',', $attachment1)[1]),
                    'name' => 'attachment1.'.explode('/', mime_content_type($attachment1))[1],
                    'encoding' => 'base64',
                    'type' => mime_content_type($attachment1),
                    'disposition' => 'attachment',
                );
            }

            if (!empty($attachment2)) {
                $attachments[] = array(
                    'string' => base64_decode(explode(',', $attachment2)[1]),
                    'name' => 'attachment1.'.explode('/', mime_content_type($attachment2))[1],
                    'encoding' => 'base64',
                    'type' => mime_content_type($attachment2),
                    'disposition' => 'attachment',
                );
            }

            if (!empty($attachment3)) {
                $attachments[] = array(
                    'string' => base64_decode(explode(',', $attachment3)[1]),
                    'name' => 'attachment1.'.explode('/', mime_content_type($attachment3))[1],
                    'encoding' => 'base64',
                    'type' => mime_content_type($attachment3),
                    'disposition' => 'attachment',
                );
            }

            if (!empty($attachment4)) {
                $attachments[] = array(
                    'string' => base64_decode(explode(',', $attachment4)[1]),
                    'name' => 'attachment1.'.explode('/', mime_content_type($attachment4))[1],
                    'encoding' => 'base64',
                    'type' => mime_content_type($attachment4),
                    'disposition' => 'attachment',
                );
            }

            if (!empty($attachment5)) {
                $attachments[] = array(
                    'string' => base64_decode(explode(',', $attachment5)[1]),
                    'name' => 'attachment1.'.explode('/', mime_content_type($attachment5))[1],
                    'encoding' => 'base64',
                    'type' => mime_content_type($attachment5),
                    'disposition' => 'attachment',
                );
            }

            $verify = daftplugInstantify::handleLicense($purchaseCode, 'verify');

            if (wp_verify_nonce($nonce, "{$this->optionName}_support_ticket_nonce") && $verify->verification->valid) {
                $sent = wp_mail('support@daftplug.com', "[$this->name] New support ticket from {$firstName}", $message, $headers, $attachments);
                if ($sent) {
                    wp_die('1');
                } else {
                    wp_die('0');
                }
            } else {
                wp_die('0');
            }
        }

        public function saveSettings() {
            if (!current_user_can($this->capability)) {
                return false;
            }

            $nonce = $_POST['nonce'];
            $newSettings = json_decode(stripslashes($_POST['settings']), true);
            $settings = wp_parse_args($newSettings, $this->settings);
            $saved = update_option("{$this->optionName}_settings", $settings);

            if ($saved && wp_verify_nonce($nonce, "{$this->optionName}_settings_nonce")) {
                wp_die('1');
            } else {
                wp_die('0');
            }
        }
        
        public function exportSettings() {
            $fileName = sprintf('%s-settings-%s-%s.json', $this->slug, date('Y-m-d'), uniqid());
            $settings = wp_json_encode($this->settings);
            nocache_headers();
            @header('Content-Type: application/json');
            @header('Content-Disposition: attachment; filename="'.$fileName.'"');
            @header('Content-Transfer-Encoding: binary');
            @header('Content-Length: '.strlen($settings));
            @header('Connection: close');
            echo $settings;
            exit();
        }

        public function importSettings() {
            $nonce = $_POST['nonce'];
            $importedSettings = json_decode(stripslashes($_POST['settings']), true);
            $settings = wp_parse_args($importedSettings, $this->settings);
            $saved = update_option("{$this->optionName}_settings", $settings);

            if ($saved && wp_verify_nonce($nonce, "{$this->optionName}_settings_import_nonce")) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            } 
        }

        public function resetSettings() {
            $nonce = $_POST['nonce'];
            $saved = update_option("{$this->optionName}_settings", $this->defaultSettings);

            if ($saved && wp_verify_nonce($nonce, "{$this->optionName}_settings_reset_nonce")) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            } 
        }

        public function renderNotice() {
            if (daftplugInstantify::getSetting('amp') == 'on' && daftplugInstantify::isAmpPluginActive()) {
                ?>
                <div class="daftplugAdminPage_content -flex10">
                    <div class="daftplugAdminContentWrapper -notice">
                        <div class="daftplugAdminNotice -flexAuto">
                            <div class="daftplugAdminNotice_container">
                                <div class="daftplugAdminNotice_text -flex10"><?php esc_html_e('We detected that you are using a third-party AMP plugin, so AMP features are not active. Please disable all other AMP plugins to enable AMP pages on your website.', $this->textDomain); ?></div>
                                <img class="daftplugAdminNotice_icon -flex2" src="<?php echo plugins_url('admin/assets/img/icon-warning.png', $this->pluginFile)?>"/>
                            </div>
                        </div>    
                    </div>
                </div>
                <?php
            }
        }

        public function getPwaStatus() {
            return array(
                'icon' => array(
                    'title' => esc_html__('Icon', $this->textDomain),
                    'condition' => !empty($this->settings['pwaIcon']),
                    'true' => esc_html__('Your website has the icon.', $this->textDomain),
                    'false' => esc_html__('Web app icon is not selected.', $this->textDomain),
                ),
                'https' => array(
                    'title' => esc_html__('HTTPS', $this->textDomain),
                    'condition' => is_ssl(),
                    'true' => esc_html__('Your site is serverd over HTTPS.', $this->textDomain),
                    'false' => esc_html__('Your site is not using HTTPS.', $this->textDomain),
                ),
            );
        }

        public function checkUpdate($transient) {
            $result = daftplugInstantify::handleLicense($this->purchaseCode, 'update');

            if (!$transient) {
                return false;
            }

            if (empty($transient->response)) {
                $transient->response = array();
            }

            if ($result && empty($result->error) && !empty($result->data) && version_compare($this->version, $result->data->new_version, '<')) {
                $result->data->plugin = $this->pluginBasename;
                $transient->response[$this->pluginBasename] = $result->data;
            }

            return $transient;
        }

        public function checkInfo($result, $action, $args) {
            $result = false;

            if (isset($args->slug) && $args->slug === $this->slug) {
                $info = daftplugInstantify::handleLicense($this->purchaseCode, 'update');

                if (is_object($info) && empty($info->error) && !empty($info->data)) {
                    if (!empty($info->data->sections)) {
                        $info->data->sections = (array)$info->data->sections;
                    }

                    $result = $info->data;
                }
            }

            return $result;
        }
    }
}