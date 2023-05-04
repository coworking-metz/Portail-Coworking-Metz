<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -offlineusage -flex12" data-subpage="offlineusage" data-title="<?php esc_html_e('Offline Usage', $this->textDomain); ?>">
    <div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="pwaOfflineUsageOfflineUsage">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Offline Usage', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Offline usage adds offline support and reliable performance on your web app. This allows a visitor to load any previously viewed page while they are offline or on low connectivity network. The plugin also defines a special “Offline Page”, which allows you to customize a message and the experience if the app is offline and the page is not in the offline cache.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the special offline fallback page for your web application. This page will show up your users when they navigate your website without an internet connection and the requested page won\'t be in cache.', $this->textDomain); ?></p>
                        <label for="pwaOfflineFallbackPage" class="daftplugAdminField_label -flex4"><?php esc_html_e('Offline Fallback Page', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineFallbackPage" id="pwaOfflineFallbackPage" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Offline Fallback Page', $this->textDomain); ?>" autocomplete="off" required>
                                <?php foreach (get_pages() as $page) { ?>
                                <option value="<?php echo get_page_link($page->ID) ?>" <?php selected(daftplugInstantify::getSetting('pwaOfflineFallbackPage'), get_page_link($page->ID)) ?>><?php echo $page->post_title ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable live reconnecting notification for your users when they go offline or their connection interrupts on your website.', $this->textDomain); ?></p>
                        <label for="pwaOfflineNotification" class="daftplugAdminField_label -flex4"><?php esc_html_e('Offline Notification', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaOfflineNotification" id="pwaOfflineNotification" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaOfflineNotification'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable offline form submission. If enabled, the forms data will be saved, then your website will automatically sync it once the user is online and submit pending forms in background using AJAX.', $this->textDomain); ?></p>
                        <label for="pwaOfflineForms" class="daftplugAdminField_label -flex4"><?php esc_html_e('Offline Forms', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaOfflineForms" id="pwaOfflineForms" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaOfflineForms'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable offline Google Analytics support. If enabled, Instantify will store the data and sync it as soon as the user is online again.', $this->textDomain); ?></p>
                        <label for="pwaOfflineGoogleAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Offline Google Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="pwaOfflineGoogleAnalytics" id="pwaOfflineGoogleAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaOfflineGoogleAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="pwaOfflineUsageCachingStrategies">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Caching Strategies', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('All network requests are cached by Instantify, so that your website can serve content from the browser cache if available and display requested content as fast as possible. Here you are able to manually change the caching strategy for some request types. Available caching strategies (hover for description):', $this->textDomain); ?>
                        <strong class="daftplugAdminCacheStrategy" data-tooltip="<?php esc_html_e('This strategy will use a cached response for a request if it is available and update the cache in the background with a response form the network. (If it’s not cached it will wait for the network response and use that). This is a fairly safe strategy as it means users are regularly updating their cache. The downside of this strategy is that it’s always requesting an asset from the network, using up the user’s bandwidth.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></strong>
                        <strong class="daftplugAdminCacheStrategy" data-tooltip="<?php esc_html_e('This will try and get a request from the network first. If it receives a response, it’ll pass that to the browser and also save it to a cache. If the network request fails, the last cached response will be used.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('Network First', $this->textDomain); ?></strong>
                        <strong class="daftplugAdminCacheStrategy" data-tooltip="<?php esc_html_e('This strategy will check the cache for a response first and use that if one is available. If the request isn’t in the cache, the network will be used and any valid response will be added to the cache before being passed to the browser.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('Cache First', $this->textDomain); ?></strong>
                        <strong class="daftplugAdminCacheStrategy" data-tooltip="<?php esc_html_e('This strategy won\'t cache anything. The network will be used and the response will be passed directly to the browser (That\'s how the browser would handle the request without the Instantify).', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('Network Only', $this->textDomain); ?></strong>
                        <strong class="daftplugAdminCacheStrategy" data-tooltip="<?php esc_html_e('This strategy will always use a pre-populated cached response and ensures that requests never go to the network. Because this strategy never goes to the network, these assets will get updated only when you update your caching settings or cache is expired, so in most cases this strategy is not useful.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('Cache Only', $this->textDomain); ?></strong>
                    </p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your pages (HTML files). We recommend you to set it on Network First always show users latest version of your website and same time update the cache in the background in any case if the user is on slow connectivity or offline.', $this->textDomain); ?></p>
                        <label for="pwaOfflineHtmlStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('HTML Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineHtmlStrategy" id="pwaOfflineHtmlStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('HTML Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineHtmlStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineHtmlStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineHtmlStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineHtmlStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineHtmlStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your JavaScript (JS files). We recommend you to set it on Stale While Revalidate to improve performance and always update users with latest version of your website in their cache.', $this->textDomain); ?></p>
                        <label for="pwaOfflineJavascriptStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('JavaScript Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineJavascriptStrategy" id="pwaOfflineJavascriptStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('JavaScript Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineJavascriptStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineJavascriptStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineJavascriptStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineJavascriptStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineJavascriptStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your stylesheets (CSS files). We recommend you to set it on Stale While Revalidate to improve performance and always update users with latest version of your website in their cache.', $this->textDomain); ?></p>
                        <label for="pwaOfflineStylesheetsStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('Stylesheets Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineStylesheetsStrategy" id="pwaOfflineStylesheetsStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Stylesheets Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineStylesheetsStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineStylesheetsStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineStylesheetsStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineStylesheetsStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineStylesheetsStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your fonts (WOFF, WOFF2, EOT, TTF and SVG files). We recommend you to set it on Stale While Revalidate to improve performance and always update users with latest version of your website in their cache.', $this->textDomain); ?></p>
                        <label for="pwaOfflineFontsStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('Fonts Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineFontsStrategy" id="pwaOfflineFontsStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Fonts Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineFontsStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineFontsStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineFontsStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineFontsStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineFontsStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your images (PNG, JPG, JPEG, GIF and ICO files). We recommend you to set it on Stale While Revalidate to improve performance and always update users with latest version of your website in their cache.', $this->textDomain); ?></p>
                        <label for="pwaOfflineImagesStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('Images Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineImagesStrategy" id="pwaOfflineImagesStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Images Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineImagesStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineImagesStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineImagesStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineImagesStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineImagesStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your videos (MP4, MPG, MKV, SVI, AVI, FLV and WEBM files). We recommend you to set it on Cache First as media files are large size, static and they don\'t require re-validation. Serving them from the cache as soon as they\'re available will improve your website performance by skipping the waiting time for the network request.', $this->textDomain); ?></p>
                        <label for="pwaOfflineVideosStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('Videos Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineVideosStrategy" id="pwaOfflineVideosStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Videos Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineVideosStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineVideosStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineVideosStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineVideosStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineVideosStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the caching strategy for your audios (MP3, M4A, FLAC, WAV, WMA and AAC files). We recommend you to set it on Cache First as media files are large size, static and they don\'t require re-validation. Serving them from the cache as soon as they\'re available will improve your website performance by skipping the waiting time for the network request.', $this->textDomain); ?></p>
                        <label for="pwaOfflineAudiosStrategy" class="daftplugAdminField_label -flex4"><?php esc_html_e('Audios Strategy', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="pwaOfflineAudiosStrategy" id="pwaOfflineAudiosStrategy" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Audios Strategy', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="StaleWhileRevalidate" <?php selected(daftplugInstantify::getSetting('pwaOfflineAudiosStrategy'), 'StaleWhileRevalidate') ?>><?php esc_html_e('Stale While Revalidate', $this->textDomain); ?></option>
                                <option value="NetworkFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineAudiosStrategy'), 'NetworkFirst') ?>><?php esc_html_e('Network First', $this->textDomain); ?></option>
                                <option value="CacheFirst" <?php selected(daftplugInstantify::getSetting('pwaOfflineAudiosStrategy'), 'CacheFirst') ?>><?php esc_html_e('Cache First', $this->textDomain); ?></option>
                                <option value="NetworkOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineAudiosStrategy'), 'NetworkOnly') ?>><?php esc_html_e('Network Only', $this->textDomain); ?></option>
                                <option value="CacheOnly" <?php selected(daftplugInstantify::getSetting('pwaOfflineAudiosStrategy'), 'CacheOnly') ?>><?php esc_html_e('Cache Only', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Define how many days should cached content be remained in the browser cache storage. We recommend you to leave it on default as browser cache is updated automatically if your caching strategy is StaleWhileRevalidate but if you are using CacheFirst strategy, then lower expiration times might be a right choice.', $this->textDomain); ?></p>
                        <label for="pwaOfflineCacheExpiration" class="daftplugAdminField_label -flex4"><?php esc_html_e('Cache Expiration Time', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputNumber -flexAuto">
                            <input type="number" name="pwaOfflineCacheExpiration" id="pwaOfflineCacheExpiration" class="daftplugAdminInputNumber_field" value="<?php echo daftplugInstantify::getSetting('pwaOfflineCacheExpiration'); ?>" min="1" step="1" max="365" data-placeholder="<?php esc_html_e('Cache Expiration Time', $this->textDomain); ?>" required>
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