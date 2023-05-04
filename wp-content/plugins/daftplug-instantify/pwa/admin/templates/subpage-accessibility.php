<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -accessibility -flex12" data-subpage="accessibility" data-title="<?php esc_html_e('Accessibility', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityNavigationTabBar">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Navigation Tab Bar', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php _e('Navigation tab bar provides app like experience by adding tabbed navigation menu bar on the bottom of your web app when accessed from mobile devices.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable navigation tab bar.', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBar" class="daftplugAdminField_label -flex4"><?php esc_html_e('Navigation Tab Bar', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaNavigationTabBar" id="pwaNavigationTabBar" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaNavigationTabBar'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaNavigationTabBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of navigation tab bar.', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBarBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Background Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaNavigationTabBarBgColor" id="pwaNavigationTabBarBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaNavigationTabBarBgColor'); ?>" data-placeholder="<?php esc_html_e('Background Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaNavigationTabBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the color of icons in navigation tab bar.', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBarIconColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Icon Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaNavigationTabBarIconColor" id="pwaNavigationTabBarIconColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaNavigationTabBarIconColor'); ?>" data-placeholder="<?php esc_html_e('Icon Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaNavigationTabBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the color of active icon in navigation tab bar.', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBarIconActiveColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Icon Active Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaNavigationTabBarIconActiveColor" id="pwaNavigationTabBarIconActiveColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaNavigationTabBarIconActiveColor'); ?>" data-placeholder="<?php esc_html_e('Active Icon Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaNavigationTabBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of active icon in navigation tab bar.', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBarIconActiveBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Icon Active Background Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaNavigationTabBarIconActiveBgColor" id="pwaNavigationTabBarIconActiveBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaNavigationTabBarIconActiveBgColor'); ?>" data-placeholder="<?php esc_html_e('Active Icon Background Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaNavigationTabBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms navigation tab bar feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBarPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaNavigationTabBarPlatforms" id="pwaNavigationTabBarPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaNavigationTabBarPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaNavigationTabBarPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaNavigationTabBarPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaNavigationTabBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php _e('Add items to the navigation tab bar. Instantify is using <a class="daftplugAdminLink" href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome</a> icons, so you have to enter desired icon class in Icon field. Example: fas fa-home', $this->textDomain); ?></p>
                        <label for="pwaNavigationTabBarItem" class="daftplugAdminField_label -flex4"><?php esc_html_e('Navigation Items', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputAddField -flexAuto">
                            <span class="daftplugAdminButton -addField" data-add="pwaNavigationTabBarItem" data-max="11"><?php esc_html_e('+ Add Navigation Item', $this->textDomain); ?></span>
                        </div>
                    </div>
                    <?php for ($ntb = 1; $ntb <= 7; $ntb++) { ?>
                    <fieldset class="daftplugAdminFieldset -miniFieldset -pwaNavigationTabBarItem<?php echo $ntb; ?> -pwaNavigationTabBarDependentDisableD">
                        <h5 class="daftplugAdminFieldset_title"><?php printf(__('Navigation Item %s', $this->textDomain), $ntb); ?></h5>
                        <label class="daftplugAdminInputCheckbox -flexAuto -hidden">
                            <input type="checkbox" name="pwaNavigationTabBarItem<?php echo $ntb; ?>" id="pwaNavigationTabBarItem<?php echo $ntb; ?>" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%s', $ntb)), 'on'); ?>>
                        </label>
                        <div class="daftplugAdminField">
                            <label for="pwaNavigationTabBarItem<?php echo $ntb; ?>Icon" class="daftplugAdminField_label -flex2"><?php esc_html_e('Icon', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputText -flexAuto">
                                <input type="text" name="pwaNavigationTabBarItem<?php echo $ntb; ?>Icon" id="pwaNavigationTabBarItem<?php echo $ntb; ?>Icon" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sIcon', $ntb)); ?>" data-placeholder="<?php esc_html_e('Icon', $this->textDomain); ?>" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="daftplugAdminField">
                            <label for="pwaNavigationTabBarItem<?php echo $ntb; ?>Label" class="daftplugAdminField_label -flex2"><?php esc_html_e('Label', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputText -flexAuto">
                                <input type="text" name="pwaNavigationTabBarItem<?php echo $ntb; ?>Label" id="pwaNavigationTabBarItem<?php echo $ntb; ?>Label" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sLabel', $ntb)); ?>" data-placeholder="<?php esc_html_e('Label', $this->textDomain); ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaNavigationTabBarItem<?php echo $ntb; ?>CustomUrlDependentHideE">
                            <label for="pwaNavigationTabBarItem<?php echo $ntb; ?>Page" class="daftplugAdminField_label -flex2"><?php esc_html_e('Page', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputSelect -flexAuto">
                                <select name="pwaNavigationTabBarItem<?php echo $ntb; ?>Page" id="pwaNavigationTabBarItem<?php echo $ntb; ?>Page" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Page', $this->textDomain); ?>" autocomplete="off" required>
                                    <option value="<?php echo trailingslashit(strtok(home_url('/', 'https'), '?')); ?>" <?php selected(daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sPage', $ntb)), trailingslashit(strtok(home_url('/', 'https'), '?'))); ?>><?php esc_html_e('Home Page', $this->textDomain); ?></option>
                                    <option value="*directSearch*" <?php selected(daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sPage', $ntb)), '*directSearch*'); ?>><?php esc_html_e('*Direct Search*', $this->textDomain); ?></option>
                                    <?php foreach (get_pages() as $page) { ?>
                                    <option value="<?php echo get_page_link($page->ID) ?>" <?php selected(daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sPage', $ntb)), get_page_link($page->ID)); ?>><?php echo $page->post_title ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaNavigationTabBarItem<?php echo $ntb; ?>CustomUrlDependentHideD" style="display: none;">
                            <label for="pwaNavigationTabBarItem<?php echo $ntb; ?>Url" class="daftplugAdminField_label -flex2"><?php esc_html_e('URL', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputText -flexAuto">
                                <input type="url" name="pwaNavigationTabBarItem<?php echo $ntb; ?>Url" id="pwaNavigationTabBarItem<?php echo $ntb; ?>Url" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sUrl', $ntb)); ?>" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="daftplugAdminField">
                            <label for="pwaNavigationTabBarItem<?php echo $ntb; ?>CustomUrl" class="daftplugAdminField_label -flex4"><?php esc_html_e('Custom URL', $this->textDomain); ?></label>
                            <label class="daftplugAdminInputCheckbox -flexAuto">
                                <input type="checkbox" name="pwaNavigationTabBarItem<?php echo $ntb; ?>CustomUrl" id="pwaNavigationTabBarItem<?php echo $ntb; ?>CustomUrl" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sCustomUrl', $ntb)), 'on'); ?>>
                            </label>
                        </div>
                    </fieldset>
                    <?php } ?>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityScrollProgressBar">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Scroll Progress Bar', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Scroll Progress Bar feature creates a progress bar on top of the page that indicates the scroll progress percentage of the page.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable scroll progress bar.', $this->textDomain); ?></p>
                        <label for="pwaScrollProgressBar" class="daftplugAdminField_label -flex4"><?php esc_html_e('Scroll Progress Bar', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaScrollProgressBar" id="pwaScrollProgressBar" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaScrollProgressBar'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaScrollProgressBarDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms scroll progress bar feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaScrollProgressBarPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaScrollProgressBarPlatforms" id="pwaScrollProgressBarPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('pwaScrollProgressBarPlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaScrollProgressBarPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaScrollProgressBarPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaScrollProgressBarPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityDarkMode">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Dark Mode', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Dark mode gives you the ability to add a simple dark mode switch button on your website and suggest your users an option for switching to the dark version of your website. You can also enable automatic switching on dark mode if user display preference is set to Dark Mode in their device OS settings and if their battery level is low, saving their device\'s energy.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable dark mode on your website.', $this->textDomain); ?></p>
                        <label for="pwaDarkMode" class="daftplugAdminField_label -flex4"><?php esc_html_e('Dark Mode', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaDarkMode" id="pwaDarkMode" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaDarkMode'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaDarkModeDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select position of your dark mode switch button on your website.', $this->textDomain); ?></p>
                        <label for="pwaDarkModeSwitchButtonPosition" class="daftplugAdminField_label -flex4"><?php esc_html_e('Switch Button Position', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaDarkModeSwitchButtonPosition" id="pwaDarkModeSwitchButtonPosition" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Switch Button Position', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="bottom-left" <?php selected(daftplugInstantify::getSetting('pwaDarkModeSwitchButtonPosition'), 'bottom-left') ?>><?php esc_html_e('Bottom Left', $this->textDomain); ?></option>
                                <option value="bottom-right" <?php selected(daftplugInstantify::getSetting('pwaDarkModeSwitchButtonPosition'), 'bottom-right') ?>><?php esc_html_e('Bottom Right', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaDarkModeDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('OS aware dark mode automatically switches your website to a dark mode if your user\'s device OS preference is set to Dark Mode in display settings.', $this->textDomain); ?></p>
                        <label for="pwaDarkModeOSAware" class="daftplugAdminField_label -flex4"><?php esc_html_e('OS Aware Dark Mode', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaDarkModeOSAware" id="pwaDarkModeOSAware" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaDarkModeOSAware'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaDarkModeDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Battery low dark mode automatically switches your website to a dark mode if your user\'s device battery level is less than 10%, thus saving their battery from draining fast.', $this->textDomain); ?></p>
                        <label for="pwaDarkModeBatteryLow" class="daftplugAdminField_label -flex4"><?php esc_html_e('Battery Low Dark Mode', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaDarkModeBatteryLow" id="pwaDarkModeBatteryLow" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaDarkModeBatteryLow'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaDarkModeDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms dark mode feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaDarkModePlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaDarkModePlatforms" id="pwaDarkModePlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('pwaDarkModePlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaDarkModePlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaDarkModePlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaDarkModePlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityWebShareButton">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Web Share Button', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('With the Web Share API, web apps are able to use the same system-provided share capabilities as platform-specific apps. From this section you can enable floating share button with the native share functionality for your mobile device users.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable web share button on your website.', $this->textDomain); ?></p>
                        <label for="pwaWebShareButton" class="daftplugAdminField_label -flex4"><?php esc_html_e('Web Share Button', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaWebShareButton" id="pwaWebShareButton" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaWebShareButton'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaWebShareButtonDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the share icon color on your web share button.', $this->textDomain); ?></p>
                        <label for="pwaWebShareButtonIconColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Icon Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaWebShareButtonIconColor" id="pwaWebShareButtonIconColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaWebShareButtonIconColor'); ?>" data-placeholder="<?php esc_html_e('Button Icon Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaWebShareButtonDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of your web share button.', $this->textDomain); ?></p>
                        <label for="pwaWebShareButtonBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Background Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaWebShareButtonBgColor" id="pwaWebShareButtonBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaWebShareButtonBgColor'); ?>" data-placeholder="<?php esc_html_e('Button Background Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaWebShareButtonDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select position of your web share button on your website.', $this->textDomain); ?></p>
                        <label for="pwaWebShareButtonPosition" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Position', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaWebShareButtonPosition" id="pwaWebShareButtonPosition" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Button Position', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="bottom-left" <?php selected(daftplugInstantify::getSetting('pwaWebShareButtonPosition'), 'bottom-left') ?>><?php esc_html_e('Bottom Left', $this->textDomain); ?></option>
                                <option value="top-left" <?php selected(daftplugInstantify::getSetting('pwaWebShareButtonPosition'), 'top-left') ?>><?php esc_html_e('Top Left', $this->textDomain); ?></option>
                                <option value="bottom-right" <?php selected(daftplugInstantify::getSetting('pwaWebShareButtonPosition'), 'bottom-right') ?>><?php esc_html_e('Bottom Right', $this->textDomain); ?></option>
                                <option value="top-right" <?php selected(daftplugInstantify::getSetting('pwaWebShareButtonPosition'), 'top-right') ?>><?php esc_html_e('Top Right', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaWebShareButtonDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms web share button feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaWebShareButtonPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaWebShareButtonPlatforms" id="pwaWebShareButtonPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaWebShareButtonPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaWebShareButtonPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaWebShareButtonPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityPullDownNavigation">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Pull Down Navigation', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Pull down navigation touchscreen gesture allows your users to drag the screen and then release it, as a signal to the application to refresh contents or navigate your web app.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable pull down navigation touchscreen gesture.', $this->textDomain); ?></p>
                        <label for="pwaPullDownNavigation" class="daftplugAdminField_label -flex4"><?php esc_html_e('Pull Down Navigation', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaPullDownNavigation" id="pwaPullDownNavigation" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPullDownNavigation'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaPullDownNavigationDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of pull down navigation touchscreen gesture.', $this->textDomain); ?></p>
                        <label for="pwaPullDownNavigationBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Background Color', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputColor -flexAuto">
                            <input type="text" name="pwaPullDownNavigationBgColor" id="pwaPullDownNavigationBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaPullDownNavigationBgColor'); ?>" data-placeholder="<?php esc_html_e('Background Color', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaPullDownNavigationDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms pull down navigation feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaPullDownNavigationPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaPullDownNavigationPlatforms" id="pwaPullDownNavigationPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaPullDownNavigationPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaPullDownNavigationPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaPullDownNavigationPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilitySwipeNavigation">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Swipe Navigation', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Swipe Navigation allows your users to execute web browser\'s "Back" and "Next" actions by simple swiping on the screen anywhere on the website. Swiping left will result in going back and swiping right will result in going next.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable swipe navigation touchscreen gesture.', $this->textDomain); ?></p>
                        <label for="pwaSwipeNavigation" class="daftplugAdminField_label -flex4"><?php esc_html_e('Swipe Navigation', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaSwipeNavigation" id="pwaSwipeNavigation" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaSwipeNavigation'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaSwipeNavigationDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms swipe navigation feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaSwipeNavigationPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaSwipeNavigationPlatforms" id="pwaSwipeNavigationPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaSwipeNavigationPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaSwipeNavigationPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaSwipeNavigationPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityShakeToRefresh">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Shake To Refresh', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Shake to refresh gesture brings amazing UX experience to your users by simply shaking phone as a signal to your web application to refresh the contents of the screen.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable shake to refresh gesture.', $this->textDomain); ?></p>
                        <label for="pwaShakeToRefresh" class="daftplugAdminField_label -flex4"><?php esc_html_e('Shake To Refresh', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaShakeToRefresh" id="pwaShakeToRefresh" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaShakeToRefresh'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaShakeToRefreshDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms shake to refresh feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaShakeToRefreshPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaShakeToRefreshPlatforms" id="pwaShakeToRefreshPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaShakeToRefreshPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaShakeToRefreshPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaShakeToRefreshPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityPreloader">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Preloader', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Preloader feature gives you ability to show a nice loader animation between page loadings. Loader appears at the start of page load and disappears after it is fully loaded.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable preloader for your web app.', $this->textDomain); ?></p>
                        <label for="pwaPreloader" class="daftplugAdminField_label -flex4"><?php esc_html_e('Preloader', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaPreloader" id="pwaPreloader" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPreloader'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaPreloaderDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the preloader style.', $this->textDomain); ?></p>
                        <label for="pwaPreloaderStyle" class="daftplugAdminField_label -flex4"><?php esc_html_e('Style', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaPreloaderStyle" id="pwaPreloaderStyle" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Style', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="default" <?php selected(daftplugInstantify::getSetting('pwaPreloaderStyle'), 'default') ?>><?php esc_html_e('Default', $this->textDomain); ?></option>
                                <option value="skeleton" <?php selected(daftplugInstantify::getSetting('pwaPreloaderStyle'), 'skeleton') ?>><?php esc_html_e('Skeleton', $this->textDomain); ?></option>
                                <option value="spinner" <?php selected(daftplugInstantify::getSetting('pwaPreloaderStyle'), 'spinner') ?>><?php esc_html_e('Spinner', $this->textDomain); ?></option>
                                <option value="redirect" <?php selected(daftplugInstantify::getSetting('pwaPreloaderStyle'), 'redirect') ?>><?php esc_html_e('Redirect', $this->textDomain); ?></option>
                                <option value="percent" <?php selected(daftplugInstantify::getSetting('pwaPreloaderStyle'), 'percent') ?>><?php esc_html_e('Percent', $this->textDomain); ?></option>
                                <option value="fade" <?php selected(daftplugInstantify::getSetting('pwaPreloaderStyle'), 'fade') ?>><?php esc_html_e('Fade', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaPreloaderDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms preloader feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaPreloaderPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaPreloaderPlatforms" id="pwaPreloaderPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('pwaPreloaderPlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaPreloaderPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaPreloaderPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaPreloaderPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityInactiveBlur">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Inactive Blur', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Inactive Blur feature blurs the website when it\'s not focused, so when the user switches to another tab in browser or will minimize your website as a background task your website will be blurred.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable inactive blur.', $this->textDomain); ?></p>
                        <label for="pwaInactiveBlur" class="daftplugAdminField_label -flex4"><?php esc_html_e('Inactive Blur', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaInactiveBlur" id="pwaInactiveBlur" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaInactiveBlur'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaInactiveBlurDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms inactive blur feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaInactiveBlurPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaInactiveBlurPlatforms" id="pwaInactiveBlurPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaInactiveBlurPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaInactiveBlurPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaInactiveBlurPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaAccessibilityToastMessages">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Toast Messages', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Toast Messages provide simple feedback about an operation in a small popup. It only fills the amount of space required for the message and the current activity remains visible and interactive. Toasts automatically disappear after a timeout.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable toast messages for your web app.', $this->textDomain); ?></p>
                        <label for="pwaToastMessages" class="daftplugAdminField_label -flex4"><?php esc_html_e('Toast Messages', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaToastMessages" id="pwaToastMessages" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaToastMessages'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaToastMessagesDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms toast messages feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaToastMessagesPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaToastMessagesPlatforms" id="pwaToastMessagesPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaToastMessagesPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaToastMessagesPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaToastMessagesPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
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