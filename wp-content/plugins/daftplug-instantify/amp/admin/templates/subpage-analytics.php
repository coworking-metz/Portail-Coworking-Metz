<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -analytics -flex12" data-subpage="analytics" data-title="<?php esc_html_e('Analytics', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsGoogleAnalytics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Google Analytics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Google Analytics tracking on your AMP pages. To use Google Analytics, you have to enter your Tracking ID.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Google Analytics.', $this->textDomain); ?></p>
                        <label for="ampGoogleAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Google Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampGoogleAnalytics" id="ampGoogleAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampGoogleAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampGoogleAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Google Analytics tracking ID. It\'s like UA-XXXXX-Y.', $this->textDomain); ?></p>
                        <label for="ampGoogleAnalyticsTrackingId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Tracking ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampGoogleAnalyticsTrackingId" id="ampGoogleAnalyticsTrackingId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampGoogleAnalyticsTrackingId'); ?>" data-placeholder="<?php esc_html_e('Tracking ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampGoogleAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable AMP Linker. It is a new feature in AMP that helps keep user session in sync.', $this->textDomain); ?></p>
                        <label for="ampGoogleAnalyticsAmpLinker" class="daftplugAdminField_label -flex4"><?php esc_html_e('AMP Linker', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampGoogleAnalyticsAmpLinker" id="ampGoogleAnalyticsAmpLinker" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampGoogleAnalyticsAmpLinker'), 'on'); ?>>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsFacebookPixel">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Facebook Pixel', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Facebook Pixel tracking on your AMP pages. To use Facebook Pixel, you have to enter your Facebook Pixel ID.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Facebook Pixel.', $this->textDomain); ?></p>
                        <label for="ampFacebookPixel" class="daftplugAdminField_label -flex4"><?php esc_html_e('Facebook Pixel', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampFacebookPixel" id="ampFacebookPixel" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampFacebookPixel'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampFacebookPixelDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Facebook Pixel ID. It\'s like 15XXXXXXXXXXX48.', $this->textDomain); ?></p>
                        <label for="ampFacebookPixelId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Pixel ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampFacebookPixelId" id="ampFacebookPixelId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampFacebookPixelId'); ?>" data-placeholder="<?php esc_html_e('Pixel ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsSegmentAnalytics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Segment Analytics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Segment Analytics tracking on your AMP pages. To use Segment Analytics, you have to enter your Segment Analytics write key.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Segment Analytics.', $this->textDomain); ?></p>
                        <label for="ampSegmentAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Segment Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampSegmentAnalytics" id="ampSegmentAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampSegmentAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampSegmentAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Segment Analytics write key.', $this->textDomain); ?></p>
                        <label for="ampSegmentAnalyticsWriteKey" class="daftplugAdminField_label -flex4"><?php esc_html_e('Write Key', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampSegmentAnalyticsWriteKey" id="ampSegmentAnalyticsWriteKey" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampSegmentAnalyticsWriteKey'); ?>" data-placeholder="<?php esc_html_e('Write Key', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsStatCounter">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('StatCounter', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use StatCounter tracking on your AMP pages. To use StatCounter, you have to enter your StatCounter URL.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable StatCounter.', $this->textDomain); ?></p>
                        <label for="ampStatCounter" class="daftplugAdminField_label -flex4"><?php esc_html_e('StatCounter', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampStatCounter" id="ampStatCounter" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampStatCounter'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampStatCounterDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your StatCounter URL.', $this->textDomain); ?></p>
                        <label for="ampStatCounterUrl" class="daftplugAdminField_label -flex4"><?php esc_html_e('URL', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="url" name="ampStatCounterUrl" id="ampStatCounterUrl" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampStatCounterUrl'); ?>" data-placeholder="<?php esc_html_e('URL', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsHistatsAnalytics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Histats Analytics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Histats Analytics tracking on your AMP pages. To use Histats Analytics, you have to enter your Histats Analytics ID.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Histats Analytics.', $this->textDomain); ?></p>
                        <label for="ampHistatsAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Histats Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampHistatsAnalytics" id="ampHistatsAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampHistatsAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampHistatsAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Histats Analytics ID.', $this->textDomain); ?></p>
                        <label for="ampHistatsAnalyticsId" class="daftplugAdminField_label -flex4"><?php esc_html_e('ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampHistatsAnalyticsId" id="ampHistatsAnalyticsId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampHistatsAnalyticsId'); ?>" data-placeholder="<?php esc_html_e('ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsYandexMetrika">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Yandex Metrika', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Yandex Metrika tracking on your AMP pages. To use Yandex Metrika, you have to enter your Counter ID.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Yandex Metrika.', $this->textDomain); ?></p>
                        <label for="ampYandexMetrika" class="daftplugAdminField_label -flex4"><?php esc_html_e('Yandex Metrika', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampYandexMetrika" id="ampYandexMetrika" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampYandexMetrika'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampYandexMetrikaDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Counter ID.', $this->textDomain); ?></p>
                        <label for="ampYandexMetrikaCounterId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Counter ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampYandexMetrikaCounterId" id="ampYandexMetrikaCounterId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampYandexMetrikaCounterId'); ?>" data-placeholder="<?php esc_html_e('Counter ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsChartbeatAnalytics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Chartbeat Analytics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Chartbeat Analytics tracking on your AMP pages. To use Chartbeat Analytics, you have to enter your Account ID.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Chartbeat Analytics.', $this->textDomain); ?></p>
                        <label for="ampChartbeatAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Chartbeat Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampChartbeatAnalytics" id="ampChartbeatAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampChartbeatAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampChartbeatAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Account ID.', $this->textDomain); ?></p>
                        <label for="ampChartbeatAnalyticsAccountId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Account ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampChartbeatAnalyticsAccountId" id="ampChartbeatAnalyticsAccountId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampChartbeatAnalyticsAccountId'); ?>" data-placeholder="<?php esc_html_e('Account ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsClickyAnalytics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Clicky Analytics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Clicky Analytics tracking on your AMP pages. To use Clicky Analytics, you have to enter your Site ID.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Clicky Analytics.', $this->textDomain); ?></p>
                        <label for="ampClickyAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Clicky Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampClickyAnalytics" id="ampClickyAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampClickyAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampClickyAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Site ID.', $this->textDomain); ?></p>
                        <label for="ampClickyAnalyticsSiteId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Site ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampClickyAnalyticsSiteId" id="ampClickyAnalyticsSiteId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampClickyAnalyticsSiteId'); ?>" data-placeholder="<?php esc_html_e('Site ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAnalyticsAlexaMetrics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Alexa Metrics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are able to use Alexa Metrics tracking on your AMP pages. To use Alexa Metrics, you have to enter your Account and Domain.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Alexa Metrics.', $this->textDomain); ?></p>
                        <label for="ampAlexaMetrics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Alexa Metrics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampAlexaMetrics" id="ampAlexaMetrics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampAlexaMetrics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampAlexaMetricsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Account.', $this->textDomain); ?></p>
                        <label for="ampAlexaMetricsAccount" class="daftplugAdminField_label -flex4"><?php esc_html_e('Account', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAlexaMetricsAccount" id="ampAlexaMetricsAccount" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAlexaMetricsAccount'); ?>" data-placeholder="<?php esc_html_e('Account', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAlexaMetricsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Domain.', $this->textDomain); ?></p>
                        <label for="ampAlexaMetricsDomain" class="daftplugAdminField_label -flex4"><?php esc_html_e('Domain', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAlexaMetricsDomain" id="ampAlexaMetricsDomain" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAlexaMetricsDomain'); ?>" data-placeholder="<?php esc_html_e('Domain', $this->textDomain); ?>" autocomplete="off" required>
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