<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -activation" data-page="activation" data-title="<?php esc_html_e('Activation', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Activation', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('Please, enter your plugin\'s CodeCanyon purchase code to activate the plugin and access in-plugin support with automatic updates.', $this->textDomain); ?></h5>
    </div>
    <div class="daftplugAdminPage_content -flex5">
    	<div class="daftplugAdminActivateLicense -flexAuto">
    		<form name="daftplugAdminActivateLicense_form" class="daftplugAdminActivateLicense_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_activate_license_nonce"); ?>" spellcheck="false" autocomplete="off">
    			<div class="daftplugAdminField">
                    <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Envato purchase code here to verify your purchase.', $this->textDomain); ?></p>
                    <div class="daftplugAdminInputText -flexAuto">
                        <input type="text" name="purchaseCode" id="purchaseCode" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Purchase Code', $this->textDomain); ?>" value="<?php echo esc_html($this->purchaseCode); ?>" autocomplete="off" required>
                    </div>
                </div>
                <div class="daftplugAdminField" style="margin-bottom: 20px;">
                    <div class="daftplugAdminField_response -flex7"></div>
                    <div class="daftplugAdminField_submit -flex5">
                        <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Activate License', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Waiting', $this->textDomain); ?>" data-submitted="<?php esc_html_e('License Activated', $this->textDomain); ?>" data-failed="<?php esc_html_e('Activation Failed', $this->textDomain); ?>"></button>
                    </div>
                </div>
    		</form>
    	</div> 	   	
    </div>
    <div class="daftplugAdminPage_content -flex8">
    	<div class="daftplugAdminFaq -flexAuto">
    		<div class="daftplugAdminFaq_list">
    			<div class="daftplugAdminFaq_item">
    				<div class="daftplugAdminFaq_question">
    					<?php esc_html_e('What is CodeCanyon purchase code?', $this->textDomain); ?>
    				</div>
    				<div class="daftplugAdminFaq_answer">
    					<?php esc_html_e('Purchase code is a license key, that you get after buying the plugin on CodeCanyon. It looks like this: 86781226-23d0-4b3c-7dfa-c1c147e0dece.', $this->textDomain); ?>
    					<div class="daftplugAdminFaq_image">
    						<img src="<?php echo plugins_url('admin/assets/img/image-purchase-code.jpg', $this->pluginFile); ?>">
    					</div>
    				</div>
    			</div>
    			<div class="daftplugAdminFaq_item">
    				<div class="daftplugAdminFaq_question">
    					<?php esc_html_e('How do I get my purchase code?', $this->textDomain); ?>
    				</div>
    				<div class="daftplugAdminFaq_answer">
    					<?php _e('After purchasing the item, go to <a class="daftplugAdminLink" href="http://codecanyon.net/downloads" target="_blank">http://codecanyon.net/downloads</a>, click "Download" and select “License Certificate & Purchase Code”. You’ll find your purchase code in the downloaded file.', $this->textDomain); ?>
    					<div class="daftplugAdminFaq_image">
                            <img src="<?php echo plugins_url('admin/assets/img/image-download-purchase-code.jpg', $this->pluginFile); ?>">
                        </div>
    				</div>
    			</div>
    			<div class="daftplugAdminFaq_item">
    				<div class="daftplugAdminFaq_question">
    					<?php esc_html_e('How do I activate a CodeCanyon license?', $this->textDomain); ?>
    				</div>
    				<div class="daftplugAdminFaq_answer">
    					<?php esc_html_e('To activate your license, in the plugin insert your purchase code into the field above and press “Activate License” button. After the successful activation, you will have full access to plugin content.', $this->textDomain); ?>
    					<div class="daftplugAdminFaq_image">
                            <img src="<?php echo plugins_url('admin/assets/img/image-activate-purchase-code.jpg', $this->pluginFile); ?>">
                        </div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
</article>