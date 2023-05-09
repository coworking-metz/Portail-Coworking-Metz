<?php

if (!defined('ABSPATH')) exit;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

if (!class_exists('daftplugInstantifyPwaAdminPushnotifications')) {
    class daftplugInstantifyPwaAdminPushnotifications {
        public static $name;
        public static $description;
        public static $slug;
        public static $version;
        public static $textDomain;
        public static $optionName;
        public static $pluginFile;
        public static $pluginBasename;
        public static $settings;
        public static $vapidKeys;
        public static $subscribedDevices;

        public function __construct($config) {
            self::$name = $config['name'];
            self::$description = $config['description'];
            self::$slug = $config['slug'];
            self::$version = $config['version'];
            self::$textDomain = $config['text_domain'];
            self::$optionName = $config['option_name'];
            self::$pluginFile = $config['plugin_file'];
            self::$pluginBasename = $config['plugin_basename'];
            self::$settings = $config['settings'];
            self::$vapidKeys = get_option(self::$optionName."_vapid_keys", true);
            self::$subscribedDevices = get_option(self::$optionName."_subscribed_devices", true);

            add_action("wp_ajax_".self::$optionName."_send_notification", array($this, 'doModalPush'));
            add_action("wp_ajax_".self::$optionName."_handle_scheduled_notification", array($this, 'handleScheduledNotification'));
            add_action("wp_ajax_".self::$optionName."_get_subscriber_analytics", array($this, 'getSubscriberAnalytics'));
            add_action("wp_ajax_".self::$optionName."_get_subscriber_stats", array($this, 'getSubscriberStats'));
            add_action('add_meta_boxes', array($this, 'addMetaBoxes'), 10, 2);
            if (daftplugInstantify::isWooCommerceActive()) {
                add_filter('wp_insert_post_data', array($this, 'filterWooCommercePostData'), 10, 2);
                if (daftplugInstantify::getSetting('pwaPushWooOrderStatusUpdate') == 'on') {
                    add_action('woocommerce_order_status_changed', array($this, 'doWooOrderStatusUpdatePush'), 10, 4);
                }
            }
            if (daftplugInstantify::isUltimateMemberActive() && daftplugInstantify::isUltimateMemberActive('um-notifications')) {
                if (daftplugInstantify::getSetting('pwaPushUmRoleUpdate') == 'on') {
                    add_action('um_after_member_role_upgrade', array($this, 'doUmRoleUpdatePush'), 10, 3);
                }
            }
            add_action('save_post', array($this, 'doAutoPush'), 10, 2);

			foreach (self::$subscribedDevices as $key => $value) {
			    if (!array_key_exists('endpoint', self::$subscribedDevices[$key])) {
			        unset(self::$subscribedDevices[$key]);
			    }
			}

	        update_option(self::$optionName."_subscribed_devices", self::$subscribedDevices);
        }

        public function doModalPush() {
            $notificationData = json_decode(stripslashes($_POST['notificationData']), true);
            $nonce = $_POST['nonce'];
            $pushData = array(
                'title' => !empty($notificationData['pushTitle']) ? $notificationData['pushTitle'] : '',
                'body' => !empty($notificationData['pushBody']) ? $notificationData['pushBody'] : '',
                'image' => !empty($notificationData['pushImage']) ? esc_url_raw(wp_get_attachment_image_src($notificationData['pushImage'], 'full')[0] ?? '') : '',
                'icon' => !empty($notificationData['pushIcon']) ? esc_url_raw(wp_get_attachment_image_src($notificationData['pushIcon'], 'full')[0] ?? '') : '',
                'data' => array(
                    'url' => !empty($notificationData['pushUrl']) ? trailingslashit(esc_url_raw($notificationData['pushUrl'])).'?utm_source=pwa-notification' : '',
                ),
                'requireInteraction' => ($notificationData['pushFixed'] == 'on') ? true : false,
                'vibrate' => ($notificationData['pushVibrate'] == 'on') ? array(200, 100, 200) : array(),
            );

            if ($notificationData['pushActionButton1'] == 'on') {
                $pushData['actions'][] = array('action' => 'action1', 'title' => $notificationData['pushActionButton1Text']);
                $pushData['data']['pushActionButton1Url'] = trailingslashit(esc_url_raw($notificationData['pushActionButton1Url']));
            }

            if ($notificationData['pushActionButton2'] == 'on') {
                $pushData['actions'][] = array('action' => 'action2', 'title' => $notificationData['pushActionButton2Text']);
                $pushData['data']['pushActionButton2Url'] = trailingslashit(esc_url_raw($notificationData['pushActionButton2Url']));
            }

            $segment = $notificationData['pushSegment'];

            if (wp_verify_nonce($nonce, self::$optionName."_send_notification_nonce")) {
                if ($notificationData['pushScheduled'] == 'on') {
                    $userTimezone = ((get_option('gmt_offset') > 0) ? '+' : '-') . get_option('gmt_offset');
                    $dateTime = strtotime($notificationData['pushScheduledDatetime'].' '.$userTimezone);
                    $scheduleNotification = wp_schedule_single_event($dateTime, self::$optionName."_send_scheduled_notification", array('notificationData' => $notificationData));
                    if ($scheduleNotification) {
                        set_transient(self::$optionName."_send_scheduled_notification_date_".$dateTime, $dateTime - time(), $dateTime - time());
                        htmlspecialchars(wp_send_json_success(array(
                            'scheduled' => true,
                            'message' => esc_html__('Notification scheduled successfully', self::$textDomain),
                            'date' => get_date_from_gmt(date('Y-m-d H:i:s', $dateTime), 'd-M-Y'),
                            'time' => get_date_from_gmt(date('Y-m-d H:i:s', $dateTime), 'h:i A'),
                            'datetime' => $dateTime,
                            'timeleft' => $dateTime - time(),
                            'timetotal' => get_transient(self::$optionName."_send_scheduled_notification_date_".$dateTime),
                            'args' => array('notificationData' => $notificationData),
                        ), null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                    } else {
                        wp_send_json_error(array(
                            'scheduled' => false,
                            'message' => esc_html__('Notification scheduling failed', self::$textDomain),
                        ));
                    }
                } else {
                    $sendNotification = self::sendNotification($pushData, $segment);
                    if ($sendNotification) {
                        wp_send_json_success(array(
                            'message' => esc_html__('Notification sent successfully', self::$textDomain),
                        ));
                    } else {
                        wp_send_json_error(array(
                            'message' => esc_html__('Notification sending failed', self::$textDomain),
                        ));
                    }
                }
            } else {
                wp_send_json_error(array(
                    'message' => esc_html__('Push notification failed', self::$textDomain),
                ));
            }
        }

        public function handleScheduledNotification() {
            $hook = self::$optionName."_send_scheduled_notification";
            $time = intval($_REQUEST['time']);
            $args = json_decode(stripslashes($_REQUEST['args']), true);
            $method = $_REQUEST['method'];
            $unscheduled = wp_unschedule_event($time, $hook, $args, false);
            delete_transient(self::$optionName."_send_scheduled_notification_date_".$time);

            if ($unscheduled) {
                switch ($method) {
                    case 'send':
                        $sentNotification = self::sendScheduledNotification($args['notificationData']);
                        if ($sentNotification) {
                            wp_send_json_success();
                        } else {
                            wp_send_json_error();
                        }
                        break;
                    case 'edit':
                        $notificationData = json_decode(stripslashes($_REQUEST['notificationData']), true);
                        $userTimezone = ((get_option('gmt_offset') > 0) ? '+' : '-') . get_option('gmt_offset');
                        $dateTime = strtotime($notificationData['pushScheduledDatetime'].' '.$userTimezone);
                        $scheduleNotification = wp_schedule_single_event($dateTime, self::$optionName."_send_scheduled_notification", array('notificationData' => $notificationData));
                        if ($scheduleNotification) {
                            set_transient(self::$optionName."_send_scheduled_notification_date_".$dateTime, $dateTime - time(), $dateTime - time());
                            htmlspecialchars(wp_send_json_success(array(
                                'scheduled' => true,
                                'message' => esc_html__('Notification scheduled successfully', self::$textDomain),
                                'date' => get_date_from_gmt(date('Y-m-d H:i:s', $dateTime), 'd-M-Y'),
                                'time' => get_date_from_gmt(date('Y-m-d H:i:s', $dateTime), 'h:i A'),
                                'datetime' => $dateTime,
                                'timeleft' => $dateTime - time(),
                                'timetotal' => get_transient(self::$optionName."_send_scheduled_notification_date_".$dateTime),
                                'args' => array('notificationData' => $notificationData),
                                'oldSchedule' => $time,
                            ), null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                        } else {
                            wp_send_json_error(array(
                                'scheduled' => false,
                                'message' => esc_html__('Notification scheduling failed.', self::$textDomain),
                            ));
                        }
                        break;
                    case 'remove':
                        wp_send_json_success();
                        break;
                    default:
                        echo 'Error: method not handled';
                        return;
                }
            } else {
                wp_send_json_error();
            }
        }

        public function renderMetaBoxContent($post, $callbackArgs) {
            $pwaNoPushNewContent = get_post_meta($post->ID, 'pwaNoPushNewContent', true);
            $pwaNoPushWooNewProduct = get_post_meta($post->ID, 'pwaNoPushWooNewProduct', true);
            $pwaNoPushWooPriceDrop = get_post_meta($post->ID, 'pwaNoPushWooPriceDrop', true);
            $pwaNoPushWooSalePrice = get_post_meta($post->ID, 'pwaNoPushWooSalePrice', true);
            $pwaNoPushWooBackInStock = get_post_meta($post->ID, 'pwaNoPushWooBackInStock', true);
            wp_nonce_field(self::$optionName."_no_push_meta_nonce", self::$optionName."_no_push_meta_nonce");
            if (daftplugInstantify::isWooCommerceActive() && $post->post_type == 'product') {
                if (daftplugInstantify::getSetting('pwaPushWooNewProduct') == 'on') { ?>
                    <label style="display: block; margin: 5px;">
                        <input type="checkbox" name="pwaNoPushWooNewProduct" value="on" <?php checked($pwaNoPushWooNewProduct, 'on'); ?>>
                        <?php esc_html_e('Don\'t Send WooCommerce New Product Notification', self::$textDomain); ?>
                    </label style="display: block; margin: 5px;">
                <?php }
                if (daftplugInstantify::getSetting('pwaPushWooPriceDrop') == 'on') { ?>
                    <label style="display: block; margin: 5px;">
                        <input type="checkbox" name="pwaNoPushWooPriceDrop" value="on" <?php checked($pwaNoPushWooPriceDrop, 'on'); ?>>
                        <?php esc_html_e('Don\'t Send WooCommerce Price Drop Notification', self::$textDomain); ?>
                    </label>
                <?php }
                if (daftplugInstantify::getSetting('pwaPushWooSalePrice') == 'on') { ?>
                    <label style="display: block; margin: 5px;">
                        <input type="checkbox" name="pwaNoPushWooSalePrice" value="on" <?php checked($pwaNoPushWooSalePrice, 'on'); ?>>
                        <?php esc_html_e('Don\'t Send WooCommerce Sale Price Notification', self::$textDomain); ?>
                    </label>
                <?php }
                if (daftplugInstantify::getSetting('pwaPushWooBackInStock') == 'on') { ?>
                    <label style="display: block; margin: 5px;">
                        <input type="checkbox" name="pwaNoPushWooBackInStock" value="on" <?php checked($pwaNoPushWooBackInStock, 'on'); ?>>
                        <?php esc_html_e('Don\'t Send WooCommerce Back In Stock Notification', self::$textDomain); ?>
                    </label>
                <?php }
            } else {
                if (daftplugInstantify::getSetting('pwaPushNewContent') == 'on') { ?>
                    <label style="display: block; margin: 5px;">
                        <input type="checkbox" name="pwaNoPushNewContent" value="on" <?php checked($pwaNoPushNewContent, 'on'); ?>>
                        <?php esc_html_e('Don\'t Send New Content Notification', self::$textDomain); ?>
                    </label>
                <?php }
            }
        }

        public function addMetaBoxes($postType, $post)  {
            if ((daftplugInstantify::getSetting('pwaPushNewContent') == 'on' && in_array($post->post_type, (array)daftplugInstantify::getSetting('pwaPushNewContentPostTypes'))) || $post->post_type == 'product') {
                add_meta_box(self::$optionName."_no_push_meta_box", esc_html__('Push Notifications', self::$textDomain), array($this, 'renderMetaBoxContent'), $postType, 'side', 'default', array());
            }
        }

        public function filterWooCommercePostData($data, $postArr) {
            global $post;

            if (!$post || $post->post_type != 'product') {
                return $data;
            }

            $wooCurrency = html_entity_decode(get_woocommerce_currency_symbol(get_option('woocommerce_currency')));
            $priceFormat = get_woocommerce_price_format();
            $oldSalePrice = get_post_meta($post->ID, '_sale_price', true);
            $newSalePrice = $postArr['_sale_price'];
            $oldRegularPrice = get_post_meta($post->ID, '_regular_price', true);
            $newRegularPrice = $postArr['_regular_price'];
            $oldStock = get_post_meta($post->ID, '_stock', true );
            $newStock = $postArr['_stock'];

            if ($oldRegularPrice) {
                set_transient(self::$optionName."_regular_price", sprintf($priceFormat, $wooCurrency, $oldRegularPrice), 5);
            } else {
                set_transient(self::$optionName."_regular_price", sprintf($priceFormat, $wooCurrency, $newRegularPrice), 5);
            }

            if ((!$oldSalePrice && $newSalePrice) || ($oldSalePrice > $newSalePrice && $newSalePrice != 0)) {
                set_transient(self::$optionName."_sale_price", sprintf($priceFormat, $wooCurrency, $newSalePrice), 5);
            }

            if ($newRegularPrice < $oldRegularPrice) {
                set_transient(self::$optionName."_dropped_price", sprintf($priceFormat, $wooCurrency, $newRegularPrice), 5);
            }

            if ($oldStock == 0 && $newStock > 0) {
                set_transient(self::$optionName."_back_in_stock", true, 5);
            }

            return $data;
        }

        public function doWooOrderStatusUpdatePush($orderId, $oldStatus, $newStatus, $order) {
            if ($oldStatus != $newStatus) {
                $pushData = array(
                    'title' => __('Your Order Status Updated', self::$textDomain),
                    'body' => sprintf(__('Your order (ID %s) status has updated from %s to %s. Click on this notification to see the order.', self::$textDomain), $orderId, $oldStatus, $newStatus),
                    'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                    'data' => array(
                        'url' => trailingslashit($order->get_view_order_url()),
                    ),
                );

                self::sendNotification($pushData, '', $order->get_user_id());
            }
        }

        public function doUmRoleUpdatePush($new_roles, $old_roles, $user_id) {
            $diff = array_diff($old_roles, $new_roles);
            if (count($old_roles) != count($new_roles) || !empty($diff)) {
                $oldMembership = array_map(function($item) {
                    return UM()->roles()->get_role_name($item);
                }, $old_roles);
                $oldMembership = implode(', ', $oldMembership);
                $newMembership = array_map(function($item) {
                    return UM()->roles()->get_role_name($item);
                }, $new_roles);
                $newMembership = implode(', ', $newMembership);
                $pushData = array(
                    'title' => __('Your membership level has been changed', self::$textDomain),
                    'body' => sprintf(__('Your membership level has been changed from %s to %s.', self::$textDomain), $oldMembership, $newMembership),
                    'icon' => um_get_avatar_url(get_avatar($user_id, 40)),
                    'data' => array(
                        'url' => um_user_profile_url(),
                    ),
                );

                self::sendNotification($pushData, '', $user_id);
            }
        }

        public function doAutoPush($id, $post) {
            $isAutosave = (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_autosave($id);
            $isRevision = wp_is_post_revision($id);
            $isValidNonce = (isset($_POST[self::$optionName."_no_push_meta_nonce"]) && wp_verify_nonce($_POST[self::$optionName."_no_push_meta_nonce"], self::$pluginBasename)) ? 'true' : 'false';
            $pwaNoPushNewContentMeta = (isset($_POST['pwaNoPushNewContent']) ? $_POST['pwaNoPushNewContent'] : 'off');
            $pwaNoPushWooNewProductMeta = (isset($_POST['pwaNoPushWooNewProduct']) ? $_POST['pwaNoPushWooNewProduct'] : 'off');
            $pwaNoPushWooPriceDropMeta = (isset($_POST['pwaNoPushWooPriceDrop']) ? $_POST['pwaNoPushWooPriceDrop'] : 'off');
            $pwaNoPushWooSalePriceMeta = (isset($_POST['pwaNoPushWooSalePrice']) ? $_POST['pwaNoPushWooSalePrice'] : 'off');
            $pwaNoPushWooBackInStockMeta = (isset($_POST['pwaNoPushWooBackInStock']) ? $_POST['pwaNoPushWooBackInStock'] : 'off');

            if ($post->post_status != 'publish' || $isAutosave || $isRevision || !$isValidNonce) {
                return;
            }

            if ($post->post_type !== 'product') {
                // New Content Push
                if (daftplugInstantify::getSetting('pwaPushNewContent') == 'on' && $pwaNoPushNewContentMeta == 'off' && in_array($post->post_type, (array)daftplugInstantify::getSetting('pwaPushNewContentPostTypes'))) {
                    if (strpos($_POST['_wp_http_referer'], 'post-new.php') !== false) {
                        $pushData = array(
                            'title' => sprintf(__('New %s - %s', self::$textDomain), get_post_type_labels($post)->singular_name, $post->post_title),
                            'body' => substr(strip_tags($post->post_content), 0, 77).'...',
                            'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                            'data' => array(
                                'url' => trailingslashit(get_permalink($id)),
                            ),
                        );

                        if (has_post_thumbnail($id)) {
                            $pushData['image'] = get_the_post_thumbnail_url($id);
                        }

                        self::sendNotification($pushData, 'all');
                    }
                }
            } else {
                // New Product Push
                if (daftplugInstantify::getSetting('pwaPushWooNewProduct') == 'on' && $pwaNoPushWooNewProductMeta == 'off') {
                    if (strpos($_POST['_wp_http_referer'], 'post-new.php') !== false) {
                        $pushData = array(
                            'title' => sprintf(__('New Product - %s', self::$textDomain), $post->post_title),
                            'body' => substr(strip_tags($post->post_content), 0, 77).'...',
                            'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                            'data' => array(
                                'url' => trailingslashit(get_permalink($id)),
                            ),
                        );

                        if (has_post_thumbnail($id)) {
                            $pushData['image'] = get_the_post_thumbnail_url($id);
                        }

                        self::sendNotification($pushData, 'all');
                    }
                }

                // Price Drop Push
                if (daftplugInstantify::getSetting('pwaPushWooPriceDrop') == 'on' && $pwaNoPushWooPriceDropMeta == 'off' && get_transient(self::$optionName."_dropped_price")) {
                    $pushData = array(
                        'title' => sprintf(__('Price Drop - %s', self::$textDomain), $post->post_title),
                        'body' => sprintf(__('Price dropped from %s to %s', self::$textDomain), get_transient(self::$optionName."_regular_price"), get_transient(self::$optionName."_dropped_price")),
                        'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                        'data' => array(
                            'url' => trailingslashit(get_permalink($id)),
                        ),
                    );

                    if (has_post_thumbnail($id)) {
                        $pushData['image'] = get_the_post_thumbnail_url($id);
                    }

                    self::sendNotification($pushData, 'all');
                }

                // Sale Price Push
                if (daftplugInstantify::getSetting('pwaPushWooSalePrice') == 'on' && $pwaNoPushWooSalePriceMeta == 'off' && get_transient(self::$optionName."_sale_price")) {
                    $pushData = array(
                        'title' => sprintf(__('New Sale Price - %s', self::$textDomain), $post->post_title),
                        'body' => sprintf(__('New Sale Price: %s', self::$textDomain), get_transient(self::$optionName."_sale_price")),
                        'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                        'data' => array(
                            'url' => trailingslashit(get_permalink($id)),
                        ),
                    );

                    if (has_post_thumbnail($id)) {
                        $pushData['image'] = get_the_post_thumbnail_url($id);
                    }

                    self::sendNotification($pushData, 'all');
                }

                // Back In Stock Push
                if (daftplugInstantify::getSetting('pwaPushWooBackInStock') == 'on' && $pwaNoPushWooBackInStockMeta == 'off' && get_transient(self::$optionName."_back_in_stock")) {
                    $pushData = array(
                        'title' => sprintf(__('Back In Stock - %s', self::$textDomain), $post->post_title),
                        'body' => sprintf(__('%s is now back in stock', self::$textDomain), $post->post_title),
                        'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                        'data' => array(
                            'url' => trailingslashit(get_permalink($id)),
                        ),
                    );

                    if (has_post_thumbnail($id)) {
                        $pushData['image'] = get_the_post_thumbnail_url($id);
                    }

                    self::sendNotification($pushData, 'all');
                }
            }
        }

        public static function sendNotification($pushData, $segment = 'all', $targetUserId = null) {
            require_once(plugin_dir_path(self::$pluginFile) . implode(DIRECTORY_SEPARATOR, array('pwa', 'includes', 'libs', 'web-push-php', 'autoload.php')));

            $auth = array(
                'VAPID' => array(
                    'subject' => get_bloginfo('wpurl'),
                    'publicKey' => self::$vapidKeys['pwaPublicKey'],
                    'privateKey' => self::$vapidKeys['pwaPrivateKey'],
                ),
            );

            $webPush = new WebPush($auth, array(
                'TTL' => (int)daftplugInstantify::getSetting('pwaPushTtl'),
                'batchSize' => (int)count(self::$subscribedDevices),
            ));
            $webPush->setReuseVAPIDHeaders(true);

            $pushData = wp_parse_args($pushData, array(
                'title' => '',
                'badge' => '',
                'body' => '',
                'icon' => '',
                'image' => '',
                'data' => '',
                'tag' => 'notification',
                'renotify' => true,
                'requireInteraction' => false,
                'vibrate' => array(),
            ));

            if ($targetUserId !== null) {
                $subscriptions = array();
                foreach (self::$subscribedDevices as $subscribedDevice) {
                    if ($targetUserId == $subscribedDevice['user']) {
                        $subscriptions[] =  array(
                                                'subscription' => Subscription::create(
                                                    array(
                                                        'endpoint' => $subscribedDevice['endpoint'],
                                                        'publicKey' => $subscribedDevice['userKey'],
                                                        'authToken' => $subscribedDevice['userAuth'],
                                                    )
                                                ),
                                                'payload' => null
                                            );
                    }
                }

                foreach ($subscriptions as $subscription) {
                    $webPush->queueNotification(
                        $subscription['subscription'],
                        json_encode($pushData)
                    );
                }
            } else {
                switch ($segment) {
                    case 'all':
                        $subscriptions = array();
                        foreach (self::$subscribedDevices as $subscribedDevice) {
                            $subscriptions[] =  array(
                                                    'subscription' => Subscription::create(
                                                        array(
                                                            'endpoint' => $subscribedDevice['endpoint'],
                                                            'publicKey' => $subscribedDevice['userKey'],
                                                            'authToken' => $subscribedDevice['userAuth'],
                                                        )
                                                    ),
                                                    'payload' => null
                                                );
                        }
        
                        foreach ($subscriptions as $subscription) {
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                        }
                        break;
                    case 'mobile':
                        $subscriptions = array();
                        foreach (self::$subscribedDevices as $subscribedDevice) {
                            if (preg_match('[Android|iOS]', $subscribedDevice['deviceInfo'])) {
                                $subscriptions[] =  array(
                                                        'subscription' => Subscription::create(
                                                            array(
                                                                'endpoint' => $subscribedDevice['endpoint'],
                                                                'publicKey' => $subscribedDevice['userKey'],
                                                                'authToken' => $subscribedDevice['userAuth'],
                                                            )
                                                        ),
                                                        'payload' => null
                                                    );
                            }
                        }
        
                        foreach ($subscriptions as $subscription) {
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                        }
                        break;
                    case 'desktop':
                        $subscriptions = array();
                        foreach (self::$subscribedDevices as $subscribedDevice) {
                            if (preg_match('[Windows|Linux|Mac|Ubuntu|Solaris]', $subscribedDevice['deviceInfo'])) {
                                $subscriptions[] =  array(
                                                        'subscription' => Subscription::create(
                                                            array(
                                                                'endpoint' => $subscribedDevice['endpoint'],
                                                                'publicKey' => $subscribedDevice['userKey'],
                                                                'authToken' => $subscribedDevice['userAuth'],
                                                            )
                                                        ),
                                                        'payload' => null
                                                    );
                            }
                        }
        
                        foreach ($subscriptions as $subscription) {
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                        }
                        break;
                    case 'registered':
                        $subscriptions = array();
                        foreach (self::$subscribedDevices as $subscribedDevice) {
                            if (is_numeric($subscribedDevice['user'])) {
                                $subscriptions[] =  array(
                                                        'subscription' => Subscription::create(
                                                            array(
                                                                'endpoint' => $subscribedDevice['endpoint'],
                                                                'publicKey' => $subscribedDevice['userKey'],
                                                                'authToken' => $subscribedDevice['userAuth'],
                                                            )
                                                        ),
                                                        'payload' => null
                                                    );
                            }
                        }
        
                        foreach ($subscriptions as $subscription) {
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                        }
                        break;
                    case 'unregistered':
                        $subscriptions = array();
                        foreach (self::$subscribedDevices as $subscribedDevice) {
                            if ($subscribedDevice['user'] == 'Unregistered') {
                                $subscriptions[] =  array(
                                                        'subscription' => Subscription::create(
                                                            array(
                                                                'endpoint' => $subscribedDevice['endpoint'],
                                                                'publicKey' => $subscribedDevice['userKey'],
                                                                'authToken' => $subscribedDevice['userAuth'],
                                                            )
                                                        ),
                                                        'payload' => null
                                                    );
                            }
                        }
        
                        foreach ($subscriptions as $subscription) {
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                        }
                        break;
                    case substr($segment, 0, 7) === 'Country':
                        $country = str_replace('Country - ', '', $segment);
                        $subscriptions = array();
                        foreach (self::$subscribedDevices as $subscribedDevice) {
                            if ($subscribedDevice['country'] == $country) {
                                $subscriptions[] =  array(
                                                        'subscription' => Subscription::create(
                                                            array(
                                                                'endpoint' => $subscribedDevice['endpoint'],
                                                                'publicKey' => $subscribedDevice['userKey'],
                                                                'authToken' => $subscribedDevice['userAuth'],
                                                            )
                                                        ),
                                                        'payload' => null
                                                    );
                            }
                        }
        
                        foreach ($subscriptions as $subscription) {
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                        }
                        break;
                    default:
                        $subscription = array(
                            'subscription' => Subscription::create(
                                array(
                                    'endpoint' => self::$subscribedDevices[$segment]['endpoint'],
                                    'publicKey' => self::$subscribedDevices[$segment]['userKey'],
                                    'authToken' => self::$subscribedDevices[$segment]['userAuth'],
                                )
                            ),
                            'payload' => null
                        );
    
                        $webPush->queueNotification(
                            $subscription['subscription'],
                            json_encode($pushData)
                        );
                        break;
                }
            }

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if (!$report->isSuccess()) {
                    unset(self::$subscribedDevices[$endpoint]);
                    update_option(self::$optionName."_subscribed_devices", self::$subscribedDevices);
                } else {
                    return true;
                }
            }
        }

        public static function sendScheduledNotification($notificationData) {
            $pushData = array(
                'title' => !empty($notificationData['pushTitle']) ? $notificationData['pushTitle'] : '',
                'body' => !empty($notificationData['pushBody']) ? $notificationData['pushBody'] : '',
                'image' => !empty($notificationData['pushImage']) ? esc_url_raw(wp_get_attachment_image_src($notificationData['pushImage'], 'full')[0] ?? '') : '',
                'icon' => !empty($notificationData['pushIcon']) ? esc_url_raw(wp_get_attachment_image_src($notificationData['pushIcon'], 'full')[0] ?? '') : '',
                'data' => array(
                    'url' => !empty($notificationData['pushUrl']) ? trailingslashit(esc_url_raw($notificationData['pushUrl'])).'?utm_source=pwa-notification' : '',
                ),
                'requireInteraction' => ($notificationData['pushFixed'] == 'on') ? true : false,
                'vibrate' => ($notificationData['pushVibrate'] == 'on') ? array(200, 100, 200) : array(),
            );

            if ($notificationData['pushActionButton1'] == 'on') {
                $pushData['actions'][] = array('action' => 'action1', 'title' => $notificationData['pushActionButton1Text']);
                $pushData['data']['pushActionButton1Url'] = trailingslashit(esc_url_raw($notificationData['pushActionButton1Url']));
            }

            if ($notificationData['pushActionButton2'] == 'on') {
                $pushData['actions'][] = array('action' => 'action2', 'title' => $notificationData['pushActionButton2Text']);
                $pushData['data']['pushActionButton2Url'] = trailingslashit(esc_url_raw($notificationData['pushActionButton2Url']));
            }

            $segment = $notificationData['pushSegment'];

            return self::sendNotification($pushData, $segment);
        }

        public function getPostTypes() {
            $excludes = array('product', 'attachment');
            $postTypes = get_post_types(
                            array(
                                'public' => true,
                            ),
                            'names'
                         );

            foreach ($excludes as $exclude) {
                unset($postTypes[$exclude]);
            }

            return array_values($postTypes);
        }

        public function getScheduledNotifications() {
            $crons  = _get_cron_array();
            $events = array();
        
            if (empty($crons)) {
                return array();
            }
        
            foreach ($crons as $time => $cron) {
                foreach ($cron as $hook => $dings) {
                    foreach ($dings as $sig => $data) {
                        if ($hook == self::$optionName."_send_scheduled_notification") {
                            $events["$hook-$sig-$time"] = array(
                                'time' => $time,
                                'sig' => $sig,
                                'args' => $data['args'],
                            );
                        }
                    }
                }
            }
        
            return $events;
        }

        public function getSubscriberAnalytics() {
            $dates = $this->getLastNDays(365);
            $subscribers = get_transient(self::$optionName."_subscriber_analytics");
            $data = array();

            foreach ($dates as $date) {
                if (isset($subscribers[$date])) {
                    $data[] = $subscribers[$date];
                } else {
                    $data[] = 0;
                }
            }

            wp_send_json_success(array(
                'data' => $data,
                'dates' => $dates,
            ));
        }

        public function getSubscriberStats() {
            $browser = array();
            $device = array();
            $country = array();
            $status = array();

			foreach (self::$subscribedDevices as $key => $value) {
			    $browser[] = trim(explode(' on ', self::$subscribedDevices[$key]['deviceInfo'])[0]);
                $device[] = trim(explode(' on ', self::$subscribedDevices[$key]['deviceInfo'])[1]);
                $country[] = self::$subscribedDevices[$key]['country'];
                $status[] = self::$subscribedDevices[$key]['user'];
			}

            $browserData = array_count_values($browser);
            $deviceData = array_count_values($device);
            $countryData = array_count_values($country);
            $statusData = array_count_values($status);
            $statusNames = array();
            $statusCount = array();

            if (!empty($statusData)) {
                if (array_key_exists('Unregistered', $statusData) && count($statusData) > 1) {
                    $statusNames[] = 'Unregistered';
                    $statusNames[] = 'Registered';
                    $statusCount[] = $statusData['Unregistered'];
                    $statusCount[] = array_sum(array_diff_key($statusData, array_flip(array('Unregistered'))));
                } elseif (array_key_exists('Unregistered', $statusData) && count($statusData) > 0) {
                    $statusNames[] = 'Unregistered';
                    $statusCount[] = $statusData['Unregistered'];
                } elseif (!array_key_exists('Unregistered', $statusData) && count($statusData) > 0) {
                    $statusNames[] = 'Registered';
                    $statusCount[] = array_sum($statusData);
                }
            }

            wp_send_json_success(array(
                'browserNames' => array_keys($browserData),
                'browserCount' => array_values($browserData),
                'deviceNames' => array_keys($deviceData),
                'deviceCount' => array_values($deviceData),
                'countryNames' => array_keys($countryData),
                'countryCount' => array_values($countryData),
                'statusNames' => $statusNames,
                'statusCount' => $statusCount,
            ));
		}

        public function getLastNDays($days, $format = 'j M Y') {
            $m = date("m"); $de= date("d"); $y= date("Y");
            $dateArray = array();
            for ($i=0; $i<=$days-1; $i++) {
                $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y)); 
            }
            
            return array_reverse($dateArray);
        }
    }
}