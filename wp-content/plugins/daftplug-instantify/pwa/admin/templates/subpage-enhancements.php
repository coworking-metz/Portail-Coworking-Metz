<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -enhancements -flex12" data-subpage="enhancements" data-title="<?php esc_html_e('Enhancements', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsAjaxify" data-feature-type="beta">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Ajaxify', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php _e('Ajaxify brings a true native app like experience by loading your content without reloading entire page. If you want to exclude certain links or forms from Ajaxify, just add <code>no-ajaxy</code> class on the element.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable ajaxifying your website.', $this->textDomain); ?></p>
                        <label for="pwaAjaxify" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ajaxify', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaAjaxify" id="pwaAjaxify" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaAjaxify'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaAjaxifyDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable also ajaxifying forms.', $this->textDomain); ?></p>
                        <label for="pwaAjaxifyForms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Forms', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaAjaxifyForms" id="pwaAjaxifyForms" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaAjaxifyForms'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaAjaxifyDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('By default ajaxify triggers on links. If you want to ajaxify additional components like tabs, you can add comma-separated list of their selectors to trigger ajaxify. Example: .pagination, #tab-item', $this->textDomain); ?></p>
                        <label for="pwaAjaxifySelectors" class="daftplugAdminField_label -flex4"><?php esc_html_e('Selectors', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="pwaAjaxifySelectors" id="pwaAjaxifySelectors" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('pwaAjaxifySelectors'); ?>" data-placeholder="<?php esc_html_e('Selectors', $this->textDomain); ?>" autocomplete="off">
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaAjaxifyDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms ajaxify feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaAjaxifyPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaAjaxifyPlatforms" id="pwaAjaxifyPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('pwaAjaxifyPlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaAjaxifyPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaAjaxifyPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaAjaxifyPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsPasswordlessLogin">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Passwordless Login', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php _e('The passwordless login feature is using the Web Authentication API that allows your website to authenticate users with the device\'s built-in authenticators like Touch ID, Face ID and Windows Hello or using security keys like Yubikey.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable passwordless login.', $this->textDomain); ?></p>
                        <label for="pwaPasswordlessLogin" class="daftplugAdminField_label -flex4"><?php esc_html_e('Passwordless Login', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaPasswordlessLogin" id="pwaPasswordlessLogin" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPasswordlessLogin'), 'on'); ?>>
                        </label>
                    </div>
                </fieldset>
                <?php if (!daftplugInstantify::isAmpPluginActive() && daftplugInstantify::getSetting('amp') == 'on') { ?>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsAdaptiveLoading">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Adaptive Loading', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Adaptive loading involves delivering different experiences to different users based on their network and hardware constraints. Instantify will show your users lightweight (AMP) version of your website if your user\'s network speed is low or if the user has data saver mode enabled on the device.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable adaptive loading.', $this->textDomain); ?></p>
                        <label for="pwaAdaptiveLoading" class="daftplugAdminField_label -flex4"><?php esc_html_e('Adaptive Loading', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaAdaptiveLoading" id="pwaAdaptiveLoading" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaAdaptiveLoading'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaAdaptiveLoadingDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms adaptive loading feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaAdaptiveLoadingPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaAdaptiveLoadingPlatforms" id="pwaAdaptiveLoadingPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="desktop" <?php selected(true, in_array('desktop', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms'))); ?>><?php esc_html_e('Desktop', $this->textDomain); ?></option>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <?php } ?>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsBackgroundSync">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Background Sync', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Background sync lets you defer actions until the user has stable connectivity. This ensures that crucial requests made while your web app is offline can be replayed when the user comes back online.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable background sync.', $this->textDomain); ?></p>
                        <label for="pwaBackgroundSync" class="daftplugAdminField_label -flex4"><?php esc_html_e('Background Sync', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaBackgroundSync" id="pwaBackgroundSync" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaBackgroundSync'), 'on'); ?>>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsPeriodicBackgroundSync">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Periodic Background Sync', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Periodic Background Sync enables web applications to periodically synchronize data in the background, bringing web apps closer to the behavior of a platform-specific app. It lets your website to always show fresh content in PWA by downloading data in the background when the app or page is not being used.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable periodic background sync.', $this->textDomain); ?></p>
                        <label for="pwaPeriodicBackgroundSync" class="daftplugAdminField_label -flex4"><?php esc_html_e('Periodic Background Sync', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaPeriodicBackgroundSync" id="pwaPeriodicBackgroundSync" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPeriodicBackgroundSync'), 'on'); ?>>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsPersistentStorage">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Persistent Storage', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Persistent storage can help protect critical data from eviction, and reduce the chance of data loss by requesting that your entire site\'s storage be marked as persistent.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable persistent storage.', $this->textDomain); ?></p>
                        <label for="pwaPersistentStorage" class="daftplugAdminField_label -flex4"><?php esc_html_e('Persistent Storage', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaPersistentStorage" id="pwaPersistentStorage" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPersistentStorage'), 'on'); ?>>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsUrlProtocolHandler">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('URL Protocol Handler', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('The URL Protocol Handler feature lets installed PWAs handle links that use a specific protocol for a more integrated experience. After registering a PWA as a protocol handler, when a user clicks on a hyperlink with a specific scheme from a browser or a platform-specific app, the registered PWA will open and receive the URL.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable URL protocol handler.', $this->textDomain); ?></p>
                        <label for="pwaUrlProtocolHandler" class="daftplugAdminField_label -flex4"><?php esc_html_e('URL Protocol Handler', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaUrlProtocolHandler" id="pwaUrlProtocolHandler" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaUrlProtocolHandler'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaUrlProtocolHandlerDependentHideD">
                        <p class="daftplugAdminField_description"><?php _e('Enter the protocol to be handled by your PWA web app. For example, if your website is a music streaming app, your protocol will be <code>music</code>, so when a user shares a link to a song like <code>web+<strong>music</strong>://song=123</code> and the user clicks on it, your music streaming PWA will automatically launch in a standalone window.', $this->textDomain); ?></p>
                        <label for="pwaUrlProtocolHandlerProtocol" class="daftplugAdminField_label -flex4"><?php esc_html_e('Protocol', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="pwaUrlProtocolHandlerProtocol" id="pwaUrlProtocolHandlerProtocol" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('pwaUrlProtocolHandlerProtocol'); ?>" data-placeholder="<?php esc_html_e('Protocol', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaUrlProtocolHandlerDependentHideD">
                        <p class="daftplugAdminField_description"><?php _e('Enter the URL of your web app protocol handler. This URL must include <code>%s</code>, as a placeholder that will be replaced with the escaped URL to be handled. For example, if your website is a music streaming app, and you will set <code>music</code> as a protocol, and your app handles music URLs like this <code>song=123</code> then your URL for handler will be <code>song=%s</code>. So when a user shares a link to a song like <code>web+music://<strong>song=123</strong></code> and the user clicks on it, your music streaming PWA will automatically launch in a standalone window.', $this->textDomain); ?></p>
                        <label for="pwaUrlProtocolHandlerUrl" class="daftplugAdminField_label -flex4"><?php esc_html_e('URL', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="pwaUrlProtocolHandlerUrl" id="pwaUrlProtocolHandlerUrl" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('pwaUrlProtocolHandlerUrl'); ?>" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsWebShareTarget">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Web Share Target', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Web Share Target feature adds a system-level share target picker and allows your web app to register as a share target to receive shared data from other sites or apps via share URL scheme. The feature is most useful if your website is a social networking app.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable web share target.', $this->textDomain); ?></p>
                        <label for="pwaWebShareTarget" class="daftplugAdminField_label -flex4"><?php esc_html_e('Web Share Target', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaWebShareTarget" id="pwaWebShareTarget" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaWebShareTarget'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaWebShareTargetDependentHideD">
                        <p class="daftplugAdminField_description"><?php _e('Enter the action of your web app. Action is a URL or your share URL scheme that accepts parameters and opens a share dialog. For example Facebook share action is <code>/sharer/</code> and Facebook full share URL is https://www.facebook.com<code>/sharer/</code>?u=https://example.com/', $this->textDomain); ?></p>
                        <label for="pwaWebShareTargetAction" class="daftplugAdminField_label -flex4"><?php esc_html_e('Share Action', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="pwaWebShareTargetAction" id="pwaWebShareTargetAction" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('pwaWebShareTargetAction'); ?>" data-placeholder="<?php esc_html_e('Share Action', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -pwaWebShareTargetDependentHideD">
                        <p class="daftplugAdminField_description"><?php _e('Enter the URL query parameter of your web app. It is a query parameter that gets sharable URL as a value and inserts it into the share dialog. For example Facebook URL query parameter is <code>u</code> and Facebook full share URL is https://www.facebook.com/sharer/?<code>u</code>=https://example.com/', $this->textDomain); ?></p>
                        <label for="pwaWebShareTargetUrlQuery" class="daftplugAdminField_label -flex4"><?php esc_html_e('Share URL Query', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="pwaWebShareTargetUrlQuery" id="pwaWebShareTargetUrlQuery" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('pwaWebShareTargetUrlQuery'); ?>" data-placeholder="<?php esc_html_e('Share URL Query', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsVibration">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Vibration', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Vibration feature creates vibes on tapping for mobile users. That can help mobile users recognize when they are tapping and clicking on your website.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable vibration support for your web app.', $this->textDomain); ?></p>
                        <label for="pwaVibration" class="daftplugAdminField_label -flex4"><?php esc_html_e('Vibration', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaVibration" id="pwaVibration" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaVibration'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaVibrationDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms vibration feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaVibrationPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaVibrationPlatforms" id="pwaVibrationPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaVibrationPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaVibrationPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaVibrationPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaEnhancementsScreenWakeLock">
                	<h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Screen Wake Lock', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Screen wake lock provides a way to prevent device from dimming or locking the screen when your web application needs to keep running. This capability enables new experiences that, until now, required a platform-specific app.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable screen wake lock.', $this->textDomain); ?></p>
                        <label for="pwaScreenWakeLock" class="daftplugAdminField_label -flex4"><?php esc_html_e('Screen Wake Lock', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaScreenWakeLock" id="pwaScreenWakeLock" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaScreenWakeLock'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -pwaScreenWakeLockDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select on what device types and platforms screen wake lock feature should be active and running.', $this->textDomain); ?></p>
                        <label for="pwaScreenWakeLockPlatforms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Platforms', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select multiple name="pwaScreenWakeLockPlatforms" id="pwaScreenWakeLockPlatforms" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Platforms', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="mobile" <?php selected(true, in_array('mobile', (array)daftplugInstantify::getSetting('pwaScreenWakeLockPlatforms'))); ?>><?php esc_html_e('Mobile', $this->textDomain); ?></option>
                                <option value="tablet" <?php selected(true, in_array('tablet', (array)daftplugInstantify::getSetting('pwaScreenWakeLockPlatforms'))); ?>><?php esc_html_e('Tablet', $this->textDomain); ?></option>
                                <option value="pwa" <?php selected(true, in_array('pwa', (array)daftplugInstantify::getSetting('pwaScreenWakeLockPlatforms'))); ?>><?php esc_html_e('PWA App', $this->textDomain); ?></option>
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