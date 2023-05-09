<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -overview" data-page="overview" data-title="<?php esc_html_e('Overview', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <img class="daftplugAdminPage_illustration" src="<?php echo plugins_url('admin/assets/img/illustration-overview.png', $this->pluginFile)?>"/>
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Overview', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php printf(__('Welcome to <strong>%s</strong> plugin. Here you may find status, analytics, warnings or any other information related to the plugin.', $this->textDomain), $this->name); ?></h5>
    </div>
    <div class="daftplugAdminPage_content -flex9 -getAppNoticeTable">
        <div class="daftplugAdminGetAppNotice">
             <div class="daftplugAdminGetAppNotice_image -flex6">
                <h6 class="daftplugAdminNotice_appname1"><?php echo daftplugInstantify::getSetting('pwaName'); ?></h6>
                <p class="daftplugAdminNotice_appdesc1"><?php echo daftplugInstantify::getSetting('pwaDescription'); ?></p>
                <img class="daftplugAdminNotice_appicon1" src="<?php echo wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0] ?? ''; ?>"/>
                <img class="daftplugAdminNotice_appscreenshot1" src="https://s0.wp.com/mshots/v1/<?php echo trailingslashit(daftplugInstantify::getSetting('pwaStartPage')); ?>?vpw=750&vph=1334"/>
                <h6 class="daftplugAdminNotice_appname2"><?php echo daftplugInstantify::getSetting('pwaName'); ?></h6>
                <p class="daftplugAdminNotice_appdesc2"><?php echo daftplugInstantify::getSetting('pwaDescription'); ?></p>
                <img class="daftplugAdminNotice_appicon2" src="<?php echo wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0] ?? ''; ?>"/>
                <img class="daftplugAdminNotice_appscreenshot2" src="https://s0.wp.com/mshots/v1/<?php echo trailingslashit(daftplugInstantify::getSetting('pwaStartPage')); ?>?vpw=750&vph=1334"/>
                <img class="daftplugAdminGetAppNotice_frame" src="<?php echo plugins_url('admin/assets/img/image-appframes.png', $this->pluginFile); ?>"/>
            </div>
            <div class="daftplugAdminGetAppNotice_text -flex7 -textCenter">
                <h3 class="daftplugAdminGetAppNotice_title"><?php esc_html_e('Get Android & iOS PWA Apps', $this->textDomain); ?></h3>
                <p class="daftplugAdminGetAppNotice_desc"><?php esc_html_e('Publish your PWA website into Google Play Store as a native Android app and into Apple App Store as a native iOS app to reach more users on all platforms. We can convert your PWA website into Google Play ready Android app package on top of TWA (Trusted Web Activity) technology and into App Store ready iOS app package on top of WebView technology. You will get ready-made app files with guides to submit them to Play and App Stores.', $this->textDomain); ?></p>
                <span class="daftplugAdminButton -notice"><?php esc_html_e('Get Android & iOS Apps', $this->textDomain); ?></span>
            </div>
        </div>
        <div class="daftplugAdminPricingTable">
            <div class="daftplugAdminPricingTable_item -android">
                <h3 class="daftplugAdminPricingTable_title">Android</h3>
                <img class="daftplugAdminPricingTable_icon" src="<?php echo plugins_url('admin/assets/img/icon-android.png', $this->pluginFile); ?>"/>
                <div class="daftplugAdminPricingTable_price">
                    <sup class="daftplugAdminPricingTable_currency">$</sup>
                    <span class="daftplugAdminPricingTable_amount">19</span>
                </div>
                <ul class="daftplugAdminPricingTable_features">
                    <li class="daftplugAdminPricingTable_feature" style="font-weight: 900;" data-tooltip="<?php esc_html_e('Publish your app on this platform.', $this->textDomain); ?>" data-tooltip-flow="bottom">Google Play</li>
                    <li class="daftplugAdminPricingTable_feature" data-tooltip="<?php esc_html_e('No monthly or recurring payments.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('One-Time Payment', $this->textDomain); ?></li>
                    <li class="daftplugAdminPricingTable_feature" data-tooltip="<?php esc_html_e('All changes are automatically reflected.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('No Updates Needed', $this->textDomain); ?></li>
                </ul>
                <span class="daftplugAdminButton" data-open-popup="getAndroidAppModal"><?php esc_html_e('Get Android App', $this->textDomain); ?></span>
            </div>
            <div class="daftplugAdminPricingTable_item -androios">
                <div class="daftplugAdminPricingTable_ribbon">
                    <div class="daftplugAdminPricingTable_minitext"><?php esc_html_e('BEST VALUE', $this->textDomain); ?></div>
                </div>
                <h3 class="daftplugAdminPricingTable_title">Android & iOS</h3>
                <img class="daftplugAdminPricingTable_icon" src="<?php echo plugins_url('admin/assets/img/icon-androios.png', $this->pluginFile); ?>" style="padding: 10px;"/>
                <div class="daftplugAdminPricingTable_price">
                    <sup class="daftplugAdminPricingTable_currency">$</sup>
                    <span class="daftplugAdminPricingTable_amount">37</span>
                </div>
                <ul class="daftplugAdminPricingTable_features">
                    <li class="daftplugAdminPricingTable_feature" style="font-weight: 900;" data-tooltip="<?php esc_html_e('Publish your app on both platforms.', $this->textDomain); ?>" data-tooltip-flow="bottom">Google Play & App Store</li>
                    <li class="daftplugAdminPricingTable_feature" data-tooltip="<?php esc_html_e('No monthly or recurring payments.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('One-Time Payment', $this->textDomain); ?></li>
                    <li class="daftplugAdminPricingTable_feature" data-tooltip="<?php esc_html_e('All changes are automatically reflected.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('No Updates Needed', $this->textDomain); ?></li>
                </ul>
                <span class="daftplugAdminButton" data-open-popup="getAndroiosAppModal"><?php esc_html_e('Get Android & iOS Apps', $this->textDomain); ?></span>
            </div>
            <div class="daftplugAdminPricingTable_item -ios">
                <h3 class="daftplugAdminPricingTable_title">iOS</h3>
                <img class="daftplugAdminPricingTable_icon" src="<?php echo plugins_url('admin/assets/img/icon-ios.png', $this->pluginFile); ?>"/>
                <div class="daftplugAdminPricingTable_price">
                    <sup class="daftplugAdminPricingTable_currency">$</sup>
                    <span class="daftplugAdminPricingTable_amount">29</span>
                </div>
                <ul class="daftplugAdminPricingTable_features">
                <li class="daftplugAdminPricingTable_feature" style="font-weight: 900;" data-tooltip="<?php esc_html_e('Publish your app on this platform.', $this->textDomain); ?>" data-tooltip-flow="bottom">App Store</li>
                    <li class="daftplugAdminPricingTable_feature" data-tooltip="<?php esc_html_e('No monthly or recurring payments.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('One-Time Payment', $this->textDomain); ?></li>
                    <li class="daftplugAdminPricingTable_feature" data-tooltip="<?php esc_html_e('All changes are automatically reflected.', $this->textDomain); ?>" data-tooltip-flow="bottom"><?php esc_html_e('No Updates Needed', $this->textDomain); ?></li>
                </ul>
                <span class="daftplugAdminButton" data-open-popup="getIosAppModal"><?php esc_html_e('Get iOS App', $this->textDomain); ?></span>
            </div>
        </div>
    </div>
	<?php $this->renderNotice(); ?>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <fieldset class="daftplugAdminPluginFeatures -flexAuto">
	            <h4 class="daftplugAdminPluginFeatures_title"><?php esc_html_e('Plugin Features', $this->textDomain); ?></h4>
	            <div class="daftplugAdminField">
	                <label for="pwa" class="daftplugAdminField_label -flex9"><?php esc_html_e('Progressive Web Apps (PWA)', $this->textDomain); ?></label>
	                <label class="daftplugAdminInputCheckbox -flexAuto -featuresCheckbox" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>">
	                    <input type="checkbox" name="pwa" id="pwa" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('pwa'), 'on'); ?>>
	                </label>
	            </div>
	            <div class="daftplugAdminField">
	                <label for="amp" class="daftplugAdminField_label -flex9"><?php esc_html_e('Google Accelerated Mobile Pages (AMP)', $this->textDomain); ?></label>
	                <label class="daftplugAdminInputCheckbox -flexAuto -featuresCheckbox" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>">
	                    <input type="checkbox" name="amp" id="amp" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('amp'), 'on'); ?>>
	                </label>
	            </div>
	            <div class="daftplugAdminField">
	                <label for="fbia" class="daftplugAdminField_label -flex9"><?php esc_html_e('Facebook Instant Articles (FBIA)', $this->textDomain); ?></label>
	                <label class="daftplugAdminInputCheckbox -flexAuto -featuresCheckbox" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>">
	                    <input type="checkbox" name="fbia" id="fbia" class="daftplugAdminInputCheckbox_field" <?php checked(daftplugInstantify::getSetting('fbia'), 'on'); ?>>
	                </label>
	            </div>
	        </fieldset>   
        </div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminStatus -flexAuto">
                <h4 class="daftplugAdminStatus_title"><?php esc_html_e('Progressive Web Apps', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Validate PWA', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flexAuto"><?php if(daftplugInstantify::getSetting('pwa')=='off'){echo'None';}else{printf('<a class="daftplugAdminLink" href="%s" target="_blank">%s</a>',esc_url(add_query_arg(array('psiurl'=>trailingslashit(strtok(home_url('/', 'https'), '?')),'strategy'=>'mobile','category'=>'pwa'),'https://googlechrome.github.io/lighthouse/viewer/')),esc_html__('View PWA Validation', $this->textDomain));} ?></div>
                </div>
                <?php foreach ($this->getPwaStatus() as $status) { ?>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php echo $status['title'] ?></div>
                    <?php if ($status['condition']) {
                        echo '<div class="daftplugAdminStatus_text -flexAuto"><svg class="daftplugAdminStatus_icon -iconCheck"><use href="#iconCheck"></use></svg> '.$status['true'].'</div>';
                    } else {
                        echo '<div class="daftplugAdminStatus_text -flexAuto"><svg class="daftplugAdminStatus_icon -iconX"><use href="#iconX"></use></svg> '.$status['false'].'</div>';
                    } ?>
                </div>
                <?php } ?>
            </div>
    	</div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminAmpInfo -flexAuto <?php if (daftplugInstantify::getSetting('amp') == 'off' || (daftplugInstantify::getSetting('amp') == 'on' && daftplugInstantify::isAmpPluginActive())) { echo '-disabled'; } ?>">
                <h4 class="daftplugAdminFbiaInfo_title"><?php esc_html_e('Google AMP', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('AMP URL', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><a class="daftplugAdminLink" href="<?php if(daftplugInstantify::getSetting('amp')=='off'){echo'#';}else{echo trailingslashit(strtok(home_url('/', 'https'), '?')).'?amp';} ?>" target="_blank"><?php if(daftplugInstantify::getSetting('amp')=='off'){echo'None';}else{esc_html_e('View AMP URL', $this->textDomain);} ?></a></div>                
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Validated URLs', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><?php if(daftplugInstantify::getSetting('amp')=='off'){echo'None';}else{printf('<a class="daftplugAdminLink" href="%s">%s</a>',esc_url(add_query_arg('post_type',AMP_Validated_URL_Post_Type::POST_TYPE_SLUG,admin_url('edit.php'))),esc_html__('View Validated URLs', $this->textDomain));} ?></div>                
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Error Index', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><?php if(daftplugInstantify::getSetting('amp')=='off'){echo'None';}else{printf('<a class="daftplugAdminLink" href="%s">%s</a>',esc_url(get_admin_url(null,'edit-tags.php?taxonomy='.AMP_Validation_Error_Taxonomy::TAXONOMY_SLUG.'&post_type=amp_validated_url')),esc_html__('View Error Index',$this->textDomain));} ?></div>               
                </div>
            </div>    
        </div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminFbiaInfo -flexAuto <?php if (daftplugInstantify::getSetting('fbia') == 'off') { echo '-disabled'; } ?>">
                <h4 class="daftplugAdminFbiaInfo_title"><?php esc_html_e('Facebook Instant Articles', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('RSS Feed URL', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><a class="daftplugAdminLink" href="<?php if(daftplugInstantify::getSetting('fbia')=='off'){echo'#';}else{echo$this->daftplugInstantifyFbia->feedUrl;} ?>" target="_blank"><?php if(daftplugInstantify::getSetting('fbia')=='off'){echo'None';}else{esc_html_e('View RSS Feed URL', $this->textDomain);} ?></a></div>                
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Validate Feed', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><?php if(daftplugInstantify::getSetting('fbia')=='off'){echo'None';}else{printf('<a class="daftplugAdminLink" href="%s" target="_blank">%s</a>',esc_url(add_query_arg('url',$this->daftplugInstantifyFbia->feedUrl,'https://podba.se/validate/')),esc_html__('View Feed Validation', $this->textDomain));} ?></div>               
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Article Count', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><?php if(daftplugInstantify::getSetting('fbia') == 'off'){echo'None';}else{echo$this->daftplugInstantifyFbia->getArticleCount();} ?></div>                
                </div>
            </div>    
        </div>
    </div>
</article>