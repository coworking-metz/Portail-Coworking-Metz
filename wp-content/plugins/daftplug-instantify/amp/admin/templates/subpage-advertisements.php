<?php

if (!defined('ABSPATH')) exit;

?>
<div class="daftplugAdminPage_subpage -advertisements -flex12" data-subpage="advertisements" data-title="<?php esc_html_e('Advertisements', $this->textDomain); ?>">
	<div class="daftplugAdminPage_content -flex8">
        <div class="daftplugAdminSettings -flexAuto">
            <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                <fieldset class="daftplugAdminFieldset" id="ampAdvertisementsAdSenseAutoAds">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('AdSense Auto Ads', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('From this section you can set up automatic AdSense ads. Using machine learning, Auto ads show ads only when they are likely to perform well and provide a good user experience.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable AdSense auto ads.', $this->textDomain); ?></p>
                        <label for="ampAdSenseAutoAds" class="daftplugAdminField_label -flex4"><?php esc_html_e('AdSense Auto Ads', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampAdSenseAutoAds" id="ampAdSenseAutoAds" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampAdSenseAutoAds'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampAdSenseAutoAdsDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-client from the AdSense ad code. It\'s like ca-pub-3087XXXXXXXXX642.', $this->textDomain); ?></p>
                        <label for="ampAdSenseAutoAdsClient" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Client', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdSenseAutoAdsClient" id="ampAdSenseAutoAdsClient" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdSenseAutoAdsClient'); ?>" data-placeholder="<?php esc_html_e('Data Ad Client', $this->textDomain); ?>" autocomplete="off" data-required="true" required="">
                        </label>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAdvertisementsAdAboveContent">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Advertisement Above Content', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('This AdSense advertisement will show up above your content on your AMP pages.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable advertisement above the content.', $this->textDomain); ?></p>
                        <label for="ampAdAboveContent" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ad Above Content', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampAdAboveContent" id="ampAdAboveContent" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampAdAboveContent'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampAdAboveContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the size of the advertisement.', $this->textDomain); ?></p>
                        <label for="ampAdAboveContentSize" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ad Size', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="ampAdAboveContentSize" id="ampAdAboveContentSize" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Ad Size', $this->textDomain); ?>" autocomplete="off" required>
                                <option value="responsive" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), 'responsive') ?>><?php esc_html_e('Responsive', $this->textDomain); ?></option>
                                <option value="300x250" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '300x250') ?>>300x250</option>
                                <option value="336x280" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '336x280') ?>>336x280</option>
                                <option value="728x90" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '728x90') ?>>728x90</option>
                                <option value="300x600" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '300x600') ?>>300x600</option>
                                <option value="320x100" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '320x100') ?>>320x100</option>
                                <option value="200x50" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '200x50') ?>>200x50</option>
                                <option value="320x50" <?php selected(daftplugInstantify::getSetting('ampAdAboveContentSize'), '320x50') ?>>320x50</option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAdAboveContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-client from the AdSense ad code. It\'s like ca-pub-3087XXXXXXXXX642.', $this->textDomain); ?></p>
                        <label for="ampAdAboveContentClient" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Client', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdAboveContentClient" id="ampAdAboveContentClient" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdAboveContentClient'); ?>" data-placeholder="<?php esc_html_e('Data Ad Client', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAdAboveContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-slot from the AdSense ad code. It\'s like 82XXXXXX13.', $this->textDomain); ?></p>
                        <label for="ampAdAboveContentSlot" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Slot', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdAboveContentSlot" id="ampAdAboveContentSlot" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdAboveContentSlot'); ?>" data-placeholder="<?php esc_html_e('Data Ad Slot', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAdvertisementsAdInsideContent">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Advertisement Inside Content', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('This AdSense advertisement will show up inside your content on your AMP pages.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable advertisement inside the content.', $this->textDomain); ?></p>
                        <label for="ampAdInsideContent" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ad Inside Content', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampAdInsideContent" id="ampAdInsideContent" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampAdInsideContent'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampAdInsideContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the size of the advertisement.', $this->textDomain); ?></p>
                        <label for="ampAdInsideContentSize" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ad Size', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="ampAdInsideContentSize" id="ampAdInsideContentSize" class="daftplugAdminInputSelect_field" data-placeholder="Ad Size" autocomplete="off" required>
                                <option value="responsive" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), 'responsive') ?>><?php esc_html_e('Responsive', $this->textDomain); ?></option>
                                <option value="300x250" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '300x250') ?>>300x250</option>
                                <option value="336x280" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '336x280') ?>>336x280</option>
                                <option value="728x90" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '728x90') ?>>728x90</option>
                                <option value="300x600" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '300x600') ?>>300x600</option>
                                <option value="320x100" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '320x100') ?>>320x100</option>
                                <option value="200x50" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '200x50') ?>>200x50</option>
                                <option value="320x50" <?php selected(daftplugInstantify::getSetting('ampAdInsideContentSize'), '320x50') ?>>320x50</option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAdInsideContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-client from the AdSense ad code. It\'s like ca-pub-3087XXXXXXXXX642.', $this->textDomain); ?></p>
                        <label for="ampAdInsideContentClient" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Client', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdInsideContentClient" id="ampAdInsideContentClient" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdInsideContentClient'); ?>" data-placeholder="<?php esc_html_e('Data Ad Client', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAdInsideContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-slot from the AdSense ad code. It\'s like 82XXXXXX13.', $this->textDomain); ?></p>
                        <label for="ampAdInsideContentSlot" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Slot', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdInsideContentSlot" id="ampAdInsideContentSlot" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdInsideContentSlot'); ?>" data-placeholder="<?php esc_html_e('Data Ad Slot', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="daftplugAdminFieldset" id="ampAdvertisementsAdBelowContent">
                    <h4 class="daftplugAdminFieldset_title"><?php esc_html_e('Advertisement Below Content', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('This AdSense advertisement will show up below your content on your AMP pages.', $this->textDomain); ?></p>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enable or disable advertisement below the content.', $this->textDomain); ?></p>
                        <label for="ampAdBelowContent" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ad Below Content', $this->textDomain); ?></label>
                        <label class="daftplugAdminInputCheckbox -flexAuto">
                            <input type="checkbox" name="ampAdBelowContent" id="ampAdBelowContent" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('ampAdBelowContent'), 'on'); ?>>
                        </label>
                    </div>
                    <div class="daftplugAdminField -ampAdBelowContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Select the size of the advertisement.', $this->textDomain); ?></p>
                        <label for="ampAdBelowContentSize" class="daftplugAdminField_label -flex4"><?php esc_html_e('Ad Size', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputSelect -flexAuto">
                            <select name="ampAdBelowContentSize" id="ampAdBelowContentSize" class="daftplugAdminInputSelect_field" data-placeholder="Ad Size" autocomplete="off" required>
                                <option value="responsive" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), 'responsive') ?>><?php esc_html_e('Responsive', $this->textDomain); ?></option>
                                <option value="300x250" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '300x250') ?>>300x250</option>
                                <option value="336x280" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '336x280') ?>>336x280</option>
                                <option value="728x90" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '728x90') ?>>728x90</option>
                                <option value="300x600" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '300x600') ?>>300x600</option>
                                <option value="320x100" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '320x100') ?>>320x100</option>
                                <option value="200x50" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '200x50') ?>>200x50</option>
                                <option value="320x50" <?php selected(daftplugInstantify::getSetting('ampAdBelowContentSize'), '320x50') ?>>320x50</option>
                            </select>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAdBelowContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-client from the AdSense ad code. It\'s like ca-pub-3087XXXXXXXXX642.', $this->textDomain); ?></p>
                        <label for="ampAdBelowContentClient" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Client', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdBelowContentClient" id="ampAdBelowContentClient" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdBelowContentClient'); ?>" data-placeholder="<?php esc_html_e('Data Ad Client', $this->textDomain); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField -ampAdBelowContentDependentDisableD">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your data-ad-slot from the AdSense ad code. It\'s like 82XXXXXX13.', $this->textDomain); ?></p>
                        <label for="ampAdBelowContentSlot" class="daftplugAdminField_label -flex4"><?php esc_html_e('Data Ad Slot', $this->textDomain); ?></label>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="ampAdBelowContentSlot" id="ampAdBelowContentSlot" class="daftplugAdminInputText_field" value="<?php echo daftplugInstantify::getSetting('ampAdBelowContentSlot'); ?>" data-placeholder="<?php esc_html_e('Data Ad Slot', $this->textDomain); ?>" autocomplete="off" required>
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