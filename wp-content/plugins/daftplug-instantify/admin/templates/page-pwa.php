<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -pwa" data-page="pwa" data-title="<?php esc_html_e('PWA', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
    	<img class="daftplugAdminPage_illustration" src="<?php echo plugins_url('admin/assets/img/illustration-pwa.png', $this->pluginFile)?>"/>
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Progressive Web Apps', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('Progressive Web Apps use modern web capabilities to deliver fast, native-app experiences with no app stores or downloads, and all the goodness of the web.', $this->textDomain); ?></h5>
    </div>
    <?php 
    if (daftplugInstantify::getSetting('pwa') == 'off') {
    	?>
        <div class="daftplugAdminPage_content -flex8">
            <fieldset class="daftplugAdminFieldset">
                <h4 class="daftplugAdminFieldset_title"><?php _e('PWA Disabled', $this->textDomain); ?></h4>
                <p class="daftplugAdminFieldset_description"><?php _e('Progressive Web Apps features are disabled. If you want to enable it just navigate to <a class="daftplugAdminLink" href="#/overview/" data-page="overview">Overview</a> section and enable it.', $this->textDomain); ?></p>
            </fieldset>
        </div>
    	<?php
    } else {
	    $this->daftplugInstantifyPwa->daftplugInstantifyPwaAdmin->getSubpages();
    }
    ?>
</article>