<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -settings" data-page="settings" data-title="<?php esc_html_e('Settings', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <img class="daftplugAdminPage_illustration" src="<?php echo plugins_url('admin/assets/img/illustration-settings.png', $this->pluginFile)?>"/>
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Settings', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('Control what to do with your settings and data, reset/export/import settings or deactivate license to activate Instantify on another website.', $this->textDomain); ?></h5>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminLicenseInfo -flexAuto">
    		    <h4 class="daftplugAdminLicenseInfo_title"><?php esc_html_e('License Information', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('License Status', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8"><svg class="daftplugAdminStatus_icon -iconCheck" style="margin-left: 0;"><use href="#iconCheck"></use></svg> <?php esc_html_e('Active', $this->textDomain); ?></div>                
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Purchase Code', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8" style="filter: blur(2.5px);">your-license-code-is-hidden</div>                
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex4"><?php esc_html_e('Action', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex8">
						<button type="submit" class="daftplugAdminButton -submit -confirm -deactivateLicense" data-submit="<?php esc_html_e('Deactivate License', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Deactivating License', $this->textDomain); ?>" data-submitted="<?php esc_html_e('License Deactivated', $this->textDomain); ?>" data-failed="<?php esc_html_e('Deactivation Failed', $this->textDomain); ?>" data-sure="<?php esc_html_e('Are You Sure?', $this->textDomain); ?>" data-duration="2000ms" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_deactivate_license_nonce"); ?>" data-tooltip="<?php esc_html_e('Press & Hold to deactivate license', $this->textDomain); ?>" data-tooltip-flow="top"></button>
                    </div>                
                </div>
            </div>
    	</div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminExportImport -flexAuto">
    		    <h4 class="daftplugAdminExportImport_title"><?php esc_html_e('Export/Import Settings', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex7"><?php esc_html_e('Click this button to export plugin settings', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex5">
                        <button type="submit" class="daftplugAdminButton -submit -download -settingsExport" data-submit="<?php esc_html_e('Export Settings', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Exporting', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Settings Exported', $this->textDomain); ?>" data-failed="<?php esc_html_e('Exporting Failed', $this->textDomain); ?>"></button>
                    </div>                
                </div>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex7"><?php esc_html_e('Click this button to import plugin settings', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex5">
                        <form name="daftplugAdminSettingsImport_form" class="daftplugAdminSettingsImport_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_import_nonce"); ?>" spellcheck="false" autocomplete="off" enctype="multipart/form-data">
                            <input type="file" name="settingsFile" class="-hidden" id="settingsFile" required>
                            <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Import Settings', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Importing', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Settings Imported', $this->textDomain); ?>" data-failed="<?php esc_html_e('Importing Failed', $this->textDomain); ?>"></button>
                        </form>
                    </div>                
                </div>
            </div>
    	</div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminResetSettings -flexAuto">
    		    <h4 class="daftplugAdminExportImport_title"><?php esc_html_e('Reset Settings', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex7"><?php esc_html_e('Click this button to reset plugin settings', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex5">
                        <button type="submit" class="daftplugAdminButton -submit -confirm -settingsReset" data-submit="<?php esc_html_e('Reset Settings', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Resetting Settings', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Settings Reset', $this->textDomain); ?>" data-failed="<?php esc_html_e('Resetting Failed', $this->textDomain); ?>" data-sure="<?php esc_html_e('Are You Sure?', $this->textDomain); ?>" data-duration="2000ms" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_reset_nonce"); ?>" data-tooltip="<?php esc_html_e('Press & Hold to reset settings', $this->textDomain); ?>" data-tooltip-flow="top"></button>
                    </div>
                </div>
                <p class="daftplugAdminFieldset_description"><?php esc_html_e('Note: This will instantly revert all settings to their default states but will leave your data intact.', $this->textDomain); ?></p>
            </div>
    	</div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminExportImport -flexAuto">
    		    <h4 class="daftplugAdminExportImport_title"><?php esc_html_e('Uninstallation Settings', $this->textDomain); ?></h4>
                <div class="daftplugAdminStatus_container">
                    <div class="daftplugAdminStatus_label -flex7"><?php esc_html_e('Choose whether to save your settings and data or delete them upon plugin uninstallation.', $this->textDomain); ?></div>
                    <div class="daftplugAdminStatus_text -flex5">
                        <form name="daftplugAdminSettings_form" class="daftplugAdminSettings_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_settings_nonce"); ?>" spellcheck="false" autocomplete="off">
                            <div class="daftplugAdminInputSelect -flexAuto">
                                <select name="uninstallSettings" id="uninstallSettings" class="daftplugAdminInputSelect_field" data-placeholder="<?php esc_html_e('Uninstall Settings', $this->textDomain); ?>" autocomplete="off" required>
                                    <option value="save" <?php selected(daftplugInstantify::getSetting('uninstallSettings'), 'save') ?>><?php esc_html_e('Save', $this->textDomain); ?></option>
                                    <option value="delete" <?php selected(daftplugInstantify::getSetting('uninstallSettings'), 'delete') ?>><?php esc_html_e('Delete', $this->textDomain); ?></option>
                                </select>
                            </div>
                            <div class="daftplugAdminSettings_submit">
                                <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Save Settings', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Saving', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Settings Saved', $this->textDomain); ?>" data-failed="<?php esc_html_e('Saving Failed', $this->textDomain); ?>"></button>
                            </div>
                        </form>
                    </div>                
                </div>
            </div>
    	</div>
    </div>
</article>