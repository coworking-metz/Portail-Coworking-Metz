<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -feed -flex12" data-subpage="feed" data-title="<?php esc_html_e('Feed', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="fbiaFeedFeedPreferences">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Feed Preferences', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you are allowed to set FBIA feed preferences. You can assign your Instant Articles a custom style, set copyright details, enable RTL publishing and define number of latest articles in your feed and choose update frequency of the feed.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter the name of your custom article style.', $this->textDomain); ?></p>
                        <label for="fbiaCopyright" class="daftplugAdminField_label -flex4"><?php esc_html_e('Article Style', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="fbiaArticleStyle" id="fbiaArticleStyle" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('fbiaArticleStyle'); ?>" data-placeholder="<?php esc_html_e('Article Style', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter the standard copyright details for your instant articles.', $this->textDomain); ?></p>
                        <label for="fbiaCopyright" class="daftplugAdminField_label -flex4"><?php esc_html_e('Copyright', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="fbiaCopyright" id="fbiaCopyright" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Copyright', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4"><?php echo daftplugInstantify::getSetting('fbiaCopyright'); ?></textarea>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable Right to Left Publishing for Arabic, Hebrew, Persian and other languages.', $this->textDomain); ?></p>
                        <label for="fbiaRtlPublishing" class="daftplugAdminField_label -flex4"><?php esc_html_e('RTL Publishing', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="fbiaRtlPublishing" id="fbiaRtlPublishing" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbiaRtlPublishing'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Choose the number of latest articles, that will be rendered on your FBIA feed.', $this->textDomain); ?></p>
                        <label for="fbiaArticleQuantity" class="daftplugAdminField_label -flex4"><?php esc_html_e('Article Quantity', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputRange -flexAuto">
                            <input type="range" name="fbiaArticleQuantity" id="fbiaArticleQuantity" class="daftplugAdminInputRange_field" value="<?php echo daftplugInstantify::getSetting('fbiaArticleQuantity'); ?>" min="1" step="1" max="100" data-placeholder="<?php esc_html_e('Article Quantity', $this->textDomain); ?>" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select update frequency of the Instant Articles feed. Lower value will update your feed more frequently, but it might increase the server load.', $this->textDomain); ?></p>
                        <label for="ampMode" class="daftplugAdminField_label -flex4"><?php esc_html_e('Update Frequency', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="fbiaUpdateFrequency" id="fbiaUpdateFrequency" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Update Frequency', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="5" <?php selected('5', daftplugInstantify::getSetting('fbiaUpdateFrequency')) ?>><?php esc_html_e('Once every 5 hours', $this->textDomain); ?></option>
                                <option value="4" <?php selected('4', daftplugInstantify::getSetting('fbiaUpdateFrequency')) ?>><?php esc_html_e('Once every 4 hours', $this->textDomain); ?></option>
                                <option value="3" <?php selected('3', daftplugInstantify::getSetting('fbiaUpdateFrequency')) ?>><?php esc_html_e('Once every 3 hours', $this->textDomain); ?></option>
                                <option value="2" <?php selected('2', daftplugInstantify::getSetting('fbiaUpdateFrequency')) ?>><?php esc_html_e('Once every 2 hours', $this->textDomain); ?></option>
                                <option value="1" <?php selected('1', daftplugInstantify::getSetting('fbiaUpdateFrequency')) ?>><?php esc_html_e('Once every 1 hours', $this->textDomain); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Article interaction allow users to like and comment the whole single article in your Facebook Instant Articles feed.', $this->textDomain); ?></p>
                        <label for="fbiaArticleInteraction" class="daftplugAdminField_label -flex4"><?php esc_html_e('Article Interaction', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="fbiaArticleInteraction" id="fbiaArticleInteraction" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbiaArticleInteraction'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Related articles option displays four related articles to the current article in the footer.', $this->textDomain); ?></p>
                        <label for="fbiaRelatedArticles" class="daftplugAdminField_label -flex4"><?php esc_html_e('Related Articles', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="fbiaRelatedArticles" id="fbiaRelatedArticles" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbiaRelatedArticles'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php _e('Instantify is using <a class="daftplugAdminLink" href="https://developers.facebook.com/docs/instant-articles/sdk/transformer-rules/" target="_blank">Official Facebook SDK</a> to transform your articles into Instant Articles format. Here you can define your custom transforming rules.', $this->textDomain); ?></p>
                        <label for="fbiaCustomRules" class="daftplugAdminField_label -flex4"><?php esc_html_e('Custom Transformer Rules', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="fbiaCustomRules" id="fbiaCustomRules" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4"><?php echo htmlspecialchars(wp_unslash(daftplugInstantify::getSetting('fbiaCustomRules'))); ?></textarea>
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