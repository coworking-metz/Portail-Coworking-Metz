<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -amp" data-page="amp" data-title="<?php esc_html_e('AMP', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <img class="daftplugAdminPage_illustration" src="<?php echo plugins_url('admin/assets/img/illustration-amp.png', $this->pluginFile)?>"/>
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Google AMP', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('AMP provides a straightforward way to create web pages that are fast, smooth-loading and prioritize the user-experience above all else.', $this->textDomain); ?></h5>
    </div>
    <?php 
    if (daftplugInstantify::getSetting('amp') == 'off') {
        ?>
        <div class="daftplugAdminPage_content -flex8">
            <fieldset class="daftplugAdminFieldset">
                <h4 class="daftplugAdminFieldset_title"><?php _e('AMP Disabled', $this->textDomain); ?></h4>
                <p class="daftplugAdminFieldset_description"><?php _e('Google AMP features are disabled. If you want to enable it just navigate to <a class="daftplugAdminLink" href="#/overview/" data-page="overview">Overview</a> section and enable it.', $this->textDomain); ?></p>
            </fieldset>
        </div>
        <?php
    } else {
        if (!daftplugInstantify::isAmpPluginActive()) {
            $this->daftplugInstantifyAmp->daftplugInstantifyAmpAdmin->getSubpages();
        } else {
            ?>
            <div class="daftplugAdminPage_content -flex8">
                <fieldset class="daftplugAdminFieldset">
                    <h4 class="daftplugAdminFieldset_title"><?php _e('AMP Plugin Detected', $this->textDomain); ?></h4>
                    <p class="daftplugAdminFieldset_description"><?php esc_html_e('You are using a third-party AMP plugin, so this section and AMP features are not active. Please disable all other AMP plugins and visit this section again to enable AMP pages on your website.', $this->textDomain); ?></p>
                </fieldset>
            </div>
            <?php
        }
    }
    ?>
</article>