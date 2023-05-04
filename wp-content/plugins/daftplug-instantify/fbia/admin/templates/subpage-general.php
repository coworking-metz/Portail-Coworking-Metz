<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -general -flex12" data-subpage="general" data-title="<?php esc_html_e('General', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="fbiaGeneralFacebookPage">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Facebook Page', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Connect your Facebook page to the instant articles by getting the numeric ID of your page and entering it in the field below.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php _e('Enter the numeric ID of your Facebook page. If you have trouble finding it use <a class="daftplugAdminLink" href="https://lookup-id.com/" target="_blank">this service</a> to get it.', $this->textDomain); ?></p>
                        <label for="fbiaPageId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Facebook Page ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" pattern="[0-9]+" title="<?php esc_html_e('Facebook page ID must contain only numbers.', $this->textDomain); ?>" name="fbiaPageId" id="fbiaPageId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('fbiaPageId'); ?>" data-placeholder="<?php esc_html_e('Facebook Page ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="fbiaGeneralSupport">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('FBIA Support', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You can select which post types to be included in your Instant Articles Feed. Select the post types where you have rich-text informative articles.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select particular post types for Instant Articles.', $this->textDomain); ?></p>
                        <label for="fbiaOnPostTypes" class="daftplugAdminField_label -flex4"><?php esc_html_e('Supported Post Types', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="fbiaOnPostTypes" id="fbiaOnPostTypes" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Supported Post Types', $this->textDomain); ?>" autocomplete="off" required>
                                <?php foreach (array_map('get_post_type_object', $this->daftplugInstantifyFbiaAdminGeneral->getPostTypes()) as $postType) { ?>
                                    <option value="<?php echo $postType->name; ?>" <?php selected(true, in_array($postType->name, (array)daftplugInstantify::getSetting('fbiaOnPostTypes'))); ?>><?php echo $postType->label; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <div class="daftplugAdminSettings_submit">
                    <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Save Settings', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Saving', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Settings Saved', $this->textDomain); ?>" data-failed="<?php esc_html_e('Saving Failed', $this->textDomain); ?>"></button>
                </div>
            </form>
        </div>
    </div>
</div>