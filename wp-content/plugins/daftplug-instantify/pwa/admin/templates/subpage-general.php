<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -general -flex12" data-subpage="general" data-title="<?php esc_html_e('General', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex10">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="pwaGeneralPwaStatistics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('PWA Statistics', $this->textDomain); ?></h4>
                    <div class="daftplugAdminInstallerStatistics">
                        <div class="daftplugAdminInstallerAnalytics">
                            <div class="daftplugAdminInstallerAnalytics_header">
                                <h4 class="daftplugAdminInstallerAnalytics_title"><?php esc_html_e('PWA Installation Analytics', $this->textDomain); ?></h4>
                                <div class="daftplugAdminInstallerAnalytics_buttons">
                                    <span class="daftplugAdminButton -analyticsButton -active" data-period="1week">1 Week</span>
                                    <span class="daftplugAdminButton -analyticsButton" data-period="1month">1 Month</span>
                                    <span class="daftplugAdminButton -analyticsButton" data-period="3month">3 Month</span>
                                    <span class="daftplugAdminButton -analyticsButton" data-period="6month">6 Month</span>
                                    <span class="daftplugAdminButton -analyticsButton" data-period="1year">1 Year</span>                  
                                </div>
                            </div>
                            <div class="daftplugAdminInstallerAnalytics_chartArea">
                                <canvas id="daftplugAdminInstallerAnalytics_chart"></canvas>
                            </div>
                        </div>
                        <div class="daftplugAdminInstallerStats">
                            <h4 class="daftplugAdminInstallerStats_total"><?php printf(__('Total PWA Users: %s', $this->textDomain), count($this->daftplugInstantifyPwaAdminGeneral->installedDevices)); ?></h4>
                            <div class="daftplugAdminInstallerStats_container" style="margin-bottom: 20px;">
                                <div class="daftplugAdminInstallerStats_item">
                                    <h4 class="daftplugAdminInstallerStats_title"><?php esc_html_e('Users By Browser', $this->textDomain); ?></h4>
                                    <div class="daftplugAdminInstallerStats_chartPie">
                                        <canvas id="daftplugAdminInstallerStats_chartBrowser"></canvas>
                                    </div>
                                </div>
                                <div class="daftplugAdminInstallerStats_item">
                                    <h4 class="daftplugAdminInstallerStats_title"><?php esc_html_e('Users By Device', $this->textDomain); ?></h4>
                                    <div class="daftplugAdminInstallerStats_chartPie">
                                        <canvas id="daftplugAdminInstallerStats_chartDevice"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="daftplugAdminInstallerStats_container">
                                <div class="daftplugAdminInstallerStats_item">
                                    <h4 class="daftplugAdminInstallerStats_title"><?php esc_html_e('Users By Country', $this->textDomain); ?></h4>
                                    <div class="daftplugAdminInstallerStats_chartPie">
                                        <canvas id="daftplugAdminInstallerStats_chartCountry"></canvas>
                                    </div>
                                </div>
                                <div class="daftplugAdminInstallerStats_item">
                                    <h4 class="daftplugAdminInstallerStats_title"><?php esc_html_e('Users By Status', $this->textDomain); ?></h4>
                                    <div class="daftplugAdminInstallerStats_chartPie">
                                        <canvas id="daftplugAdminInstallerStats_chartStatus"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaGeneralSupport">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('PWA Support', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you are able to enable or disable PWA support on particular platforms, posts and page types.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms PWA feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaPlatforms" class="daftplugAdminField_label -flex3"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaPlatforms" id="pwaPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('pwaPlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable PWA on all post types and pages. If turned on, this will allow all content and pages on your site to have PWA support. We recommend to enable PWA on all content to serve all pages as PWA.', $this->textDomain); ?></p>
                        <label for="pwaOnAll" class="daftplugAdminField_label -flex3"><?php esc_html_e('All Content', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaOnAll" id="pwaOnAll" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaOnAll'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaOnAllDependentHideE">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select particular post types where you want PWA features to be available.', $this->textDomain); ?></p>
                        <label for="pwaOnPostTypes" class="daftplugAdminField_label -flex3"><?php esc_html_e('Supported Post Types', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaOnPostTypes" id="pwaOnPostTypes" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Supported Post Types', $this->textDomain); ?>" autocomplete="off" required>
                                <?php foreach (array_map('get_post_type_object', $this->daftplugInstantifyPwaAdminGeneral->getPostTypes()) as $postType) { ?>
                                    <option value="<?php echo $postType->name; ?>" <?php selected(true, in_array($postType->name, (array)daftplugInstantify::getSetting('pwaOnPostTypes'))); ?>><?php echo $postType->label; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaOnAllDependentHideE">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select particular page types where you want PWA features to be available.', $this->textDomain); ?></p>
                        <label for="pwaOnPageTypes" class="daftplugAdminField_label -flex3"><?php esc_html_e('Supported Page Types', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaOnPageTypes" id="pwaOnPageTypes" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Supported Pages', $this->textDomain); ?>" autocomplete="off" required>
                                <?php foreach ($this->daftplugInstantifyPwaAdminGeneral->getPageTypes() as $id => $option) { ?>
                                    <option value="<?php echo $id; ?>" <?php selected(true, in_array($id, (array)daftplugInstantify::getSetting('pwaOnPageTypes'))); ?>><?php echo $option['label'] ?></option>
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