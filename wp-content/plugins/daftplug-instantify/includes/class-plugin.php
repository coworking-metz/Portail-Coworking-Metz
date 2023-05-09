<?php

if (!defined('ABSPATH')) exit;

use Minishlink\WebPush\VAPID;

if (!class_exists('daftplugInstantify')) {
    class daftplugInstantify {
        public $name;
        public $description;
        public static $slug;
        public $version;
        public $textDomain;
        public $optionName;
        public static $pluginOptionName;

        public static $pluginFile;
        public $pluginBasename;
        public $pluginUploadDir;

        public static $verifyUrl;
        public static $itemId;

        public static $website;

        public $purchaseCode;

        public $capability;

        public static $settings;

        public $defaultSettings;

        public $daftplugInstantifyPwa;
        public $daftplugInstantifyAmp;
        public $daftplugInstantifyFbia;
        public $daftplugInstantifyPublic;
        public $daftplugInstantifyAdmin;

        public function __construct($config) {
            $this->name = $config['name'];
            $this->description = $config['description'];
            self::$slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];
            self::$pluginOptionName = $config['option_name'];

            self::$pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];
            $this->pluginUploadDir = $config['plugin_upload_dir'];

            self::$verifyUrl = $config['verify_url'];
            self::$itemId = $config['item_id'];

            self::$website = parse_url(site_url(), PHP_URL_HOST);

            $this->purchaseCode = get_option("{$this->optionName}_purchase_code");

            $this->capability = 'manage_options';

            self::$settings = $config['settings'];

            $this->defaultSettings = array(
                'uninstallSettings' => 'delete',
                'pwa' => 'on',
                'pwaPlatforms' => array('desktop', 'mobile', 'tablet'),
                'pwaOnAll' => 'on',
                'pwaOnPostTypes' => array('post'),
                'pwaOnPageTypes' => array('is_home'),
                'pwaDynamicManifest' => 'off',
                'pwaName' => get_bloginfo('name'),
                'pwaShortName' => get_bloginfo('name'),
                'pwaStartPage' => trailingslashit(strtok(home_url('/', 'https'), '?')),
                'pwaDescription' => get_bloginfo('description'),
                'pwaIcon' => get_option('site_icon'),
                'pwaDisplayMode' => 'standalone',
                'pwaOrientation' => 'any',
                'pwaIosStatusBarStyle' => 'default',
                'pwaThemeColor' => '#FFFFFF',
                'pwaBackgroundColor' => '#FFFFFF',
                'pwaCategories' => array('shopping'),
                'pwaIarcRatingId' => '',
                'pwaRelatedApplication1' => 'off',
                'pwaRelatedApplication1Platform' => '',
                'pwaRelatedApplication1Url' => '',
                'pwaRelatedApplication1Id' => '',
                'pwaRelatedApplication2' => 'off',
                'pwaRelatedApplication2Platform' => '',
                'pwaRelatedApplication2Url' => '',
                'pwaRelatedApplication2Id' => '',
                'pwaRelatedApplication3' => 'off',
                'pwaRelatedApplication3Platform' => '',
                'pwaRelatedApplication3Url' => '',
                'pwaRelatedApplication3Id' => '',
                'pwaAppShortcut1' => 'off',
                'pwaAppShortcut1Name' => '',
                'pwaAppShortcut1Url' => '',
                'pwaAppShortcut1Icon' => '',
                'pwaAppShortcut2' => 'off',
                'pwaAppShortcut2Name' => '',
                'pwaAppShortcut2Url' => '',
                'pwaAppShortcut2Icon' => '',
                'pwaAppShortcut3' => 'off',
                'pwaAppShortcut3Name' => '',
                'pwaAppShortcut3Url' => '',
                'pwaAppShortcut3Icon' => '',
                'pwaAppShortcut4' => 'off',
                'pwaAppShortcut4Name' => '',
                'pwaAppShortcut4Url' => '',
                'pwaAppShortcut4Icon' => '',
                'pwaOverlays' => 'on',
                'pwaOverlaysBrowsers' => array('chrome', 'firefox', 'opera', 'safari', 'edge'),
                'pwaOverlaysTypeFullscreen' => 'off',
                'pwaOverlaysTypeHeader' => 'on',
                'pwaOverlaysTypeHeaderMessage' => 'Get our web app. It won\'t take up space on your phone.',
                'pwaOverlaysTypeHeaderBackgroundColor' => '#0A10FF',
                'pwaOverlaysTypeHeaderTextColor' => '#FFFFFF',
                'pwaOverlaysTypeSnackbar' => 'off',
                'pwaOverlaysTypeSnackbarMessage' => 'Installing uses almost no storage and provides a quick way to return to this app.',
                'pwaOverlaysTypeSnackbarBackgroundColor' => '#0A10FF',
                'pwaOverlaysTypeSnackbarTextColor' => '#FFFFFF',
                'pwaOverlaysTypeIos' => 'on',
                'pwaOverlaysTypePost' => 'on',
                'pwaOverlaysTypeMenu' => 'off',
                'pwaOverlaysTypeMenuMessage' => 'Find what you need faster with our web app!',
                'pwaOverlaysTypeMenuBackgroundColor' => '#0A10FF',
                'pwaOverlaysTypeMenuTextColor' => '#FFFFFF',
                'pwaOverlaysTypeFeed' => 'off',
                'pwaOverlaysTypeFeedMessage' => 'Keep reading, even when you\'re on the train!',
                'pwaOverlaysTypeFeedBackgroundColor' => '#0A10FF',
                'pwaOverlaysTypeFeedTextColor' => '#FFFFFF',
                'pwaOverlaysTypeCheckout' => 'off',
                'pwaOverlaysTypeCheckoutMessage' => 'Keep track of your orders. Our web app is fast, small and works offline.',
                'pwaOverlaysTypeCheckoutBackgroundColor' => '#0A10FF',
                'pwaOverlaysTypeCheckoutTextColor' => '#FFFFFF',
                'pwaOverlaysTypeCoupon' => 'off',
                'pwaOverlaysTypeCouponMessage' => 'Install our web app on your phone and get an exclusive discount coupon for all products. It won\'t take up space on your phone.',
                'pwaOverlaysTypeCouponPercentage' => '5',
                'pwaOverlaysSkip' => 'off',
                'pwaOverlaysDelay' => '3',
                'pwaOverlaysShowAgain' => '2',
                'pwaInstallButton' => 'off',
                'pwaInstallButtonShortcode' => '[pwa-install-button]',
                'pwaInstallButtonBrowsers' => array('chrome', 'firefox', 'opera', 'safari', 'edge'),
                'pwaInstallButtonText' => 'Install App',
                'pwaInstallButtonBackgroundColor' => '#0A10FF',
                'pwaInstallButtonTextColor' => '#FFFFFF',
                'pwaOfflineFallbackPage' => '',
                'pwaOfflineNotification' => 'on',
                'pwaOfflineForms' => 'off',
                'pwaOfflineGoogleAnalytics' => 'off',
                'pwaOfflineHtmlStrategy' => 'NetworkFirst',
                'pwaOfflineJavascriptStrategy' => 'StaleWhileRevalidate',
                'pwaOfflineStylesheetsStrategy' => 'StaleWhileRevalidate',
                'pwaOfflineFontsStrategy' => 'StaleWhileRevalidate',
                'pwaOfflineImagesStrategy' => 'StaleWhileRevalidate',
                'pwaOfflineVideosStrategy' => 'CacheFirst',
                'pwaOfflineAudiosStrategy' => 'CacheFirst',
                'pwaOfflineCacheExpiration' => '10',
                'pwaNavigationTabBar' => 'off',
                'pwaNavigationTabBarBgColor' => '#FFFFFF',
                'pwaNavigationTabBarIconColor' => '#B3B9CA',
                'pwaNavigationTabBarIconActiveColor' => '#FFFFFF',
                'pwaNavigationTabBarIconActiveBgColor' => '#2552FE',
                'pwaNavigationTabBarPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaNavigationTabBarItem1' => 'off',
                'pwaNavigationTabBarItem1Icon' => '',
                'pwaNavigationTabBarItem1Label' => '',
                'pwaNavigationTabBarItem1Page' => '',
                'pwaNavigationTabBarItem1Url' => '',
                'pwaNavigationTabBarItem1CustomUrl' => 'off',
                'pwaNavigationTabBarItem2' => 'off',
                'pwaNavigationTabBarItem2Icon' => '',
                'pwaNavigationTabBarItem2Label' => '',
                'pwaNavigationTabBarItem2Page' => '',
                'pwaNavigationTabBarItem2Url' => '',
                'pwaNavigationTabBarItem2CustomUrl' => 'off',
                'pwaNavigationTabBarItem3' => 'off',
                'pwaNavigationTabBarItem3Icon' => '',
                'pwaNavigationTabBarItem3Label' => '',
                'pwaNavigationTabBarItem3Page' => '',
                'pwaNavigationTabBarItem3Url' => '',
                'pwaNavigationTabBarItem3CustomUrl' => 'off',
                'pwaNavigationTabBarItem4' => 'off',
                'pwaNavigationTabBarItem4Icon' => '',
                'pwaNavigationTabBarItem4Label' => '',
                'pwaNavigationTabBarItem4Page' => '',
                'pwaNavigationTabBarItem4Url' => '',
                'pwaNavigationTabBarItem4CustomUrl' => 'off',
                'pwaNavigationTabBarItem5' => 'off',
                'pwaNavigationTabBarItem5Icon' => '',
                'pwaNavigationTabBarItem5Label' => '',
                'pwaNavigationTabBarItem5Page' => '',
                'pwaNavigationTabBarItem5Url' => '',
                'pwaNavigationTabBarItem5CustomUrl' => 'off',
                'pwaNavigationTabBarItem6' => 'off',
                'pwaNavigationTabBarItem6Icon' => '',
                'pwaNavigationTabBarItem6Label' => '',
                'pwaNavigationTabBarItem6Page' => '',
                'pwaNavigationTabBarItem6Url' => '',
                'pwaNavigationTabBarItem6CustomUrl' => 'off',
                'pwaNavigationTabBarItem7' => 'off',
                'pwaNavigationTabBarItem7Icon' => '',
                'pwaNavigationTabBarItem7Label' => '',
                'pwaNavigationTabBarItem7Page' => '',
                'pwaNavigationTabBarItem7Url' => '',
                'pwaNavigationTabBarItem7CustomUrl' => 'off',
                'pwaScrollProgressBar' => 'off',
                'pwaScrollProgressBarPlatforms' => array('desktop', 'mobile', 'tablet', 'pwa'),
                'pwaDarkMode' => 'off',
                'pwaDarkModeSwitchButtonPosition' => 'bottom-right',
                'pwaDarkModeOSAware' => 'off',
                'pwaDarkModeBatteryLow' => 'off',
                'pwaDarkModePlatforms' => array('desktop', 'mobile', 'tablet', 'pwa'),
                'pwaWebShareButton' => 'off',
                'pwaWebShareButtonIconColor' => '#FFFFFF',
                'pwaWebShareButtonBgColor' => '#3740FF',
                'pwaWebShareButtonPosition' => 'bottom-right',
                'pwaWebShareButtonPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaPullDownNavigation' => 'off',
                'pwaPullDownNavigationBgColor' => '#3740FF',
                'pwaPullDownNavigationPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaSwipeNavigation' => 'off',
                'pwaSwipeNavigationPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaShakeToRefresh' => 'off',
                'pwaShakeToRefreshPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaPreloader' => 'off',
                'pwaPreloaderStyle' => 'default',
                'pwaPreloaderPlatforms' => array('desktop', 'mobile', 'tablet', 'pwa'),
                'pwaInactiveBlur' => 'off',
                'pwaInactiveBlurPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaToastMessages' => 'on',
                'pwaToastMessagesPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaCouponReward' => 'off',
                'pwaCouponRewardPercentage' => '5',
                'pwaAjaxify' => 'off',
                'pwaAjaxifyForms' => 'off',
                'pwaAjaxifySelectors' => '',
                'pwaAjaxifyPlatforms' => array('desktop', 'mobile', 'tablet', 'pwa'),
                'pwaPasswordlessLogin' => 'off',
                'pwaAdaptiveLoading' => 'off',
                'pwaAdaptiveLoadingPlatforms' => array('desktop', 'mobile', 'tablet', 'pwa'),
                'pwaBackgroundSync' => 'off',
                'pwaPeriodicBackgroundSync' => 'off',
                'pwaPersistentStorage' => 'off',
                'pwaUrlProtocolHandler' => 'off',
                'pwaUrlProtocolHandlerProtocol' => '',
                'pwaUrlProtocolHandlerUrl' => '',
                'pwaWebShareTarget' => 'off',
                'pwaWebShareTargetAction' => '',
                'pwaWebShareTargetUrlQuery' => '',
                'pwaVibration' => 'off',
                'pwaVibrationPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaScreenWakeLock' => 'off',
                'pwaScreenWakeLockPlatforms' => array('mobile', 'tablet', 'pwa'),
                'pwaPushTtl' => '2419200',
                'pwaPushPrompt' => 'on',
                'pwaPushPromptMessage' => 'We would like to show you notifications for the latest news and updates.',
                'pwaPushPromptBgColor' => '#FFFFFF',
                'pwaPushPromptTextColor' => '#444F5B',
                'pwaPushPromptSkip' => 'off',
                'pwaPushButton' => 'on',
                'pwaPushButtonIconColor' => '#FFFFFF',
                'pwaPushButtonBgColor' => '#FF3838',
                'pwaPushButtonPosition' => 'bottom-left',
                'pwaPushButtonBehavior' => 'shown',
                'pwaPushNewContent' => 'off',
                'pwaPushNewContentPostTypes' => array('post'),
                'pwaPushWooNewProduct' => 'off',
                'pwaPushWooPriceDrop' => 'off',
                'pwaPushWooSalePrice' => 'off',
                'pwaPushWooBackInStock' => 'off',
                'pwaPushWooAbandonedCart' => 'off',
                'pwaPushWooAbandonedCartInterval' => '1',
                'pwaPushWooOrderStatusUpdate' => 'off',
                'pwaPushWooNewOrder' => 'off',
                'pwaPushWooNewOrderRoles' => array('administrator'),
                'pwaPushWooLowStock' => 'off',
                'pwaPushWooLowStockRole' => array('administrator'),
                'pwaPushWooLowStockThreshold' => '5',
                'pwaPushBpMemberMention' => 'off',
                'pwaPushBpMemberReply' => 'off',
                'pwaPushBpNewMessage' => 'off',
                'pwaPushBpFriendRequest' => 'off',
                'pwaPushBpFriendAccepted' => 'off',
                'pwaPushPeepsoNotifications' => 'off',
                'pwaPushUmRoleUpdate' => 'off',
                'pwaPushUmNewComment' => 'off',
                'pwaPushUmCommentReply' => 'off',
                'pwaPushUmGuestComment' => 'off',
                'pwaPushUmProfileView' => 'off',
                'pwaPushUmGuestProfileView' => 'off',
                'pwaPushUmPrivateMessage' => 'off',
                'pwaPushUmNewFollow' => 'off',
                'pwaPushUmFriendRequest' => 'off',
                'pwaPushUmFriendRequestAccepted' => 'off',
                'pwaPushUmWallPost' => 'off',
                'pwaPushUmWallComment' => 'off',
                'pwaPushUmPostLike' => 'off',
                'pwaPushUmNewMention' => 'off',
                'pwaPushUmGroupApprove' => 'off',
                'pwaPushUmGroupJoinRequest' => 'off',
                'pwaPushUmGroupInvitation' => 'off',
                'pwaPushUmGroupRoleChange' => 'off',
                'pwaPushUmGroupPost' => 'off',
                'pwaPushUmGroupComment' => 'off',
                'amp' => 'off',
                'ampPlatforms' => array('desktop', 'mobile', 'tablet'),
                'ampUrlStructure' => 'queryParameter',
                'ampOnAll' => 'on',
                'ampOnPostTypes' => array('post'),
                'ampOnPageTypes' => array('is_singular'),
                'ampPluginSuppression' => 'off',
                'ampPluginSuppressionList' => array(),
                'ampSidebarMenu' => 'off',
                'ampSidebarMenuId' => '',
                'ampSidebarMenuBgColor' => '#2E3334',
                'ampSidebarMenuPosition' => 'right',
                'ampCustomCss' => '',
                'ampAdSenseAutoAds' => 'off',
                'ampAdSenseAutoAdsClient' => '',
                'ampAdAboveContentSize' => 'responsive',
                'ampAdAboveContentClient' => '',
                'ampAdAboveContentSlot' => '',
                'ampAdInsideContentSize' => 'responsive',
                'ampAdInsideContentClient' => '',
                'ampAdInsideContentSlot' => '',
                'ampAdBelowContentSize' => 'responsive',
                'ampAdBelowContentClient' => '',
                'ampAdBelowContentSlot' => '',
                'ampAdAboveContent' => 'off',
                'ampAdInsideContent' => 'off',
                'ampAdBelowContent' => 'off',
                'ampGoogleAnalytics' => 'off',
                'ampGoogleAnalyticsTrackingId' => '',
                'ampGoogleAnalyticsAmpLinker' => 'off',
                'ampFacebookPixelId' => '',
                'ampSegmentAnalyticsWriteKey' => '',
                'ampStatCounterUrl' => '',
                'ampHistatsAnalyticsId' => '',
                'ampYandexMetrikaCounterId' => '',
                'ampChartbeatAnalyticsAccountId' => '',
                'ampClickyAnalyticsSiteId' => '',
                'ampFacebookPixel' => 'off',
                'ampSegmentAnalytics' => 'off',
                'ampStatCounter' => 'off',
                'ampHistatsAnalytics' => 'off',
                'ampYandexMetrika' => 'off',
                'ampChartbeatAnalytics' => 'off',
                'ampClickyAnalytics' => 'off',
                'ampAlexaMetrics' => 'off',
                'ampAlexaMetricsAccount' => '',
                'ampAlexaMetricsDomain' => '',
                'ampCookieNotice' => 'off',
                'ampCookieNoticeMessage' => 'This website uses cookies to ensure you get the best experience on our website.',
                'ampCookieNoticeButtonText' => 'Got it!',
                'ampCookieNoticePosition' => 'top',
                'fbia' => 'off',
                'fbiaPageId' => '',
                'fbiaOnPostTypes' => array('post'),
                'fbiaArticleStyle' => 'default',
                'fbiaCopyright' => '',
                'fbiaRtlPublishing' => 'off',
                'fbiaArticleQuantity' => '10',
                'fbiaUpdateFrequency' => '1',
                'fbiaArticleInteraction' => 'off',
                'fbiaRelatedArticles' => 'off',
                'fbiaCustomRules' => '',
                'fbiaAudienceNetwork' => 'off',
                'fbiaAudienceNetworkPlacementId' => '',
                'fbiaCustomAdEmbed' => 'off',
                'fbiaCustomAdEmbedCode' => '',
                'fbiaAnalytics' => 'off',
                'fbiaAnalyticsCode' => '',
            );

            if ($this->purchaseCode && !isset($_GET['noinstantify'])) {
                if (daftplugInstantify::getSetting('pwa') == 'on') {
                   require_once(plugin_dir_path(dirname(__FILE__)) . 'pwa/includes/class-plugin.php');
                   $this->daftplugInstantifyPwa = new daftplugInstantifyPwa($config);
                }

                if (daftplugInstantify::getSetting('amp') == 'on') {
                   require_once(plugin_dir_path(dirname(__FILE__)) . 'amp/includes/class-plugin.php');
                   $this->daftplugInstantifyAmp = new daftplugInstantifyAmp($config);
                }

                if (daftplugInstantify::getSetting('fbia') == 'on') {
                   require_once(plugin_dir_path(dirname(__FILE__)) . 'fbia/includes/class-plugin.php');
                   $this->daftplugInstantifyFbia = new daftplugInstantifyFbia($config);
                }

                if (!wp_next_scheduled("{$this->optionName}_verify_license_schedule")) {
                    wp_schedule_event(time(), 'weekly', "{$this->optionName}_verify_license_schedule");
                }
            }

            if ($this->isPublic()) {
                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-public.php');
                $this->daftplugInstantifyPublic = new daftplugInstantifyPublic($config, $this->daftplugInstantifyPwa, $this->daftplugInstantifyAmp, $this->daftplugInstantifyFbia);
            }

            if ($this->isAdmin()) {
                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php');
                $this->daftplugInstantifyAdmin = new daftplugInstantifyAdmin($config, $this->defaultSettings, $this->daftplugInstantifyPwa, $this->daftplugInstantifyAmp, $this->daftplugInstantifyFbia);
            }

            if (get_transient("{$this->optionName}_updated")) {
                update_option("{$this->optionName}_settings", wp_parse_args(self::$settings, $this->defaultSettings));
                if (function_exists('plwp_create_tables')) {
                    add_action('plugins_loaded', 'plwp_create_tables');
                }
                delete_transient("{$this->optionName}_updated");
            }

            add_action('plugins_loaded', array($this, 'loadTextDomain'));
            add_filter("plugin_action_links_{$this->pluginBasename}", array($this, 'addPluginActionLinks'));
            register_activation_hook(self::$pluginFile, array($this, 'onActivate'));
            add_action('upgrader_process_complete', array($this, 'onUpdate'), 10, 2);
            register_deactivation_hook(self::$pluginFile, array($this, 'onDeactivate'));
            add_action("{$this->optionName}_verify_license_schedule", array($this, 'verifyLicenseSchedule'));
            add_filter('wp_mail', array($this, 'mailAttachments'));
        }

        public function loadTextDomain() {
            load_plugin_textdomain($this->textDomain, false, dirname($this->pluginBasename) . '/languages/');
        }

        public function addPluginActionLinks($links) {
            $slug = self::$slug;
            $links[] = '<a href="'.esc_url(admin_url("admin.php?page={$slug}")).'">Settings</a>';
            $links[] = '<a href="http://codecanyon.net/user/daftplug/portfolio?ref=DaftPlug" target="_blank">More plugins by DaftPlug</a>';
        
            return $links;
        }

        public function onActivate() {
            $errors = array(
                'curl' => array(
                    'message' => __('⚠️ Instantify features require <i>curl</i> extension to function properly.', $this->textDomain),
                    'condition' => !extension_loaded('curl'),
                ),
                'dom' => array(
                    'message' => __('⚠️ Instantify features require <i>dom</i> extension to function properly.', $this->textDomain),
                    'condition' => !extension_loaded('dom'),
                ),
                'iconv' => array(
                    'message' => __('⚠️ Instantify features require <i>iconv</i> extension to function properly.', $this->textDomain),
                    'condition' => !extension_loaded('iconv'),
                ),
                'libxml' => array(
                    'message' => __('⚠️ Instantify features require <i>libxml</i> extension to function properly.', $this->textDomain),
                    'condition' => !extension_loaded('libxml'),
                ),
                'spl' => array(
                    'message' => __('⚠️ Instantify features require <i>spl</i> extension to function properly.', $this->textDomain),
                    'condition' => !extension_loaded('spl'),
                ),
            );

            foreach ($errors as $error) {
                if ($error['condition']) {
                    die('<p style="font-family:cursive;font-size:15px;font-weight:600;color:#4073ff;margin-top:16px;">'.$error['message'].' '. __('If you have trouble fixing this, please contact us on', $this->textDomain).' <i>support@daftplug.com</i></p>');
                }
            }

            add_option("{$this->optionName}_settings", $this->defaultSettings);
            add_option("{$this->optionName}_installed_devices", array());
            add_option("{$this->optionName}_subscribed_devices", array());
            if (function_exists('plwp_create_tables')) {
                plwp_create_tables();
            }

            if (!version_compare(PHP_VERSION, '7.3', '<') && extension_loaded('mbstring') && extension_loaded('openssl')) {
                require_once(plugin_dir_path(self::$pluginFile) . implode(DIRECTORY_SEPARATOR, array('pwa', 'includes', 'libs', 'web-push-php', 'autoload.php')));
                $vapidKeys = VAPID::createVapidKeys();
                add_option("{$this->optionName}_vapid_keys", array('pwaPublicKey' => $vapidKeys['publicKey'], 'pwaPrivateKey' => $vapidKeys['privateKey']));
            }

            if (!get_transient("{$this->optionName}_installer_analytics")) {
                set_transient("{$this->optionName}_installer_analytics", array(
                    date('j M Y') => 0,
                ));
            }

            if (!get_transient("{$this->optionName}_subscriber_analytics")) {
                set_transient("{$this->optionName}_subscriber_analytics", array(
                    date('j M Y') => 0,
                ));
            }

            if (!is_dir($this->pluginUploadDir)) {
                wp_mkdir_p($this->pluginUploadDir);
            }
        }

        public function onUpdate($upgraderObject, $options) {
            if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
                foreach ($options['plugins'] as $plugin) {
                    if ($plugin == $this->pluginBasename) {
                        set_transient("{$this->optionName}_updated", 'yes', 3600);
                    }
                }
            }
        }

        public function onDeactivate() {
            do_action("{$this->optionName}_on_deactivate");
        }

        public function verifyLicenseSchedule() {
            $verify = $this->handleLicense($this->purchaseCode, 'verify');
            if ($verify->code !== '200' && $verify->code !== '429') {
                delete_option("{$this->optionName}_purchase_code");
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                deactivate_plugins($this->pluginBasename);
            }
        }

        public static function handleLicense($purchaseCode, $action) {
            $params = array(
                'sslverify' => false,
                'body' => array(
                    'action' => $action,
                    'slug' => urlencode(self::$slug),
                    'item_id' => urlencode(self::$itemId),
                    'purchase_code' => urlencode($purchaseCode),
                    'website' => self::$website
                ),
                'user-agent' => 'WordPress/'.get_bloginfo('version').'; '.get_bloginfo('url')
            );
        
            $response = wp_remote_get(self::$verifyUrl, $params);
        
            if (!is_wp_error($response)) {
                $result = json_decode(wp_remote_retrieve_body($response));
            } else {
				$result = $response->get_error_message();
			}

            return $result;
        }

        public static function getSetting($key) {
            if (array_key_exists($key, self::$settings)) {
                return self::$settings[$key];
            } else {
                return false;
            }
        }

        public static function setSetting($key, $value) {
            $optionName = self::$pluginOptionName;
            self::$settings[$key] = $value;
            update_option("{$optionName}_settings", self::$settings);
        }

        public static function isAdmin() {
            if (function_exists('is_admin') && is_admin()) {
                return true;
            } else {
                $currentUrl = set_url_scheme(
                    sprintf(
                        'http://%s%s',
                        $_SERVER['HTTP_HOST'],
                        $_SERVER['REQUEST_URI']
                    )
                );
                $adminUrl = strtolower(admin_url());

                if (strpos($currentUrl, $adminUrl) !== false) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        public static function isPublic() {
            if (function_exists('is_admin') && function_exists('wp_doing_ajax') && !is_admin() && wp_doing_ajax()) {
                return true;
            } else {
                $currentUrl = set_url_scheme(
                    sprintf(
                        'http://%s%s',
                        $_SERVER['HTTP_HOST'],
                        $_SERVER['REQUEST_URI']
                    )
                );
                $adminUrl = strtolower(admin_url());

                if (strpos($currentUrl, $adminUrl) !== false) {
                    if (strpos($currentUrl, 'admin-ajax.php') !== false) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            }
        }

        public static function isWooCommerceActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            return is_plugin_active('woocommerce/woocommerce.php');
        }

        public static function isBuddyPressActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            return is_plugin_active('buddypress/bp-loader.php');
        }

        public static function isPeepsoActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            return is_plugin_active('peepso-core/peepso.php');
        }

        public static function isUltimateMemberActive($extension = '') {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            if (!empty($extension)) {
                return is_plugin_active("{$extension}/{$extension}.php");
            } else {
                return is_plugin_active('ultimate-member/ultimate-member.php');
            }
        }

        public static function isOnesignalActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            return is_plugin_active('onesignal-free-web-push-notifications/onesignal.php');
        }

        public static function isWebpushrActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            return is_plugin_active('webpushr-web-push-notifications/push.php');
        }

        public static function isWprocketActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            return is_plugin_active('wp-rocket/wp-rocket.php');
        }

        public static function isAmpPluginActive() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            if (is_plugin_active('amp/amp.php') || is_plugin_active('better-amp/better-amp.php') || is_plugin_active('accelerated-mobile-pages/accelerated-mobile-pages.php') || is_plugin_active('weeblramp/weeblramp.php') || is_plugin_active('amp-wp/amp-wp.php') || is_plugin_active('amp-enhancer/amp-enhancer.php')) {
            	return true;
            } else {
            	return false;
            }
        }

        public static function isAmpPage() {
            if (function_exists('amp_is_request')) {
                return amp_is_request();
            } else if (function_exists('is_amp_endpoint')) {
                return is_amp_endpoint();
            }
        }

        public static function isPwaPage() {
            if (isset($_GET['isPwa']) && $_GET['isPwa'] == 'true') {
                return true;
            } else {
                return false;
            }
        }

        public static function isPlatform($platform) {
            (!class_exists('Mobile_Detect')) ? require_once(plugin_dir_path(self::$pluginFile) . implode(DIRECTORY_SEPARATOR, array('includes', 'libs', 'Mobile_Detect.php'))) : '';
            $detect = new Mobile_Detect;
            switch ($platform) {
                case 'mobile':
                    $detected = $detect->isMobile() && !$detect->isTablet();
                    break;
                case 'tablet':
                    $detected = $detect->isTablet();
                    break;
                case 'desktop':
                    $detected = !$detect->isMobile() && !$detect->isTablet();
                    break;
                case 'chrome':
                    $detected = $detect->isChrome() && !$detect->isOpera();
                    break;
                case 'safari':
                    $detected = $detect->isSafari();
                    break;
                case 'firefox':
                    $detected = $detect->isFirefox();
                    break;
                case 'opera':
                    $detected = $detect->isOpera() && $detect->isChrome();
                    break;
                case 'edge':
                    $detected = $detect->isEdge() && $detect->isChrome();
                    break;
                default:
                    $detected = $detect->is($platform);
            }

            return $detected;
        }
        
        public static function getCurrentUrl($clean = false) {
            $http = 'http';
            if (isset($_SERVER['HTTPS'])) {
                $http = 'https';
            }
            $host = $_SERVER['HTTP_HOST'];
            $requestUri = $_SERVER['REQUEST_URI'];

            if ($clean == true) {
                return trim(strtok($http.'://'.htmlentities($host).htmlentities($requestUri), '?'));
            } else {
                return $http.'://'.htmlentities($host).htmlentities($requestUri);
            }
        }

        public function mailAttachments($atts) {
            $attachment_arrays = array();
            if (array_key_exists('attachments', $atts) && isset($atts['attachments']) && $atts['attachments'] ) {
                $attachments = $atts['attachments'];
                if (is_array($attachments) && ! empty($attachments)) {
                    $is_multidimensional_array = count($attachments) == count($attachments, COUNT_RECURSIVE) ? false : true;
                    if (!$is_multidimensional_array) $attachments = array($attachments);
                    foreach ($attachments as $index => $attachment) {
                        if (is_array($attachment) && (array_key_exists('path', $attachment) || array_key_exists('string', $attachment))) {
                            $attachment_arrays[] = $attachment;
                            if ($is_multidimensional_array) {
                                unset($atts['attachments'][$index]);
                            } else {
                                $atts['attachments'] = array();
                            }
                        }
                    }
                }
                global $phpmailer;
                if (!($phpmailer instanceof PHPMailer)) {
                    require_once(ABSPATH.WPINC.'/PHPMailer/PHPMailer.php');
                    require_once(ABSPATH.WPINC.'/PHPMailer/SMTP.php');
                    require_once(ABSPATH.WPINC.'/PHPMailer/Exception.php');
                    $phpmailer = new PHPMailer\PHPMailer\PHPMailer(true);
                }
                $phpmailer->___wp_attachments = $attachment_arrays;
                add_action('phpmailer_init', function(\PHPMailer\PHPMailer\PHPMailer $phpmailer) {
                    $attachment_arrays = property_exists($phpmailer, '___wp_attachments') ? (is_array($phpmailer->___wp_attachments) ? $phpmailer->___wp_attachments : array()) : array();
                    $phpmailer->___wp_attachments = array();
                    foreach ($attachment_arrays as $attachment) {
                        $is_filesystem_attachment = array_key_exists('path', $attachment) ? true : false;
                        try {
                            $encoding = $attachment['encoding'] ?? $phpmailer::ENCODING_BASE64;
                            $type = $attachment['type'] ?? '';
                            $disposition = $attachment['disposition'] ?? 'attachment';
                            if ($is_filesystem_attachment) {
                                $phpmailer->addAttachment(($attachment['path'] ?? null), ($attachment['name'] ?? ''), $encoding, $type, $disposition);
                            } else {
                                $phpmailer->addStringAttachment(($attachment['string'] ?? null), ($attachment['filename'] ?? ''), $encoding, $type, $disposition);
                            }
                        } catch (\PHPMailer\PHPMailer\Exception $e) {
                            continue;
                        }
                    } 
                });
            }
            return $atts;
        }
    }
}