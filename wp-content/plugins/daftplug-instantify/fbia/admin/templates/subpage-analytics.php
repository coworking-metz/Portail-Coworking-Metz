<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -analytics -flex12" data-subpage="analytics" data-title="<?php esc_html_e('Analytics', $this->textDomain); ?>">
    <div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="fbiaAnalyticsAnalytics">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Analytics', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Instant Articles allow publishers to track the engagement with their articles through data that Facebook provides as well as data that publishers can gather by embedding in-house analytics tools or third-party measurement services.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable third-party analytics for Instant Articles.', $this->textDomain); ?></p>
                        <label for="fbiaAnalytics" class="daftplugAdminField_label -flex4"><?php esc_html_e('Analytics', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="fbiaAnalytics" id="fbiaAnalytics" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbiaAnalytics'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -fbiaAnalyticsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter the full analytics code for any other analytics services you wish to use, for example Google Analytics code.', $this->textDomain); ?></p>
                        <label for="fbiaAnalyticsCode" class="daftplugAdminField_label -flex4"><?php esc_html_e('Analytics Code', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="fbiaAnalyticsCode" id="fbiaAnalyticsCode" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Analytics Code', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" required><?php echo daftplugInstantify::getSetting('fbiaAnalyticsCode'); ?></textarea>
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