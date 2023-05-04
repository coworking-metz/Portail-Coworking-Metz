<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyFbiaAdminGeneral')) {
    class daftplugInstantifyFbiaAdminGeneral {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;
        public $pluginUploadDir;

        public $settings;

    	public function __construct($config) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];
            $this->pluginUploadDir = $config['plugin_upload_dir'];

            $this->settings = $config['settings'];

            add_action('add_meta_boxes', array($this, 'addMetaBoxes'), 10, 2);
            add_action('save_post', array($this, 'saveMetaBox'), 10, 2);
    	}

        public function renderMetaBoxContent($post, $callbackArgs) {
            $fbia = get_post_meta($post->ID, 'fbia', true);
            wp_nonce_field("{$this->optionName}_articles_meta_nonce", "{$this->optionName}_articles_meta_nonce");
            ?>
            <div class="daftplugAdminMetabox">
                <div class="daftplugAdminField">
                    <label for="fbia" class="daftplugAdminField_label -flex9"><?php esc_html_e('Exclude from Instant Articles', $this->textDomain); ?></label>
                    <label class="daftplugAdminInputCheckbox -flexAuto">
                        <input type="checkbox" name="fbia" id="fbia" class="daftplugAdminInputCheckbox_field" value="exclude" <?php checked($fbia, 'exclude'); ?>>
                    </label>
                </div>
            </div>
            <?php
        }

        public function addMetaBoxes($postType, $post)  {
            if (in_array($post->post_type, (array)daftplugInstantify::getSetting('fbiaOnPostTypes'))) {
                add_meta_box("{$this->optionName}_articles_meta_box", esc_html__('FBIA', $this->textDomain), array($this, 'renderMetaBoxContent'), $postType, 'side', 'default', array());
            }
        }

        public function saveMetaBox($postId) {
            $isAutosave = wp_is_post_autosave($postId);
            $isRevision = wp_is_post_revision($postId);
            $isValidNonce = (isset($_POST["{$this->optionName}_articles_meta_nonce"]) && wp_verify_nonce($_POST["{$this->optionName}_articles_meta_nonce"], $this->pluginBasename)) ? 'true' : 'false';

            if ($isAutosave || $isRevision || !$isValidNonce) {
                return;
            }

            if (isset($_POST['fbia'])) {
                $fbia = $_POST['fbia'];
            } else {
                $fbia = 'include';
            }
            
            update_post_meta($postId, 'fbia', $fbia);
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
    }
}