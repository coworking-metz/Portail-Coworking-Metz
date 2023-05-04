<?php

if (!defined('ABSPATH')) exit;

?>
<article class="daftplugAdminPage -support" data-page="support" data-title="<?php esc_html_e('Support', $this->textDomain); ?>">
    <div class="daftplugAdminPage_heading -flex12">
        <img class="daftplugAdminPage_illustration" src="<?php echo plugins_url('admin/assets/img/illustration-support.png', $this->pluginFile)?>"/>
        <h2 class="daftplugAdminPage_title"><?php esc_html_e('Support', $this->textDomain); ?></h2>
        <h5 class="daftplugAdminPage_subheading"><?php esc_html_e('We understand all the importance of product support for our customers. That’s why we are ready to solve all your issues and answer any questions related to our plugin.', $this->textDomain); ?></h5>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminFaq -flexAuto">
                <h4 class="daftplugAdminFaq_title"><?php esc_html_e('Frequently Asked Questions', $this->textDomain); ?></h4>
                <p class="daftplugAdminFaq_description"><?php esc_html_e('Reading the FAQ is useful when you\'re experiencing a common issue related to the plugin. If the FAQ didn\'t help and you have a hard time resolving the problem, please submit a ticket.', $this->textDomain); ?></p>
                <div class="daftplugAdminFaq_list">
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('Add To Home Screen overlays are not showing, Why?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php esc_html_e('Make sure the installation overlays are enabled, you are visiting your website from a Chrome, Firefox, Opera or Safari mobile browser and you have not already dismissed overlay by tapping "Continue in browser" button.', $this->textDomain); ?></div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('How can I setup Push Notifications?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php _e('Push Notifications are automatically configured for you via VAPID method, so you don\'t need additional configurations or Firebase project creation. Alternatively, you can use the <a class="daftplugAdminLink" href="https://wordpress.org/plugins/onesignal-free-web-push-notifications/" target="_blank">OneSignal Push Notification</a> plugin as a push notification service.', $this->textDomain); ?>
                        </div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('Does Push Notifications work on iOS?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php esc_html_e('Currently iOS Safari is not supporting web based push notifications. It will be available in future Safari updates. It is technically impossible for now to send push notifications to iPhones, but however you can send push notifications to the Macbook and iMac users.', $this->textDomain); ?></div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('AMP version of the website seems broken, Why?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php esc_html_e('Instantify generates AMP pages from your active theme. But valid AMP requires all the JavaScript to be removed from the page. Instantify will try to convert every component into AMP supported format but some JS based website features may not work, like JavaScript slider or hamburger menu. Also it allows only 75KB of CSS. Instantify will automatically get all the currently used styles and will remove the unused CSS for particular pages, but if your website is too heavy, it may lose some styles on the AMP version. There is no way to automatically convert complex JavaScript functionalities into AMP supported format. Although, your AMP pages will be eligible for AMP search features in Google search results. That’s why AMP is not used for dynamic interactions and it’s mainly used for static content and it’s loading speed. For interaction and dynamic content there is the PWA. Instantify’s PWA won’t have any issue with your website and all of your current website components and features will remain with additional PWA features. Alternatively, you can use other free AMP plugins that are offering theme-independent standalone AMP ready templates as AMP pages. It won’t be generated from your theme, so this kind of AMP won’t be like your non-AMP regular website. Instantify has a built-in compatibility with other AMP plugins and you can just install and activate other AMP plugins.', $this->textDomain); ?></div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('How to setup Facebook Instant Articles?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php esc_html_e('Facebook uses the HTML and RSS feed of your website for converting them into instant articles. We simplified setup process as much as possible by automatically creating an RSS feed and including meta tags, but most of the settings are in Facebook page’s publishing tool section, so you\'ll need to setup and configure Instant Articles on Facebook.', $this->textDomain); ?>
                        	<div class="daftplugAdminFaq_image">
                        		<h6 class="daftplugAdminFaq_label">1. <?php _e('Go to <a class="daftplugAdminLink" href="https://www.facebook.com/instant_articles/signup" target="_blank">Instant Articles signup page</a> and choose the Facebook Page.', $this->textDomain); ?></h6>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-signup.png', $this->pluginFile); ?>"/>
                        	</div>
                            <div class="daftplugAdminFaq_image">
                        		<h6 class="daftplugAdminFaq_label">2. <?php esc_html_e('Scroll down a little to the Tools section on the page, expand "Connect Your Site" tab, copy your Page ID, enter it into the plugin settings Facebook Page field and save settings.', $this->textDomain); ?></h6>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-copy-page-id.png', $this->pluginFile); ?>"/>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-paste-page-id.png', $this->pluginFile); ?>"/>
                        	</div>
                            <div class="daftplugAdminFaq_image">
                        		<h6 class="daftplugAdminFaq_label">3. <?php esc_html_e('Back on Facebook enter your website URL in the field and click "Submit URL" button.', $this->textDomain); ?></h6>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-connect-site.png', $this->pluginFile); ?>">
                        	</div>
                            <div class="daftplugAdminFaq_image">
                        		<h6 class="daftplugAdminFaq_label">4. <?php esc_html_e('Copy your Instant Articles RSS Feed URL from plugin\'s Overview section, expand "Production RSS Feed" tab on the Facebook page\'s publishing tool, paste it in the field and click on the "Save" button.', $this->textDomain); ?></h6>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-rss-feed-overview.png', $this->pluginFile); ?>"/>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-rss-feed-facebook.png', $this->pluginFile); ?>"/>
                        	</div>
                            <div class="daftplugAdminFaq_image">
                        		<h6 class="daftplugAdminFaq_label">5. <?php esc_html_e('Expand "Styles" tab and click "default" to open the style editor and custumize the look of your Instant Articles.', $this->textDomain); ?></h6>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-article-styles-default.png', $this->pluginFile); ?>"/>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-article-styles.png', $this->pluginFile); ?>"/>
                        	</div>
                            <div class="daftplugAdminFaq_image">
                        		<h6 class="daftplugAdminFaq_label">6. <?php esc_html_e('After custumizing your Instant Articles styles, expand "Step 2: Submit For Review" tab and click "Submit for Review" button.', $this->textDomain); ?></h6>
                        		<img class="daftplugAdminFaq_img" src="<?php echo plugins_url('admin/assets/img/image-submit-for-review.png', $this->pluginFile); ?>"/>
                                <?php esc_html_e('Please note that you will need to have at least 5 articles. And when you submit for review, Facebook team will review your content and give feedback within 3-5 days. After getting approval, you can start publishing your Instant Articles on your Facebook Page.', $this->textDomain); ?>
                        	</div>
                        </div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('How PWA, AMP and FBIA relate to each other?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php esc_html_e('Progressive Web Apps, Google AMP and Facebook Instant Articles work great together. In fact, in many cases, they complement each other in one way or another. Instantify will preload your PWA from your AMP pages, so the entry point of your website will be lightning fast and it will also warm up the PWA behind the scenes for the onward journey. FBIA will bring this kind of instant experience to your website within the Facebook mobile app.', $this->textDomain); ?></div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('Is the plugin compatible with all themes and plugins?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php esc_html_e('PWAs, AMPs and FBIAs designed by Instantify is fully compatible with all kinds of WordPress configuration, including plugins and themes. Please note that you should disable all other plugins that deliver the same functionality as Instantify in order to avoid compatibility issues.', $this->textDomain); ?></div>
                    </div>
                    <div class="daftplugAdminFaq_item">
                        <div class="daftplugAdminFaq_question"><?php esc_html_e('How can I update the plugin?', $this->textDomain); ?></div>
                        <div class="daftplugAdminFaq_answer"><?php printf(__('There are two ways to update the plugin to the newer version: Using a WordPress built-in update system which will automatically check for updates and show you a notification on an <a class="daftplugAdminLink" href="%s">admin plugins page</a> when there will be an update available or manually download latest version of plugin from <a class="daftplugAdminLink" href="https://codecanyon.net/downloads" target="_blank">Codecanyon</a> and re-install it.', $this->textDomain), admin_url('/plugins.php')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminSupportTicket -flexAuto">
                <form name="daftplugAdminSupportTicket_form" class="daftplugAdminSupportTicket_form" data-nonce="<?php echo wp_create_nonce("{$this->optionName}_support_ticket_nonce"); ?>" spellcheck="false" autocomplete="off" enctype="multipart/form-data">
                    <h4 class="daftplugAdminSupportTicket_title"><?php esc_html_e('Open a ticket at our Support Center', $this->textDomain); ?></h4>
                    <p class="daftplugAdminSupportTicket_description"><?php esc_html_e('Before submitting a ticket, please make sure that the FAQ didn\'t help, you\'re using the latest version of the plugin and there are no javascript errors on your website.', $this->textDomain); ?></p>
                    <input type="hidden" name="purchaseCode" id="purchaseCode" value="<?php echo esc_html($this->purchaseCode); ?>">
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your Envato purchase code here to verify your purchase.', $this->textDomain); ?></p>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="purchaseCodeHidden" id="purchaseCodeHidden" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Purchase Code', $this->textDomain); ?>" value="your-license-code-is-hidden" autocomplete="off" required readonly style="color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.6);">
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Enter your name and email address where we\'ll send our response.', $this->textDomain); ?></p>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="firstName" id="firstName" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Your Name', $this->textDomain); ?>" value="" autocomplete="off" required>
                        </div>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="email" name="contactEmail" id="contactEmail" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Contact Email', $this->textDomain); ?>" value="<?php echo get_option('admin_email'); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('Please be as descriptive as possible regarding the details of this ticket.', $this->textDomain); ?></p>
                        <div class="daftplugAdminInputTextarea -flexAuto">
                            <textarea name="problemDescription" id="problemDescription" class="daftplugAdminInputTextarea_field" data-placeholder="<?php esc_html_e('Problem Description', $this->textDomain); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="4" data-attachments="true" required></textarea>
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <p class="daftplugAdminField_description"><?php esc_html_e('In most cases we need a temporary access to your WordPress Dashboard to check and fix the issue.', $this->textDomain); ?></p>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="wordpressUsername" id="wordpressUsername" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('WordPress Username', $this->textDomain); ?>" value="" autocomplete="off">
                        </div>
                        <div class="daftplugAdminInputText -flexAuto">
                            <input type="text" name="wordpressPassword" id="wordpressPassword" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('WordPress Password', $this->textDomain); ?>" value="" autocomplete="off">
                        </div>
                    </div>
                    <div class="daftplugAdminField">
                        <div class="daftplugAdminField_response -flex7"></div>
                        <div class="daftplugAdminField_submit -flex5">
                            <button type="submit" class="daftplugAdminButton -submit" data-submit="<?php esc_html_e('Submit Ticket', $this->textDomain); ?>" data-waiting="<?php esc_html_e('Waiting', $this->textDomain); ?>" data-submitted="<?php esc_html_e('Ticket Submitted', $this->textDomain); ?>" data-failed="<?php esc_html_e('Submit Failed', $this->textDomain); ?>"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminSupportArticles -flexAuto">
                <h4 class="daftplugAdminChangelog_title"><?php esc_html_e('Explore PWA', $this->textDomain); ?></h4>
                <ul class="daftplugAdminSupportArticles_list" data-title="Introduction">
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/what-are-pwas/" data-text="What are Progressive Web Apps?"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/pwa-checklist/" data-text="What makes a good Progressive Web App?"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/drive-business-success/" data-text="How Progressive Web Apps can drive business success"></li>
                </ul>
                <ul class="daftplugAdminSupportArticles_list" data-title="Make It Installable">
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/install-criteria/" data-text="What does it take to be installable?"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/define-install-strategy/" data-text="How to define your install strategy"></li>
                </ul>
                <ul class="daftplugAdminSupportArticles_list" data-title="Provide An Installable Experience">
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/add-manifest/" data-text="Add a web app manifest"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/offline-fallback-page/" data-text="Create an offline fallback page"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/customize-install/" data-text="How to provide your own in-app install experience"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/promote-install/" data-text="Patterns for promoting PWA installation"></li>
                </ul>
                <ul class="daftplugAdminSupportArticles_list" data-title="Create An App-Like User Experience">
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/app-like-pwas/" data-text="Make your PWA feel more like an app"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/progressively-enhance-your-pwa/" data-text="Progressively enhance your Progressive Web App"></li>
                    <li class="daftplugAdminSupportArticles_item" data-link="https://web.dev/periodic-background-sync/" data-text="Richer offline experiences with the Periodic Background Sync API"></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="daftplugAdminPage_content -flex6">
        <div class="daftplugAdminContentWrapper">
            <div class="daftplugAdminChangelog -flexAuto">
                <h4 class="daftplugAdminChangelog_title"><?php esc_html_e('Changelog', $this->textDomain); ?></h4>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.6 - 5 June, 2022">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Settings page for controlling plugin settings"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Compatibility for Webpushr notifications plugin"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Intro guide on admin side"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Updated FontAwsesome library to latest version"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.5 - 8 May, 2022">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Intro tutorial of admin settings"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Accent color feature from theme color"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Cache Only strategy to offline caching strategies"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New scope_extensions property to Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Support for Badging API for Push Notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="iOS splash screens for latest devices"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Coupon overlay close action"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Pull down navigation feature by disabling browser pull-down refresh"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Updated WorkBox to the latest version"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Support section in the admin menu"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Navigation on the admin menu"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Replaced URL Handlers with new handle_links Manifest property"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Navigation Tab Bar height for new iOS Safari"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Related Applications not promoting native apps"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.4 - 12 March, 2022">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push Notifications feature now works without GMP extension"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Navigation tab bar detects URL parameters"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Admin assets conditional loading"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push Notifications batch size is now automatically set"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Font Awesome issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.3 - 6 February, 2022">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Translatable strings"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="AMP errors"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.2 - 25 January, 2022">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to control enabled platforms for PWA and AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP feature"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push notifications stats"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Deactivate license issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.1 - 29 November, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor bugs"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 6.0 - 27 November, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="ID property to Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="URL protocol handlers option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="PWA installation stats"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Credit card payment option in native mobile app generators"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Search functionality to the admin settings"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="PWA values can be defined on each page separately"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor issue on admin side"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="PWA analytics resetting on deactivate"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.9 - 9 November, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Push notification subscribers statistics"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Added tooltip descriptions for pricing tables"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Prealoders performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Passwordless login feature"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.8 - 5 November, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Passwordless login via Touch ID, Face ID and Windows Hello"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Automated push notifications for Ultimate Member"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Remastered native mobile app pricing table and added iOS option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Subscribers won't be deleted after plugin uninstall"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Screenshots failed generation"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="JS optimization compatibility"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.7 - 27 October, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Country segment in sending push notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Push notifications settings section"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Push notifications vibrate option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Fixed push notifications option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Support for Edge browser"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push notifications service performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="User agent detection on client side"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Ajaxify option algorithm"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Font loading performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Now support ticket form supports attachments"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.6 - 11 October, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Mobile screenshot for Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="WooCommerce order status update automated push notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Inactive Blur feature performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="ServiceWorker with updated workbox module"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Plugin activation issue with SSL"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.5 - 4 October, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Maskable icon support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="iOS special installation overlay"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="WooCommerce promo code rewards installation overlay"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Admin area options performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor bugs on admin side"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.4 - 9 September, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="URL handlers support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP features"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="License verification and update stability"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.3 - 6 August, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Scroll progress bar feature"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Inactive blur effect is now in options"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.2 - 4 August, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Blur effect when the website is not focused on mobiles and tablets"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Installation button now supports desktops"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Dark Mode not working on desktops"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Manifest and ServiceWorker outputting HTML tags sometimes"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.1 - 16 June, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to add custom FBIA transformation rules"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Manifest and ServiceWorker outputs faster now"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Pull down navigation"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="FBIA article transformer"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 5.0 - 4 June, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to disable PWA for individual pages"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to export/import plugin settings"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Manifest and ServiceWorker output stability"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Pull down navigation performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="App shortcuts icon size issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="FBIA Copyright text not appearing in articles"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="FBIA output invalid HTML chars in feed"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.9 - 28 May, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Editing previews for installation overlays"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Preview for sending push notification"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to skip first time visit for push notifications prompt"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Abandoned cart automatic push notification"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Scheduled push notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Unregistered users segment for sending push notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Adaptive loading option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Support for Periodic Background Sync API"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Video file formats in caching strategies"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Audio file formats in caching strategies"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="AMP URL Structure option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP options"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with WooCommerce"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Apple status bar updated values on iOS 14.5"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="WooCommerce cart items count missing from navigation tab bar icon"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Rare bug when ServiceWorker installation was failing"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Compatibility issue with PHP 8.0"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.8 - 17 April, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="PWA general options"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Related articles for FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Custom embed ad type for FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3-rd party plugins"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.7 - 5 April, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Ability to control features by device and platform"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="IARC Rating ID property in Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Date range selectors for PWA installation analytics"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="PWA installation analytics now counts all installs"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP and Google Fonts caching"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP functionality"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Navigation Tab Bar settings not saving sometimes"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.6 - 6 March, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Dark Mode support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="ServiceWorker cache expire time option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="PWA browsing detection"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3-rd party plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Facebook in-app browser issues"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push Notifications segmentation issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.5 - 28 February, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Snackbar installation overlay"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="In feed installation overlay"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Installation overlay options"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3rd-party plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Compatibility with WP Rocket"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.4 - 21 February, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Changelog in support section"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="User agent detection"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3rd-party plugins"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.3 - 17 February, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Instant Articles feed update frequency option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with some plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="New content and product automatic push notifications"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.2 - 4 February, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3rd-party plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Installation prompts not showing sometimes"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.1 - 3 February, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to skip first time visit for installation overlays"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to set labels separately for icons in navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Navigation tab bar with Font Awesome icons"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3rd-party plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="AMP sidebar menu not working on some themes"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Mozilla firefox fullscreen prompt pointer"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 4.0 - 26 January, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New fade preloader style"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="PWA support for Opera browser"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="AMP sidebar menu option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="AMP markup validation control and reports"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Custom CSS option for AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to suppress certain plugins on AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="PWA Chrome installation experience"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP compatibility with 3rd-party plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Overview section with more information"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Ajaxify feature with pure JavaScript"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP Mode option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Manifest and ServiceWorker names"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Link preventDefault on PWA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Unexpected deactivation issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Color inputs not working"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Firefox installation prompt issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.9 - 11 January, 2021">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Automatic notifications for Peepso"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to filter push subscribers in list"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="New content detection for auto push notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push subscriber automatic updating"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Compatibility with PHP 8"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="BuddyPress automatic notifications issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.8 - 26 December, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Changelog in support section"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="More icons to navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Navigation tab bar active icon issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Save settings button overlapping popups"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Automatic push notifications sending on draft"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.7 - 18 December, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Labels below icons option in navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="More icons to navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Ability to add custom URLs to navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Ability to add multiple related applications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Opening delay option for installation overlays"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Manifest problem on some websites"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push notification static send issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push button unsubscribe issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor issue with installation button"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="OneSignal ServiceWorker issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.6 - 21 November, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Automatic cache update"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="5 new icons to navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Skeleton loading"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Offline fallback page caching"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Plugin assets"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Native Chrome prompt not showing"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.5 - 8 November, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Background sync API support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Persistent storage API support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Web share API support with button"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Web share target API support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Screen wake lock API support"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Skeleton loading option to preloader"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New icons to navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Two new icon styles on navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Pills for indicating new and beta features"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Optimizations with Enhancements"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Preloaders performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Related application property issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.4 - 3 November, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Toast messages"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor admin UI issues"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.3 - 3 November, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Icon style option to navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="WooCommerce cart count on navigation tab bar cart icon"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Related application property to Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Tooltip on subscribe notification button"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Automated push notifications for BuddyPress"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to select additional components to Ajaxify"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Various styled preloaders"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Custom style option to FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Navigation tab bar"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor ServiceWorker preload issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="License deactivation every week issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.2 - 16 October, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Categories property in Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Related application properties in Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Manifest and ServiceWorker generation"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Page caching"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.1 - 6 October, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Styling options for push notifications prompt"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Push notifications button behavior option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New order and low stock automatic notifications for admins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Manifest and ServiceWorker generation"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Page caching and offline experience"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Compatibility with OneSignal"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Maskable icon"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="AMP all content serving"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 3.0 - 20 September, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Direct push prompt with custom push prompt"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push payload issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Errors within push subscribers list"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.9 - 6 September, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Automatic push subscription updating"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Chrome installation prompts performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor issues in AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Subscribers list not showing sometimes"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Alternative prompt display issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Dynamic manifest front page detection"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="OneSignal compatibility issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="removed" data-text="AMP generation strategy option"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.8 - 1 September, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Mobile redirection option for AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Paired browsing mode on AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Settings page not loading sometimes"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.7 - 18 August, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Alternative push subscribe prompt option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Registered users in push subscribers list"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to enable preloader only for mobile"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="JS strings translation"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Send notification function for better usage"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Unexpected errors on post editing screens"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Support ticket sending issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="WP rocket compatibility issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.6 - 22 July, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push notifications debugging"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="App shortcodes causing invalid Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="License removing issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.5 - 18 July, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New types of content to push notification messages"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Segmentation ability to push subscribe users"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Scope property in Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Screenshots property in Manifest"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="App shortcuts option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Dynamic Manifest option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Maskable icon as option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="More options on navigation tab bar with direct search"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Sending automated push notifications on certain events"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="PWA to APK generation option via Trusted Web Activity"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Preloader icon moving right glitch"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Preloader causing notice on Firefox and Safari"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Navigation tab bar covering subscribe button and toast messages"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Problem with security plugins"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Unexpected license deactivation issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.4 - 21 June, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="App like mobile tab bar menu"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Ajaxify website option"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Asset dependency loading"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Notice error on plugins screen"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="New installation overlay option missing from settings"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Buttons not fitting texts on admin panel"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Apple touch icon problem"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.3 - 10 June, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New type of installation overlay"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="License information section into Overview tab"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Maskable icon support for PWA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="AMP generation strategies"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Installation overlays performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Preloader performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Verification for more security"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Offline content issue in ServiceWorker"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push subscribers list overflow"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.2 - 24 May, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="More functionality to shortcode install button"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Options to completely disable PWA, AMP or FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Caching strategies for assets, images, fonts and other content"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Offline support option for Google Analytics"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="UTM tracking for homescreen app and push notifications"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Compatibility with subfolder installations"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Overall performance of the application"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.1 - 14 May, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Page caching for offline usage"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Preloader performance"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push notification payload sending"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Select input issue"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Subscribe button not showing sometimes"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Install button appearing on PWA version"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 2.0 - 27 April, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="New analytics in AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Support for older PHP versions"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Support for WP Rocket"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Requires plugin header information"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Code for stability"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP generation"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Offline pages causing ServiceWorker to fail"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.9 - 11 April, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Optimized code for more security"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push notification error"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.8 - 10 April, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Automatic renewal of push subscribers list"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Custom copyright to FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="RTL Publishing support to FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to control the number of articles in FBIA Feed"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Article interaction option in FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Option to exclude particular posts from AMP and FBIA"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Push notifications with VAPID method"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Application stability"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Some AMP errors"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.7 - 3 April, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="500 error on Facebook Instant Articles feed"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Push notifications Firebase credentials error"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.6 - 19 March, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Error on push subscribed users list"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Subscribe button not displaying sometimes"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.5 - 8 March, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="AMP performance"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.4 - 8 March, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Selecting pages for offline content"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Code for more stability"></li>
                    <li class="daftplugAdminChangelog_item" data-type="removed" data-text="Reactify feature as it's not useful in modern browsers anymore"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.3 - 24 February, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Shortcode support for inline installation banners"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Minor bugs"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.2 - 23 February, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="added" data-text="Inline installation banners"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Service worker and offline usage"></li>
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Stability for JavaScript"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Unexpected deactivation issue"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.1 - 15 February, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="improved" data-text="Purchase code verification process"></li>
                    <li class="daftplugAdminChangelog_item" data-type="fixed" data-text="Some minor UI issues"></li>
                </ul>
                <ul class="daftplugAdminChangelog_list" data-title="Version 1.0 - 15 February, 2020">
                    <li class="daftplugAdminChangelog_item" data-type="initial" data-text="Initial release"></li>
                </ul>
            </div>
        </div>
    </div>
</article>