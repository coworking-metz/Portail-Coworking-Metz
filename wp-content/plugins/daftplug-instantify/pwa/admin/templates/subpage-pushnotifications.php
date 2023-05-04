<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -pushnotifications -flex12" data-subpage="pushnotifications" data-title="<?php esc_html_e('Push Notifications', $this->textDomain); ?>">
    <div class="daftplugAdminPage_content -flex10">
    <?php
    if (!daftplugInstantify::isOnesignalActive() && !daftplugInstantify::isWebpushrActive()) {
        if (!version_compare(PHP_VERSION, '7.3', '<') && extension_loaded('mbstring') && extension_loaded('openssl')) {
            ?>
            <div class="daftplugAdminSettings -flexAuto">
                <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                    <fieldset class="daftplugAdminFieldset" id="pwaPushNotificationsPushNotificationsStatistics">
                        <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Statistics', $this->textDomain); ?></h4>
                        <div class="daftplugAdminSubscriberStatistics">
                            <div class="daftplugAdminSubscriberAnalytics">
                                <div class="daftplugAdminSubscriberAnalytics_header">
                                    <h4 class="daftplugAdminSubscriberAnalytics_title"><?php esc_html_e('Subscriber Analytics', $this->textDomain); ?></h4>
                                    <div class="daftplugAdminSubscriberAnalytics_buttons">
                                        <span class="daftplugAdminButton -analyticsButton -active" data-period="1week">1 Week</span>
                                        <span class="daftplugAdminButton -analyticsButton" data-period="1month">1 Month</span>
                                        <span class="daftplugAdminButton -analyticsButton" data-period="3month">3 Month</span>
                                        <span class="daftplugAdminButton -analyticsButton" data-period="6month">6 Month</span>
                                        <span class="daftplugAdminButton -analyticsButton" data-period="1year">1 Year</span>             
                                    </div>
                                </div>
                                <div class="daftplugAdminSubscriberAnalytics_chartArea">
                                    <canvas id="daftplugAdminSubscriberAnalytics_chart"></canvas>
                                </div>
                            </div>
                            <div class="daftplugAdminSubscriberStats">
                                <h4 class="daftplugAdminSubscriberStats_total"><?php printf(__('Total Subscribers: %s', $this->textDomain), count($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices)); ?></h4>
                                <div class="daftplugAdminSubscriberStats_container" style="margin-bottom: 20px;">
                                    <div class="daftplugAdminSubscriberStats_item">
                                        <h4 class="daftplugAdminSubscriberStats_title"><?php esc_html_e('Users By Browser', $this->textDomain); ?></h4>
                                        <div class="daftplugAdminSubscriberStats_chartPie">
                                            <canvas id="daftplugAdminSubscriberStats_chartBrowser"></canvas>
                                        </div>
                                    </div>
                                    <div class="daftplugAdminSubscriberStats_item">
                                        <h4 class="daftplugAdminSubscriberStats_title"><?php esc_html_e('Users By Device', $this->textDomain); ?></h4>
                                        <div class="daftplugAdminSubscriberStats_chartPie">
                                            <canvas id="daftplugAdminSubscriberStats_chartDevice"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="daftplugAdminSubscriberStats_container">
                                    <div class="daftplugAdminSubscriberStats_item">
                                        <h4 class="daftplugAdminSubscriberStats_title"><?php esc_html_e('Users By Country', $this->textDomain); ?></h4>
                                        <div class="daftplugAdminSubscriberStats_chartPie">
                                            <canvas id="daftplugAdminSubscriberStats_chartCountry"></canvas>
                                        </div>
                                    </div>
                                    <div class="daftplugAdminSubscriberStats_item">
                                        <h4 class="daftplugAdminSubscriberStats_title"><?php esc_html_e('Users By Status', $this->textDomain); ?></h4>
                                        <div class="daftplugAdminSubscriberStats_chartPie">
                                            <canvas id="daftplugAdminSubscriberStats_chartStatus"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="daftplugAdminFieldset" id="pwaPushNotificationsPushNotificationsSubscribers">
                        <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Subscribers', $this->textDomain); ?></h4>
                        <p class="daftplugAdminFieldset_description" style="text-align: center;"><?php esc_html_e('Below is a list of your users with their device information who subscribed your website for push notifications.', $this->textDomain); ?></p>
                        <?php if (!empty($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices)) { ?>
                        <div class="daftplugAdminFieldset_button">
                            <span class="daftplugAdminButton -sendNotification" data-subscription="all" data-open-popup="pushModal"><?php esc_html_e('Send Push Notification', $this->textDomain); ?></span>
                        </div>
                        <div class="daftplugAdminFieldset_schedules">
                        <?php foreach($this->daftplugInstantifyPwaAdminPushnotifications->getScheduledNotifications() as $scheduledNotification) { ?>
                            <span class="daftplugAdminSchedule">
                                <div class="daftplugAdminScheduleProgress" data-timeleft="<?php echo $scheduledNotification['time'] - time(); ?>" data-timetotal="<?php echo get_transient($this->optionName."_send_scheduled_notification_date_".$scheduledNotification['time']); ?>">
                                    <div class="daftplugAdminScheduleProgress_circle"></div>
                                    <div class="daftplugAdminScheduleProgress_timer">
                                        <div class="daftplugAdminScheduleProgress_numbers"></div>
                                        <div class="daftplugAdminScheduleProgress_words"></div>
                                    </div>
                                </div>
                                <div class="daftplugAdminSchedule_meta">
                                    <div class="daftplugAdminSchedule_date"><?php echo get_date_from_gmt(date('Y-m-d H:i:s', $scheduledNotification['time']), 'd-M-Y'); ?></div>
                                    <div class="daftplugAdminSchedule_time"><?php echo get_date_from_gmt(date('Y-m-d H:i:s', $scheduledNotification['time']), 'h:i A'); ?></div>
                                    <div class="daftplugAdminSchedule_actions -actions">
                                        <span class="daftplugAdminSchedule_action -send" data-action="send" data-time="<?php echo $scheduledNotification['time']; ?>" data-args="<?php echo htmlspecialchars(wp_json_encode($scheduledNotification['args'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>" data-tooltip="<?php esc_html_e('Send Now', $this->textDomain); ?>" data-tooltip-flow="bottom">
                                            <svg class="daftplugAdminSchedule_icon -iconSend">
                                                <use href="#iconBell"></use>
                                            </svg>
                                        </span>
                                        <span class="daftplugAdminSchedule_action -edit" data-action="edit" data-time="<?php echo $scheduledNotification['time']; ?>" data-args="<?php echo htmlspecialchars(wp_json_encode($scheduledNotification['args'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>" data-tooltip="<?php esc_html_e('Edit', $this->textDomain); ?>" data-tooltip-flow="bottom" data-open-popup="scheduledPushModal">
                                            <svg class="daftplugAdminSchedule_icon -iconEdit">
                                                <use href="#iconEdit"></use>
                                            </svg>
                                        </span>
                                        <span class="daftplugAdminSchedule_action -remove" data-action="remove" data-time="<?php echo $scheduledNotification['time']; ?>" data-args="<?php echo htmlspecialchars(wp_json_encode($scheduledNotification['args'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>" data-tooltip="<?php esc_html_e('Remove', $this->textDomain); ?>" data-tooltip-flow="bottom">
                                            <svg class="daftplugAdminSchedule_icon -iconRemove">
                                                <use href="#iconRemove"></use>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </span>
                        <?php } ?>
                        </div>
                        <?php } ?>
                        <div class="daftplugAdminField">
                            <div class="daftplugAdminInputText -flexAuto">
                                <input type="text" name="subscribersFilter" id="subscribersFilter" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('Filter Subscribers List', $this->textDomain); ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="daftplugAdminTableWrap">
                            <table class="daftplugAdminTable">
                                <thead>
                                    <tr class="daftplugAdminTable_row">
                                        <th class="daftplugAdminTable_header -deviceInfo"><?php esc_html_e('Device Information', $this->textDomain); ?></th>
                                        <th class="daftplugAdminTable_header -regDate"><?php esc_html_e('Registration Date', $this->textDomain); ?></th>
                                        <th class="daftplugAdminTable_header -country"><?php esc_html_e('Country', $this->textDomain); ?></th>
                                        <th class="daftplugAdminTable_header -user"><?php esc_html_e('User', $this->textDomain); ?></th>
                                        <th class="daftplugAdminTable_header -actions"><?php esc_html_e('Actions', $this->textDomain); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices)) {
                                ?>
                                    <tr class="daftplugAdminTable_row">
                                        <td class="daftplugAdminTable_data" colspan="5">
                                            <h4 class="daftplugAdminTable_nodata"><?php esc_html_e('No subscribed devices', $this->textDomain); ?></h4>
                                        </td>
                                    </tr>
                                <?php 
                                } else {
                                    foreach($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices as $subscribedDevice) {
                                    ?>
                                    <tr class="daftplugAdminTable_row">
                                        <td class="daftplugAdminTable_data -deviceInfo"><?php echo $subscribedDevice['deviceInfo']; ?></td>
                                        <td class="daftplugAdminTable_data -regDate"><?php echo $subscribedDevice['date']; ?></td>
                                        <td class="daftplugAdminTable_data -country"><?php echo (array_key_exists('country', $subscribedDevice) ? $subscribedDevice['country'] : __('Unknown', $this->textDomain)); ?></td>
                                        <td class="daftplugAdminTable_data -user">
                                            <?php
                                                if(array_key_exists('user', $subscribedDevice)){if(is_numeric($subscribedDevice['user'])){echo get_userdata($subscribedDevice['user'])->display_name;}else{echo $subscribedDevice['user'];}}else{esc_html_e('Unknown', $this->textDomain);}
                                            ?>
                                        </td>
                                        <td class="daftplugAdminTable_data -actions">
                                            <span class="daftplugAdminTable_action -send" data-subscription="<?php echo $subscribedDevice['endpoint']; ?>" data-tooltip="<?php esc_html_e('Notify', $this->textDomain); ?>" data-tooltip-flow="top" data-open-popup="pushModal">
                                                <svg class="daftplugAdminTable_icon -iconBell">
                                                    <use href="#iconBell"></use>
                                                </svg>
                                            </span>
                                            <span class="daftplugAdminTable_action -remove" data-subscription="<?php echo $subscribedDevice['endpoint']; ?>" data-tooltip="<?php esc_html_e('Remove', $this->textDomain); ?>" data-tooltip-flow="top">
                                                <svg class="daftplugAdminTable_icon -iconRemove">
                                                    <use href="#iconRemove"></use>
                                                </svg>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                    <fieldset class="daftplugAdminFieldset" id="pwaPushNotificationsPushNotificationsSettings">
                        <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Settings', $this->textDomain); ?></h4>
                        <p class="daftplugAdminFieldset_description"><?php esc_html_e('The push notifications setting gives you full control over self-hosted notifications service configuration on your server. Here you\'ll have an ability to adjust some values that can potentially improve notifications performance and avoid your server resource overload.', $this->textDomain); ?></p>
                        <div class="daftplugAdminField">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Time To Live (TTL, in seconds) is how long a push message is retained by the push service (eg. Mozilla) in case the user\'s browser is not yet accessible (eg. is not connected). You may want to use a very long time for important notifications. The default TTL is 4 weeks. However, if you send multiple nonessential notifications, set a TTL of 0: the push notification will be delivered only if the user is currently connected. In other cases, you should use a minimum of one day if your users have multiple time zones, and if they don\'t several hours will suffice.', $this->textDomain); ?></p>
                            <label for="pwaPushTtl" class="daftplugAdminField_label -flex4"><?php esc_html_e('Time To Live (TTL)', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputNumber -flexAuto">
                                <input type="number" name="pwaPushTtl" id="pwaPushTtl" class="daftplugAdminInputNumber_field" value="<?php echo daftplugInstantify::getSetting('pwaPushTtl'); ?>" min="0" step="1" max="2419200" data-placeholder="<?php esc_html_e('Time To Live (TTL)', $this->textDomain); ?>" required>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="daftplugAdminFieldset" id="pwaPushNotificationsPushNotificationsPrompt">
                        <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Prompt', $this->textDomain); ?></h4>
                        <p class="daftplugAdminFieldset_description"><?php esc_html_e('The push notifications prompt is nice simple prompt with your website logo and a message that will ask your users to subscribe push notifications on your website.', $this->textDomain); ?></p>
                        <div class="daftplugAdminField">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable push notifications prompt.', $this->textDomain); ?></p>
                            <label for="pwaPushPrompt" class="daftplugAdminField_label -flex4"><?php esc_html_e('Push Notifications Prompt', $this->textDomain); ?></label>
                            <label class="daftplugAdminInputCheckbox -flexAuto">
                                <input type="checkbox" name="pwaPushPrompt" id="pwaPushPrompt" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushPrompt'), 'on'); ?>>
                            </label>
                        </div>
                        <div class="daftplugAdminField -pwaPushPromptDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Enter the message you want to show your users on push prompt.', $this->textDomain); ?></p>
                            <label for="pwaPushPromptMessage" class="daftplugAdminField_label -flex4"><?php esc_html_e('Prompt Message', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputTextarea -flexAuto">
                                <textarea name="pwaPushPromptMessage" id="pwaPushPromptMessage" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Description', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" required><?php echo daftplugInstantify::getSetting('pwaPushPromptMessage'); ?></textarea>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaPushPromptDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of your push notifications prompt.', $this->textDomain); ?></p>
                            <label for="pwaPushPromptBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Prompt Background Color', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputColor -flexAuto">
                                <input type="text" name="pwaPushPromptBgColor" id="pwaPushPromptBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaPushPromptBgColor'); ?>" data-placeholder="<?php esc_html_e('Prompt Background Color', $this->textDomain); ?>" required>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaPushPromptDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select the text color on your push notifications prompt.', $this->textDomain); ?></p>
                            <label for="pwaPushPromptTextColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Prompt Text Color', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputColor -flexAuto">
                                <input type="text" name="pwaPushPromptTextColor" id="pwaPushPromptTextColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaPushPromptTextColor'); ?>" data-placeholder="<?php esc_html_e('Prompt Text Color', $this->textDomain); ?>" required>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaPushPromptDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('If enabled, users who are visiting the website for the first time will not get the push notifications prompt.', $this->textDomain); ?></p>
                            <label for="pwaPushPromptSkip" class="daftplugAdminField_label -flex4"><?php esc_html_e('Skip First Visit', $this->textDomain); ?></label>
                            <label class="daftplugAdminInputCheckbox -flexAuto">
                                <input type="checkbox" name="pwaPushPromptSkip" id="pwaPushPromptSkip" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushPromptSkip'), 'on'); ?>>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset class="daftplugAdminFieldset" id="pwaPushNotificationsPushNotificationsButton">
                        <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Button', $this->textDomain); ?></h4>
                        <p class="daftplugAdminFieldset_description"><?php esc_html_e('The push notifications button is a custom subscription button on your website to increase opt-in rate and allow your users to fully control when they want to subscribe and unsubscribe for your push notifications.', $this->textDomain); ?></p>
                        <div class="daftplugAdminField">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable push notifications button on your website.', $this->textDomain); ?></p>
                            <label for="pwaPushButton" class="daftplugAdminField_label -flex4"><?php esc_html_e('Push Notifications Button', $this->textDomain); ?></label>
                            <label class="daftplugAdminInputCheckbox -flexAuto">
                                <input type="checkbox" name="pwaPushButton" id="pwaPushButton" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushButton'), 'on'); ?>>
                            </label>
                        </div>
                        <div class="daftplugAdminField -pwaPushButtonDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select the bell icon color on your push notifications button.', $this->textDomain); ?></p>
                            <label for="pwaPushButtonIconColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Icon Color', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputColor -flexAuto">
                                <input type="text" name="pwaPushButtonIconColor" id="pwaPushButtonIconColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaPushButtonIconColor'); ?>" data-placeholder="<?php esc_html_e('Button Icon Color', $this->textDomain); ?>" required>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaPushButtonDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select the background color of your push notifications button.', $this->textDomain); ?></p>
                            <label for="pwaPushButtonBgColor" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Background Color', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputColor -flexAuto">
                                <input type="text" name="pwaPushButtonBgColor" id="pwaPushButtonBgColor" class="daftplugAdminInputColor_field" value="<?php echo daftplugInstantify::getSetting('pwaPushButtonBgColor'); ?>" data-placeholder="<?php esc_html_e('Button Background Color', $this->textDomain); ?>" required>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaPushButtonDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select position of your push notifications button on your website.', $this->textDomain); ?></p>
                            <label for="pwaPushButtonPosition" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Position', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputSelect -flexAuto">
                                <select name="pwaPushButtonPosition" id="pwaPushButtonPosition" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Button Position', $this->textDomain); ?>" autocomplete="off" required>
                                    <option value="bottom-left" <?php selected(daftplugInstantify::getSetting('pwaPushButtonPosition'), 'bottom-left') ?>><?php esc_html_e('Bottom Left', $this->textDomain); ?></option>
                                    <option value="top-left" <?php selected(daftplugInstantify::getSetting('pwaPushButtonPosition'), 'top-left') ?>><?php esc_html_e('Top Left', $this->textDomain); ?></option>
                                    <option value="bottom-right" <?php selected(daftplugInstantify::getSetting('pwaPushButtonPosition'), 'bottom-right') ?>><?php esc_html_e('Bottom Right', $this->textDomain); ?></option>
                                    <option value="top-right" <?php selected(daftplugInstantify::getSetting('pwaPushButtonPosition'), 'top-right') ?>><?php esc_html_e('Top Right', $this->textDomain); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="daftplugAdminField -pwaPushButtonDependentDisableD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select behavior of your push notifications button on your website.', $this->textDomain); ?></p>
                            <label for="pwaPushButtonBehavior" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Behavior', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputSelect -flexAuto">
                                <select name="pwaPushButtonBehavior" id="pwaPushButtonBehavior" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Button Behavior', $this->textDomain); ?>" autocomplete="off" required>
                                    <option value="shown" <?php selected(daftplugInstantify::getSetting('pwaPushButtonBehavior'), 'shown') ?>><?php esc_html_e('Keep shown after user subscribes notifications', $this->textDomain); ?></option>
                                    <option value="hidden" <?php selected(daftplugInstantify::getSetting('pwaPushButtonBehavior'), 'hidden') ?>><?php esc_html_e('Hide after user subscribes notifications', $this->textDomain); ?></option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="daftplugAdminFieldset" id="pwaPushNotificationsPushNotificationsAutomation">
                        <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Automation', $this->textDomain); ?></h4>
                        <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you can enable sending automatic predefined push notifications on certain events to re-engage your users and increase conversion. You will also be allowed to exclude particular post events from sending automatic push notification via meta boxes.', $this->textDomain); ?></p>
                        <div class="daftplugAdminField">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users on publishing new content. Notification will include content title, text and featured image.', $this->textDomain); ?></p>
                            <label for="pwaPushNewContent" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Content', $this->textDomain); ?></label>
                            <label class="daftplugAdminInputCheckbox -flexAuto">
                                <input type="checkbox" name="pwaPushNewContent" id="pwaPushNewContent" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushNewContent'), 'on'); ?>>
                            </label>
                        </div>
                        <div class="daftplugAdminField -pwaPushNewContentDependentHideD">
                            <p class="daftplugAdminField_description"><?php esc_html_e('Select allowed post types for new content notification.', $this->textDomain); ?></p>
                            <label for="pwaPushNewContentPostTypes" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Content Post Types', $this->textDomain); ?></label>
                            <div class="daftplugAdminInputSelect -flexAuto">
                                <select multiple name="pwaPushNewContentPostTypes" id="pwaPushNewContentPostTypes" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('New Content Post Types', $this->textDomain); ?>" autocomplete="off" required>
                                    <?php foreach (array_map('get_post_type_object', $this->daftplugInstantifyPwaAdminPushnotifications->getPostTypes()) as $postType) { ?>
                                        <option value="<?php echo $postType->name; ?>" <?php selected(true, in_array($postType->name, (array)daftplugInstantify::getSetting('pwaPushNewContentPostTypes'))); ?>><?php echo $postType->label; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php if (daftplugInstantify::isWooCommerceActive()) { ?>
                        <fieldset class="daftplugAdminFieldset -miniFieldset">
                            <h5 class="daftplugAdminFieldset_title"><?php esc_html_e('WooCommerce', $this->textDomain); ?></h5>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users on adding new product. Notification will include product title, content and featured image.', $this->textDomain); ?></p>
                                <label for="pwaPushWooNewProduct" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Product', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooNewProduct" id="pwaPushWooNewProduct" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooNewProduct'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users on product price drop. Notification will include product title and featured image.', $this->textDomain); ?></p>
                                <label for="pwaPushWooPriceDrop" class="daftplugAdminField_label -flex4"><?php esc_html_e('Price Drop', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooPriceDrop" id="pwaPushWooPriceDrop" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooPriceDrop'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users when sale price is added to the product. Notification will include product title and featured image.', $this->textDomain); ?></p>
                                <label for="pwaPushWooSalePrice" class="daftplugAdminField_label -flex4"><?php esc_html_e('Sale Price', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooSalePrice" id="pwaPushWooSalePrice" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooSalePrice'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users when product is back in stock. Notification will include product title and featured image.', $this->textDomain); ?></p>
                                <label for="pwaPushWooBackInStock" class="daftplugAdminField_label -flex4"><?php esc_html_e('Back In Stock', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooBackInStock" id="pwaPushWooBackInStock" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooBackInStock'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users when the user has abandoned the cart.', $this->textDomain); ?></p>
                                <label for="pwaPushWooAbandonedCart" class="daftplugAdminField_label -flex4"><?php esc_html_e('Abandoned Cart', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooAbandonedCart" id="pwaPushWooAbandonedCart" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooAbandonedCart'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField -pwaPushWooAbandonedCartDependentHideD">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the for what intervals in hours should the abandoned cart notification be sent.', $this->textDomain); ?></p>
                                <label for="pwaPushWooAbandonedCartInterval" class="daftplugAdminField_label -flex4"><?php esc_html_e('Abandoned Cart Interval', $this->textDomain); ?></label>
                                <div class="daftplugAdminInputSelect -flexAuto">
                                    <select name="pwaPushWooAbandonedCartInterval" id="pwaPushWooAbandonedCartInterval" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Abandoned Cart Interval', $this->textDomain); ?>" autocomplete="off" required>
                                        <option value="1" <?php selected('1', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 1 hours', $this->textDomain); ?></option>
                                        <option value="2" <?php selected('2', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 2 hours', $this->textDomain); ?></option>
                                        <option value="3" <?php selected('3', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 3 hours', $this->textDomain); ?></option>
                                        <option value="4" <?php selected('4', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 4 hours', $this->textDomain); ?></option>
                                        <option value="5" <?php selected('5', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 5 hours', $this->textDomain); ?></option>
                                        <option value="6" <?php selected('6', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 6 hours', $this->textDomain); ?></option>
                                        <option value="7" <?php selected('7', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 7 hours', $this->textDomain); ?></option>
                                        <option value="8" <?php selected('8', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 8 hours', $this->textDomain); ?></option>
                                        <option value="9" <?php selected('9', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 9 hours', $this->textDomain); ?></option>
                                        <option value="10" <?php selected('10', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval')) ?>><?php esc_html_e('Every 10 hours', $this->textDomain); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for users when the order status is updated.', $this->textDomain); ?></p>
                                <label for="pwaPushWooOrderStatusUpdate" class="daftplugAdminField_label -flex4"><?php esc_html_e('Order Status Update', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooOrderStatusUpdate" id="pwaPushWooOrderStatusUpdate" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooOrderStatusUpdate'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for admins when new order is placed.', $this->textDomain); ?></p>
                                <label for="pwaPushWooNewOrder" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Order', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooNewOrder" id="pwaPushWooNewOrder" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooNewOrder'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField -pwaPushWooNewOrderDependentHideD">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the user role which will get notification when new order is placed.', $this->textDomain); ?></p>
                                <label for="pwaPushWooNewOrderRole" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Order Role', $this->textDomain); ?></label>
                                <div class="daftplugAdminInputSelect -flexAuto">
                                    <select name="pwaPushWooNewOrderRole" id="pwaPushWooNewOrderRole" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('New Order Role', $this->textDomain); ?>" autocomplete="off" required>
                                        <?php foreach(wp_roles()->roles as $id => $role) { ?>
                                            <option value="<?php echo $id; ?>" <?php selected(true, in_array($id, (array)daftplugInstantify::getSetting('pwaPushWooNewOrderRole'))); ?>><?php echo $role['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification for admins when product is low in stock.', $this->textDomain); ?></p>
                                <label for="pwaPushWooLowStock" class="daftplugAdminField_label -flex4"><?php esc_html_e('Low Stock', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushWooLowStock" id="pwaPushWooLowStock" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushWooLowStock'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField -pwaPushWooLowStockDependentHideD">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the user role which will get notification when product is low in stock.', $this->textDomain); ?></p>
                                <label for="pwaPushWooLowStockRole" class="daftplugAdminField_label -flex4"><?php esc_html_e('Low Stock Role', $this->textDomain); ?></label>
                                <div class="daftplugAdminInputSelect -flexAuto">
                                    <select name="pwaPushWooLowStockRole" id="pwaPushWooLowStockRole" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Low Stock Role', $this->textDomain); ?>" autocomplete="off" required>
                                        <?php foreach(wp_roles()->roles as $id => $role) { ?>
                                            <option value="<?php echo $id; ?>" <?php selected(true, in_array($id, (array)daftplugInstantify::getSetting('pwaPushWooLowStockRole'))); ?>><?php echo $role['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="daftplugAdminField -pwaPushWooLowStockDependentHideD">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Choose low stock threshold number. Notification will be sent when a stock is less than this number. This low stock threshold will be used if it\'s not already set in product inventory data.', $this->textDomain); ?></p>
                                <label for="pwaPushWooLowStockThreshold" class="daftplugAdminField_label -flex4"><?php esc_html_e('Low Stock Threshold', $this->textDomain); ?></label>
                                <div class="daftplugAdminInputNumber -flexAuto">
                                    <input type="number" name="pwaPushWooLowStockThreshold" id="pwaPushWooLowStockThreshold" class="daftplugAdminInputNumber_field" value="<?php echo daftplugInstantify::getSetting('pwaPushWooLowStockThreshold'); ?>" min="1" step="1" max="50" data-placeholder="<?php esc_html_e('Low Stock Threshold', $this->textDomain); ?>" required>
                                </div>
                            </div>
                        </fieldset>
                        <?php }
                        if (daftplugInstantify::isBuddyPressActive()) { ?>
                        <fieldset class="daftplugAdminFieldset -miniFieldset">
                            <h5 class="daftplugAdminFieldset_title"><?php esc_html_e('BuddyPress', $this->textDomain); ?></h5>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when a member mention someone in an update @username.', $this->textDomain); ?></p>
                                <label for="pwaPushBpMemberMention" class="daftplugAdminField_label -flex4"><?php esc_html_e('Member Mention', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushBpMemberMention" id="pwaPushBpMemberMention" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushBpMemberMention'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when a member replies to an update or comment.', $this->textDomain); ?></p>
                                <label for="pwaPushBpMemberReply" class="daftplugAdminField_label -flex4"><?php esc_html_e('Member Reply', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushBpMemberReply" id="pwaPushBpMemberReply" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushBpMemberReply'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when new message received.', $this->textDomain); ?></p>
                                <label for="pwaPushBpNewMessage" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Message', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushBpNewMessage" id="pwaPushBpNewMessage" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushBpNewMessage'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when new friend request received.', $this->textDomain); ?></p>
                                <label for="pwaPushBpFriendRequest" class="daftplugAdminField_label -flex4"><?php esc_html_e('Friend Request', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushBpFriendRequest" id="pwaPushBpFriendRequest" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushBpFriendRequest'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when friend request accepted.', $this->textDomain); ?></p>
                                <label for="pwaPushBpFriendAccepted" class="daftplugAdminField_label -flex4"><?php esc_html_e('Friend Accepted', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushBpFriendAccepted" id="pwaPushBpFriendAccepted" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushBpFriendAccepted'), 'on'); ?>>
                                </label>
                            </div>
                        </fieldset>
                        <?php }
                        if (daftplugInstantify::isPeepsoActive()) { ?>
                        <fieldset class="daftplugAdminFieldset -miniFieldset">
                            <h5 class="daftplugAdminFieldset_title"><?php esc_html_e('Peepso', $this->textDomain); ?></h5>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notifications for Peepso.', $this->textDomain); ?></p>
                                <label for="pwaPushPeepsoNotifications" class="daftplugAdminField_label -flex4"><?php esc_html_e('Peepso Notifications', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushPeepsoNotifications" id="pwaPushPeepsoNotifications" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushPeepsoNotifications'), 'on'); ?>>
                                </label>
                            </div>
                        </fieldset>
                        <?php }
                        if (daftplugInstantify::isUltimateMemberActive() && daftplugInstantify::isUltimateMemberActive('um-notifications')) { ?>
                        <fieldset class="daftplugAdminFieldset -miniFieldset">
                            <h5 class="daftplugAdminFieldset_title"><?php esc_html_e('Ultimate Member', $this->textDomain); ?></h5>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user role is updated.', $this->textDomain); ?></p>
                                <label for="pwaPushUmRoleUpdate" class="daftplugAdminField_label -flex4"><?php esc_html_e('Role Update', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmRoleUpdate" id="pwaPushUmRoleUpdate" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmRoleUpdate'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user comments.', $this->textDomain); ?></p>
                                <label for="pwaPushUmNewComment" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Comment', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmNewComment" id="pwaPushUmNewComment" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmNewComment'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when comment gets reply.', $this->textDomain); ?></p>
                                <label for="pwaPushUmCommentReply" class="daftplugAdminField_label -flex4"><?php esc_html_e('Comment Reply', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmCommentReply" id="pwaPushUmCommentReply" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmCommentReply'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when guest comments.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGuestComment" class="daftplugAdminField_label -flex4"><?php esc_html_e('Guest Comment', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGuestComment" id="pwaPushUmGuestComment" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGuestComment'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user views profile.', $this->textDomain); ?></p>
                                <label for="pwaPushUmProfileView" class="daftplugAdminField_label -flex4"><?php esc_html_e('Profile View', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmProfileView" id="pwaPushUmProfileView" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmProfileView'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when guest views profile.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGuestProfileView" class="daftplugAdminField_label -flex4"><?php esc_html_e('Guest Profile View', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGuestProfileView" id="pwaPushUmGuestProfileView" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGuestProfileView'), 'on'); ?>>
                                </label>
                            </div>
                            <?php if (daftplugInstantify::isUltimateMemberActive('um-messaging')) { ?>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new private message.', $this->textDomain); ?></p>
                                <label for="pwaPushUmPrivateMessage" class="daftplugAdminField_label -flex4"><?php esc_html_e('Private Message', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmPrivateMessage" id="pwaPushUmPrivateMessage" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmPrivateMessage'), 'on'); ?>>
                                </label>
                            </div>
                            <?php } if (daftplugInstantify::isUltimateMemberActive('um-followers')) { ?>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets followed by a person.', $this->textDomain); ?></p>
                                <label for="pwaPushUmNewFollow" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Follow', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmNewFollow" id="pwaPushUmNewFollow" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmNewFollow'), 'on'); ?>>
                                </label>
                            </div>
                            <?php } if (daftplugInstantify::isUltimateMemberActive('um-friends')) { ?>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new friend request.', $this->textDomain); ?></p>
                                <label for="pwaPushUmFriendRequest" class="daftplugAdminField_label -flex4"><?php esc_html_e('Friend Request', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmFriendRequest" id="pwaPushUmFriendRequest" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmFriendRequest'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user accepts friend request.', $this->textDomain); ?></p>
                                <label for="pwaPushUmFriendRequestAccepted" class="daftplugAdminField_label -flex4"><?php esc_html_e('Friends Activity', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmFriendRequestAccepted" id="pwaPushUmFriendRequestAccepted" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmFriendRequestAccepted'), 'on'); ?>>
                                </label>
                            </div>
                            <?php } if (daftplugInstantify::isUltimateMemberActive('um-social-activity')) { ?>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new wall post.', $this->textDomain); ?></p>
                                <label for="pwaPushUmWallPost" class="daftplugAdminField_label -flex4"><?php esc_html_e('Wall Post', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmWallPost" id="pwaPushUmWallPost" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmWallPost'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new wall comment.', $this->textDomain); ?></p>
                                <label for="pwaPushUmWallComment" class="daftplugAdminField_label -flex4"><?php esc_html_e('Wall Comment', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmWallComment" id="pwaPushUmWallComment" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmWallComment'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new post like.', $this->textDomain); ?></p>
                                <label for="pwaPushUmPostLike" class="daftplugAdminField_label -flex4"><?php esc_html_e('Post Like', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmPostLike" id="pwaPushUmPostLike" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmPostLike'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new mention.', $this->textDomain); ?></p>
                                <label for="pwaPushUmNewMention" class="daftplugAdminField_label -flex4"><?php esc_html_e('New Mention', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmNewMention" id="pwaPushUmNewMention" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmNewMention'), 'on'); ?>>
                                </label>
                            </div>
                            <?php } if (daftplugInstantify::isUltimateMemberActive('um-groups')) { ?>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets group approve.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGroupApprove" class="daftplugAdminField_label -flex4"><?php esc_html_e('Group Approve', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGroupApprove" id="pwaPushUmGroupApprove" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGroupApprove'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a group join request.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGroupJoinRequest" class="daftplugAdminField_label -flex4"><?php esc_html_e('Group Join Request', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGroupJoinRequest" id="pwaPushUmGroupJoinRequest" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGroupJoinRequest'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a group invitation.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGroupInvitation" class="daftplugAdminField_label -flex4"><?php esc_html_e('Group Invitation', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGroupInvitation" id="pwaPushUmGroupInvitation" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGroupInvitation'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets group role change.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGroupRoleChange" class="daftplugAdminField_label -flex4"><?php esc_html_e('Group Role Change', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGroupRoleChange" id="pwaPushUmGroupRoleChange" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGroupRoleChange'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new group post.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGroupPost" class="daftplugAdminField_label -flex4"><?php esc_html_e('Group Post', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGroupPost" id="pwaPushUmGroupPost" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGroupPost'), 'on'); ?>>
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable automatic notification when user gets a new group comment.', $this->textDomain); ?></p>
                                <label for="pwaPushUmGroupComment" class="daftplugAdminField_label -flex4"><?php esc_html_e('Group Comment', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pwaPushUmGroupComment" id="pwaPushUmGroupComment" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwaPushUmGroupComment'), 'on'); ?>>
                                </label>
                            </div>
                            <?php } ?>
                        </fieldset>
                        <?php } ?>
                    </fieldset>
                    <div class="daftplugAdminSettings_submit">
                        <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Save Settings', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Saving', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Settings Saved', $this->textDomain); ?>" data-failed="<?php esc_html_e('Saving Failed', $this->textDomain); ?>"></button>
                    </div>
                </form>
            </div>
            <div class="daftplugAdminPopup" data-popup="pushModal">
                <div class="daftplugAdminPopup_container">
                    <form name="daftplugAdminSendPush_form" class="daftplugAdminSendPush_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_send_notification_nonce"); ?>" spellcheck="false" autocomplete="off">
                        <fieldset class="daftplugAdminFieldset">
                            <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Send Push Notification', $this->textDomain); ?></h4>
                            <div class="daftplugAdminField" style="justify-content:center">
                                <button type="button" class="daftplugAdminButton -preview"><?php esc_html_e('Preview Notification', $this->textDomain); ?></button>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select what segment of your subscribers should receive this notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputSelect -flexAuto">
                                    <select name="pushSegment" id="pushSegment" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Segment', $this->textDomain); ?>" autocomplete="off" required>
                                        <option value="all"><?php esc_html_e('All Users', $this->textDomain); ?></option>
                                        <option value="mobile"><?php esc_html_e('Mobile Users', $this->textDomain); ?></option>
                                        <option value="desktop"><?php esc_html_e('Desktop Users', $this->textDomain); ?></option>
                                        <option value="registered"><?php esc_html_e('Registered Users', $this->textDomain); ?></option>
                                        <option value="unregistered"><?php esc_html_e('Unregistered Users', $this->textDomain); ?></option>
                                        <?php $subscribedCountries = array();foreach($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices as $subscribedDevice) {$subscribedCountries[] = $subscribedDevice['country'];}$subscribedCountries = array_unique($subscribedCountries);unset($subscribedCountries[__('Unknown', $this->textDomain)]);foreach($subscribedCountries as $subscribedCountry) {
                                            echo '<option value="Country - '.$subscribedCountry.'">'.sprintf(__('Users from %s', $this->textDomain), $subscribedCountry).'</option>';
                                        } foreach($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices as $subscribedDevice) { if(array_key_exists('user', $subscribedDevice)){if(is_numeric($subscribedDevice['user'])){$userName = get_userdata($subscribedDevice['user'])->display_name;}else{$userName = $subscribedDevice['user'];}}else{$userName = esc_html__('Unknown', $this->textDomain);}?>
                                            <option value="<?php echo $subscribedDevice['endpoint']; ?>"><?php echo $subscribedDevice['deviceInfo'].' / '.$subscribedDevice['date'].' / '.(array_key_exists('country', $subscribedDevice) ? $subscribedDevice['country'] : __('Unknown', $this->textDomain)).' / '.$userName; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the title of your notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputText -flexAuto">
                                    <input type="text" name="pushTitle" id="pushTitle" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('Title', $this->textDomain); ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the body of your notification (text description).', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputTextarea -flexAuto">
                                    <textarea name="pushBody" id="pushBody" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Body', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the image of notification. it will be displayed as large image on notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputUpload -flexAuto">
                                    <input type="text" name="pushImage" id="pushImage" class="daftplugAdminInputUpload_field" value="" data-mimes="png,jpg,jpeg" data-min-width="50" data-max-width="" data-min-height="50" data-max-height="" data-attach-url="">
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the redirect URL of your notification. Your users will be redirected here after they click on your notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputText -flexAuto">
                                    <input type="url" name="pushUrl" id="pushUrl" class="daftplugAdminInputText_field" value="<?php echo trailingslashit(strtok(home_url('/', 'https'), '?')); ?>" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the icon of notification. We recommend to use your website logo or site icon.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputUpload -flexAuto">
                                    <input type="text" name="pushIcon" id="pushIcon" class="daftplugAdminInputUpload_field" value="" data-mimes="png,jpg,jpeg" data-min-width="50" data-max-width="" data-min-height="50" data-max-height="" data-attach-url="">
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('You can add up to two action buttons to the notification. Currently action buttons are only supported by Chrome browsers.', $this->textDomain); ?></p>
                            </div>
                            <?php for ($ab = 1; $ab <= 2; $ab++) { ?>
                            <fieldset class="daftplugAdminFieldset -miniFieldset -pushActionButton<?php echo $ab; ?>">
                                <h5 class="daftplugAdminFieldset_title"><?php printf(__('Action Button %s', $this->textDomain), $ab); ?></h5>
                                <label class="daftplugAdminInputCheckbox -flexAuto -hidden">
                                    <input type="checkbox" name="pushActionButton<?php echo $ab; ?>" id="pushActionButton<?php echo $ab; ?>" class="daftplugAdminInputCheckbox_field">
                                </label>
                                <div class="daftplugAdminField">
                                    <div class="daftplugAdminInputText -flexAuto">
                                        <input type="text" name="pushActionButton<?php echo $ab; ?>Text" id="pushActionButton<?php echo $ab; ?>Text" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('Text', $this->textDomain); ?>" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="daftplugAdminField">
                                    <div class="daftplugAdminInputText -flexAuto">
                                        <input type="url" name="pushActionButton<?php echo $ab; ?>Url" id="pushActionButton<?php echo $ab; ?>Url" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off" required>
                                    </div>
                                </div>
                            </fieldset>     
                            <?php } ?>
                            <div class="daftplugAdminField">
                                <div class="daftplugAdminInputAddField -flexAuto">
                                    <span class="daftplugAdminButton -addField" data-add="pushActionButton"><?php esc_html_e('+ Add Action Button', $this->textDomain); ?></span>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('If enabled, your notification will vibrate the user\'s device when delivered.', $this->textDomain); ?></p>
                                <label for="pushVibrate" class="daftplugAdminField_label -flex4"><?php esc_html_e('Vibration', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pushVibrate" id="pushVibrate" class="daftplugAdminInputCheckbox_field">
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('If enabled, your notification will not hide automatically after some time and it will require user interaction to be dismissed.', $this->textDomain); ?></p>
                                <label for="pushFixed" class="daftplugAdminField_label -flex4"><?php esc_html_e('Fixed Notification', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pushFixed" id="pushFixed" class="daftplugAdminInputCheckbox_field">
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php printf(__('If enabled, you will be able to schedule your push notification to be delivered on a specific date and time instead of immediate delivery. Please note that your Timezone must match with your <a class="daftplugAdminLink" href="%s" target="_blank">WordPress Timezone Settings</a> in order to schedule push notifications.', $this->textDomain), admin_url('/options-general.php')); ?></p>
                                <label for="pushScheduled" class="daftplugAdminField_label -flex4"><?php esc_html_e('Scheduled Delivery', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pushScheduled" id="pushScheduled" class="daftplugAdminInputCheckbox_field">
                                </label>
                            </div>
                            <div class="daftplugAdminField -pushScheduledDependentHideD">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the date and time when you want this push notification to be delivered.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputText -flexAuto">
                                    <input type="datetime-local" name="pushScheduledDatetime" id="pushScheduledDatetime" class="daftplugAdminInputText_field" value="" min="<?php echo date('Y-m-d\Th:i'); ?>" data-placeholder="<?php esc_html_e('Delivery Date & Time', $this->textDomain); ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <div class="daftplugAdminField_response -flex7"></div>
                                <div class="daftplugAdminField_submit -flex5">
                                    <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Send Notification', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Sending', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Notification Sent', $this->textDomain); ?>" data-failed="<?php esc_html_e('Sending Failed', $this->textDomain); ?>"></button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>       
            </div>
            <div class="daftplugAdminPopup" data-popup="scheduledPushModal">
                <div class="daftplugAdminPopup_container">
                    <form name="daftplugAdminScheduledPush_form" class="daftplugAdminScheduledPush_form" spellcheck="false" autocomplete="off">
                        <fieldset class="daftplugAdminFieldset">
                            <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Edit Scheduled Push Notification', $this->textDomain); ?></h4>
                            <div class="daftplugAdminField" style="justify-content:center">
                                <button type="button" class="daftplugAdminButton -preview"><?php esc_html_e('Preview Notification', $this->textDomain); ?></button>
                            </div>
                            <input type="hidden" name="sig" id="sig" value="">
                            <input type="hidden" name="time" id="time" value="">
                            <input type="hidden" name="args" id="args" value="">
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select what segment of your subscribers should receive this notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputSelect -flexAuto">
                                    <select name="pushSegment" id="pushSegment" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Segment', $this->textDomain); ?>" autocomplete="off" required>
                                        <option value="all"><?php esc_html_e('All Users', $this->textDomain); ?></option>
                                        <option value="mobile"><?php esc_html_e('Mobile Users', $this->textDomain); ?></option>
                                        <option value="desktop"><?php esc_html_e('Desktop Users', $this->textDomain); ?></option>
                                        <option value="registered"><?php esc_html_e('Registered Users', $this->textDomain); ?></option>
                                        <option value="unregistered"><?php esc_html_e('Unregistered Users', $this->textDomain); ?></option>
                                        <?php $subscribedCountries = array();foreach($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices as $subscribedDevice) {$subscribedCountries[] = $subscribedDevice['country'];}$subscribedCountries = array_unique($subscribedCountries);unset($subscribedCountries[__('Unknown', $this->textDomain)]);foreach($subscribedCountries as $subscribedCountry) {
                                            echo '<option value="Country - '.$subscribedCountry.'">'.sprintf(__('Users from %s', $this->textDomain), $subscribedCountry).'</option>';
                                        } foreach($this->daftplugInstantifyPwaAdminPushnotifications::$subscribedDevices as $subscribedDevice) { if(array_key_exists('user', $subscribedDevice)){if(is_numeric($subscribedDevice['user'])){$userName = get_userdata($subscribedDevice['user'])->display_name;}else{$userName = $subscribedDevice['user'];}}else{$userName = esc_html__('Unknown', $this->textDomain);}?>
                                            <option value="<?php echo $subscribedDevice['endpoint']; ?>"><?php echo $subscribedDevice['deviceInfo'].' / '.$subscribedDevice['date'].' / '.(array_key_exists('country', $subscribedDevice) ? $subscribedDevice['country'] : __('Unknown', $this->textDomain)).' / '.$userName; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the title of your notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputText -flexAuto">
                                    <input type="text" name="pushTitle" id="pushTitle" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('Title', $this->textDomain); ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the body of your notification (text description).', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputTextarea -flexAuto">
                                    <textarea name="pushBody" id="pushBody" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Body', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the image of notification. it will be displayed as large image on notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputUpload -flexAuto">
                                    <input type="text" name="pushImage" id="pushImage" class="daftplugAdminInputUpload_field" value="" data-mimes="png,jpg,jpeg" data-min-width="50" data-max-width="" data-min-height="50" data-max-height="" data-attach-url="">
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the redirect URL of your notification. Your users will be redirected here after they click on your notification.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputText -flexAuto">
                                    <input type="url" name="pushUrl" id="pushUrl" class="daftplugAdminInputText_field" value="<?php echo trailingslashit(strtok(home_url('/', 'https'), '?')); ?>" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Select the icon of notification. We recommend to use your website logo or site icon.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputUpload -flexAuto">
                                    <input type="text" name="pushIcon" id="pushIcon" class="daftplugAdminInputUpload_field" value="" data-mimes="png,jpg,jpeg" data-min-width="50" data-max-width="" data-min-height="50" data-max-height="" data-attach-url="">
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('You can add up to two action buttons to the notification. Currently action buttons are only supported by Chrome browsers.', $this->textDomain); ?></p>
                            </div>
                            <?php for ($ab = 1; $ab <= 2; $ab++) { ?>
                            <fieldset class="daftplugAdminFieldset -miniFieldset -pushActionButton<?php echo $ab; ?>">
                                <h5 class="daftplugAdminFieldset_title"><?php printf(__('Action Button %s', $this->textDomain), $ab); ?></h5>
                                <label class="daftplugAdminInputCheckbox -flexAuto -hidden">
                                    <input type="checkbox" name="pushActionButton<?php echo $ab; ?>" id="pushActionButton<?php echo $ab; ?>" class="daftplugAdminInputCheckbox_field">
                                </label>
                                <div class="daftplugAdminField">
                                    <div class="daftplugAdminInputText -flexAuto">
                                        <input type="text" name="pushActionButton<?php echo $ab; ?>Text" id="pushActionButton<?php echo $ab; ?>Text" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('Text', $this->textDomain); ?>" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="daftplugAdminField">
                                    <div class="daftplugAdminInputText -flexAuto">
                                        <input type="url" name="pushActionButton<?php echo $ab; ?>Url" id="pushActionButton<?php echo $ab; ?>Url" class="daftplugAdminInputText_field" value="" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off" required>
                                    </div>
                                </div>
                            </fieldset>     
                            <?php } ?>
                            <div class="daftplugAdminField">
                                <div class="daftplugAdminInputAddField -flexAuto">
                                    <span class="daftplugAdminButton -addField" data-add="pushActionButton"><?php esc_html_e('+ Add Action Button', $this->textDomain); ?></span>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('If enabled, your notification will vibrate the user\'s device when delivered.', $this->textDomain); ?></p>
                                <label for="pushVibrate" class="daftplugAdminField_label -flex4"><?php esc_html_e('Vibration', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pushVibrate" id="pushVibrate" class="daftplugAdminInputCheckbox_field">
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('If enabled, your notification will not hide automatically after some time and it will require user interaction to be dismissed.', $this->textDomain); ?></p>
                                <label for="pushFixed" class="daftplugAdminField_label -flex4"><?php esc_html_e('Fixed Notification', $this->textDomain); ?></label>
                                <label class="daftplugAdminInputCheckbox -flexAuto">
                                    <input type="checkbox" name="pushFixed" id="pushFixed" class="daftplugAdminInputCheckbox_field">
                                </label>
                            </div>
                            <div class="daftplugAdminField">
                                <p class="daftplugAdminField_description"><?php esc_html_e('Enter the date and time when you want this push notification to be delivered.', $this->textDomain); ?></p>
                                <div class="daftplugAdminInputText -flexAuto">
                                    <input type="datetime-local" name="pushScheduledDatetime" id="pushScheduledDatetime" class="daftplugAdminInputText_field" value="" min="<?php echo date('Y-m-d\Th:i'); ?>" data-placeholder="<?php esc_html_e('Delivery Date & Time', $this->textDomain); ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="daftplugAdminField">
                                <div class="daftplugAdminField_response -flex6"></div>
                                <div class="daftplugAdminField_submit -flex6">
                                    <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Schedule Notification', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Scheduling', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Notification Scheduled', $this->textDomain); ?>" data-failed="<?php esc_html_e('Scheduling Failed', $this->textDomain); ?>"></button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <?php
        } else {
            ?>
            <fieldset class="daftplugAdminFieldset">
                <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Unsupported PHP version or missing extensions', $this->textDomain); ?></h4>
                <p class="daftplugAdminFieldset_description"><?php _e('Push Notification features need PHP version 7.3 or higher <i style="font-style: italic;">mbstring</i> and <i style="font-style: italic;">openssl</i> extensions to function properly. If you\'re having trouble to update your PHP version or enable extensions, please contact your hosting provider to do this in your stead.', $this->textDomain); ?></p>
            </fieldset>
            <?php
        }
    } else {
    ?>
        <fieldset class="daftplugAdminFieldset">
            <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Push Notifications Plugin Detected', $this->textDomain); ?></h4>
            <p class="daftplugAdminFieldset_description"><?php _e('You are using a 3rd-party Push Notifications as a push notification service, so this section is disabled. If you want to use Instantify\'s Push Notifications feature, please disable your current Push Notifications plugin and visit this section again.', $this->textDomain); ?></p>
        </fieldset>
    <?php
    }
    ?>
    </div>
</div>