<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -fbia" data-page="fbia" data-title="<?php esc_html_e('FBIA', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <img class="daftplugAdminPage_illustration" src="<?php echo plugins_url('admin/assets/img/illustration-fbia.png', $this->pluginFile)?>"/>
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Facebook Instant Articles', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('Instant Articles is a tool designed for media publishers to distribute instant, interactive articles to their readers within the Facebook mobile app.', $this->textDomain); ?></h5>
    </div>
    <?php 
    if (daftplugInstantify::getSetting('fbia') == 'off') {
    	?>
        <div class="daftplugAdminPage_content -flex8">
            <fieldset class="daftplugAdminFieldset">
                <h4 class="daftplugAdminFieldset_title"><?php _e('FBIA Disabled', $this->textDomain); ?></h4>
                <p class="daftplugAdminFieldset_description"><?php _e('Facebook Instant Articles features are disabled. If you want to enable it just navigate to <a class="daftplugAdminLink" href="#/overview/" data-page="overview">Overview</a> section and enable it.', $this->textDomain); ?></p>
            </fieldset>
        </div>
    	<?php
    } else {
	    $this->daftplugInstantifyFbia->daftplugInstantifyFbiaAdmin->getSubpages();
    }
    ?>
</article>