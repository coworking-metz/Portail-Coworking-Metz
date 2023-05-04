<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -advertisements -flex12" data-subpage="advertisements" data-title="<?php esc_html_e('Advertisements', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="fbiaAdvertisementsAudienceNetwork">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Facebook Audience Network', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('Facebook Audience Network helps you monetize your Instant Articles by showing highly targeted ads that match the interests of your users.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Facebook Audience Network.', $this->textDomain); ?></p>
                        <label for="fbiaAudienceNetwork" class="daftplugAdminField_label -flex4"><?php esc_html_e('Audience Network', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="fbiaAudienceNetwork" id="fbiaAudienceNetwork" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbiaAudienceNetwork'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -fbiaAudienceNetworkDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter the placement ID of your ad. It will be automatically reused and placed within your Instant Articles in order to simplify the process of inserting blocks of ad code throughout the content.', $this->textDomain); ?></p>
                        <label for="fbiaAudienceNetworkPlacementId" class="daftplugAdminField_label -flex4"><?php esc_html_e('Placement ID', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="fbiaAudienceNetworkPlacementId" id="fbiaAudienceNetworkPlacementId" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('fbiaAudienceNetworkPlacementId'); ?>" data-placeholder="<?php esc_html_e('Placement ID', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="fbiaAdvertisementsCustomAdEmbed">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Custom Ad Embed', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you are able to monetize your Instant Articles using any kind of ad network\'s advertisement embed code.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Custom Ad Embed.', $this->textDomain); ?></p>
                        <label for="fbiaCustomAdEmbed" class="daftplugAdminField_label -flex4"><?php esc_html_e('Custom Ad Embed', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="fbiaCustomAdEmbed" id="fbiaCustomAdEmbed" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbiaCustomAdEmbed'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -fbiaCustomAdEmbedDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your custom ad embed code.', $this->textDomain); ?></p>
                        <label for="fbiaCustomAdEmbedCode" class="daftplugAdminField_label -flex4"><?php esc_html_e('Custom Ad Embed Code', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="fbiaCustomAdEmbedCode" id="fbiaCustomAdEmbedCode" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Custom Ad Embed Code', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" required><?php echo daftplugInstantify::getSetting('fbiaCustomAdEmbedCode'); ?></textarea>
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