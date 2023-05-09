<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -general -flex12" data-subpage="general" data-title="<?php esc_html_e('General', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="ampGeneralUrlStructure">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('AMP URL Structure', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you are able to choose URL structure used by AMP pages. It determines endpoint type of your AMP URLs.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select AMP URL Structure. We recommend to set it on Query Parameter URL structure.', $this->textDomain); ?></p>
                        <label for="ampUrlStructure" class="daftplugAdminField_label -flex4"><?php esc_html_e('AMP URL Structure', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="ampUrlStructure" id="ampUrlStructure" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('AMP URL Structure', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="queryParameter" <?php selected('queryParameter', daftplugInstantify::getSetting('ampUrlStructure')) ?>><?php esc_html_e('Query Parameter: ?amp=1', $this->textDomain); ?></option>
                                <option value="pathSuffix" <?php selected('pathSuffix', daftplugInstantify::getSetting('ampUrlStructure')) ?>><?php esc_html_e('Path Suffix: /amp/', $this->textDomain); ?></option>
                                <option value="legacyTransitional" <?php selected('legacyTransitional', daftplugInstantify::getSetting('ampUrlStructure')) ?>><?php esc_html_e('Legacy Transitional: ?amp', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampGeneralSupport">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('AMP Support', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you are able to enable or disable AMP support on particular platforms, posts and page types.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms AMP feature should be active and running.', $this->textDomain); ?></p>
                        <label for="ampPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="ampPlatforms" id="ampPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('ampPlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('ampPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('ampPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable AMP on all post types and pages. If turned on, this will allow all content on your site to have a corresponding AMP version. We recommend to enable AMP on all content to serve all pages as AMP.', $this->textDomain); ?></p>
                        <label for="ampOnAll" class="daftplugAdminField_label -flex4"><?php esc_html_e('All Content', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampOnAll" id="ampOnAll" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampOnAll'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampOnAllDependentHideE">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select particular post types where you want AMP version to be available.', $this->textDomain); ?></p>
                        <label for="ampOnPostTypes" class="daftplugAdminField_label -flex4"><?php esc_html_e('Supported Post Types', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="ampOnPostTypes" id="ampOnPostTypes" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Supported Post Types', $this->textDomain); ?>" autocomplete="off" required>
                                <?php
                                foreach (array_map('get_post_type_object', AMP_Post_Type_Support::get_eligible_post_types()) as $postType) {
                                ?>
                                <option value="<?php echo $postType->name; ?>" <?php selected(true, in_array($postType->name, (array)daftplugInstantify::getSetting('ampOnPostTypes'))); ?>><?php echo $postType->label; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampOnAllDependentHideE">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select particular page types where you want AMP version to be available.', $this->textDomain); ?></p>
                        <label for="ampOnPageTypes" class="daftplugAdminField_label -flex4"><?php esc_html_e('Supported Page Types', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="ampOnPageTypes" id="ampOnPageTypes" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Supported Page Types', $this->textDomain); ?>" autocomplete="off" required>
                                <?php
                                foreach (AMP_Theme_Support::get_supportable_templates() as $id => $option) {
                                ?>
                                <option value="<?php echo $id; ?>" <?php selected(true, in_array($id, (array)daftplugInstantify::getSetting('ampOnPageTypes'))); ?>><?php echo $option['label'] ?></option>
                                <?php
                                }
                                ?>
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