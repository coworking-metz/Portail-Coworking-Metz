<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -gdprconsent -flex12" data-subpage="gdprconsent" data-title="<?php esc_html_e('GDPR Consent', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="ampGdprconsentCookieNotice">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Cookie Notice', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Cookie Notice allows you to elegantly inform users on AMP pages that your site uses cookies and to comply with the EU cookie law GDPR regulations.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Cookie notice.', $this->textDomain); ?></p>
                        <label for="ampCookieNotice" class="daftplugAdminField_label -flex4"><?php esc_html_e('Cookie Notice', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampCookieNotice" id="ampCookieNotice" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampCookieNotice'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampCookieNoticeDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter the message about cookie usage on your website.', $this->textDomain); ?></p>
                        <label for="ampCookieNoticeMessage" class="daftplugAdminField_label -flex4"><?php esc_html_e('Message', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="ampCookieNoticeMessage" id="ampCookieNoticeMessage" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Message', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" required><?php echo daftplugInstantify::getSetting('ampCookieNoticeMessage'); ?></textarea>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampCookieNoticeDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter the button text which will appear on the notice.', $this->textDomain); ?></p>
                        <label for="ampCookieNoticeButtonText" class="daftplugAdminField_label -flex4"><?php esc_html_e('Button Text', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampCookieNoticeButtonText" id="ampCookieNoticeButtonText" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampCookieNoticeButtonText'); ?>" data-placeholder="<?php esc_html_e('Button Text', $this->textDomain); ?>" autocomplete="off" required>
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