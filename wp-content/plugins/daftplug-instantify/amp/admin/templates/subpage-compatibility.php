<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -compatibility -flex12" data-subpage="compatibility" data-title="<?php esc_html_e('Compatibility', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="ampCompatibilityPluginSuppression">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Plugin Suppression', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('When a plugin adds markup that is not allowed in AMP you may let Instantify to remove it, or you can suppress the plugin from running on AMP pages.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable plugin suppression on AMP pages. If turned on, you will see an option below to choose the active plugins to be suppressed on AMP pages.', $this->textDomain); ?></p>
                        <label for="ampPluginSuppression" class="daftplugAdminField_label -flex4"><?php esc_html_e('Plugin Suppression', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampPluginSuppression" id="ampPluginSuppression" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampPluginSuppression'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampPluginSuppressionDependentHideD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Choose plugins to be suppressed. We recommend to suppress plugins on AMP pages which are known to cause problems or needs JavaScript to function properly as JavaScript is not allowed on AMP pages.', $this->textDomain); ?></p>
                        <label for="ampPluginSuppression" class="daftplugAdminField_label -flex4"><?php esc_html_e('Suppressible Plugins', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="ampPluginSuppressionList" id="ampPluginSuppressionList" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Suppressible Plugins', $this->textDomain); ?>" autocomplete="off" required>
                            <?php foreach (daftplugInstantifyAmpAdminCompatibility::getSuppressiblePlugins() as $pluginFile => $pluginData) { ?>
                                    <option value="<?php echo $pluginFile; ?>" <?php selected(true, in_array($pluginFile, (array)daftplugInstantify::getSetting('ampPluginSuppressionList'))); ?>><?php echo $pluginData['Name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampCompatibilitySidebarMenu">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('AMP Sidebar Menu', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('If your website is using JavaScript for toggling hamburger menu on mobile devices, that means that it won\'t work on AMP pages as AMP markup disallows JavaScript from the page. In that case, we can create a custom AMP supported sticky mobile menu for you. You\'ll just need to select your desired menu for AMP Sidebar Menu.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable AMP Sidebar Menu.', $this->textDomain); ?></p>
                        <label for="ampSidebarMenu" class="daftplugAdminField_label -flex4"><?php esc_html_e('AMP Sidebar Menu', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampSidebarMenu" id="ampSidebarMenu" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampSidebarMenu'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampSidebarMenuDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the menu which you want to be used in AMP Sidebar Menu. It should be your main menu.', $this->textDomain); ?></p>
                        <label for="ampSidebarMenuId" class="daftplugAdminField_label -flex4"><?php esc_html_e('AMP Menu', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="ampSidebarMenuId" id="ampSidebarMenuId" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('AMP Menu', $this->textDomain); ?>" autocomplete="off" required>
                                <?php foreach (get_terms('nav_menu') as $menu) { ?>
                                    <option value="<?php echo $menu->term_id; ?>" <?php selected(daftplugInstantify::getSetting('ampSidebarMenuId'), $menu->term_id) ?>><?php echo $menu->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampSidebarMenuDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of your AMP sidebar menu.', $this->textDomain); ?></p>
                        <label for="ampSidebarMenuBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Menu Background Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="ampSidebarMenuBgColor" id="ampSidebarMenuBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('ampSidebarMenuBgColor'); ?>" data-placeholder="<?php esc_html_e('Menu Background Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampSidebarMenuDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the position of your AMP sidebar menu button on your website.', $this->textDomain); ?></p>
                        <label for="ampSidebarMenuPosition" class="daftplugAdminField_label -flex4"><?php esc_html_e('Menu Button Position', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="ampSidebarMenuPosition" id="ampSidebarMenuPosition" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Menu Button Position', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="left" <?php selected(daftplugInstantify::getSetting('ampSidebarMenuPosition'), 'left') ?>><?php esc_html_e('Left', $this->textDomain); ?></option>
                                <option value="right" <?php selected(daftplugInstantify::getSetting('ampSidebarMenuPosition'), 'right') ?>><?php esc_html_e('Right', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampCompatibilityCustomCss">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('AMP Custom CSS', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Instantify uses your active WordPress themes stylesheets and templates for AMP pages, so any CSS changes made to your theme via your style.css file, the WordPress customizer or any other methods also apply to your AMP URLs. However, if you’re looking to apply CSS rules only to your AMP URLs you can add your rules here.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your custom CSS. Please note that this rules will only apply on AMP pages.', $this->textDomain); ?></p>
                        <label for="ampCustomCss" class="daftplugAdminField_label -flex3"><?php esc_html_e('Custom CSS', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="ampCustomCss" id="ampCustomCss" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4"><?php echo htmlspecialchars(wp_unslash(daftplugInstantify::getSetting('ampCustomCss'))); ?></textarea>
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