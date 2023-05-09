<?php

if (!defined('ABSPATH')) exit;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

if (!class_exists('daftplugInstantifyPwaPublicPushnotifications')) {
    class daftplugInstantifyPwaPublicPushnotifications {
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
        public $daftplugInstantifyPwaPublic;

        private static $noteData;

        public function __construct($config, $daftplugInstantifyPwaPublic) {
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
            $this->daftplugInstantifyPwaPublic = $daftplugInstantifyPwaPublic;

            add_filter(self::$optionName."_pwa_serviceworker", array($this, 'addPushToServiceWorker'));
            add_filter(self::$optionName."_public_js_vars", array($this, 'addPushJsVars'));
            add_action("wp_ajax_".self::$optionName."_handle_subscription", array($this, 'handleSubscription'));
            add_action("wp_ajax_nopriv_".self::$optionName."_handle_subscription", array($this, 'handleSubscription'));
            add_action("wp_ajax_".self::$optionName."_save_subscriber_analytics", array($this, 'saveSubscriberAnalytics'));
            add_action("wp_ajax_nopriv_".self::$optionName."_save_subscriber_analytics", array($this, 'saveSubscriberAnalytics'));

            if (daftplugInstantify::isWooCommerceActive()) {
                if (daftplugInstantify::getSetting('pwaPushWooNewOrder') == 'on') {
                    add_action('woocommerce_new_order', array($this, 'doWooNewOrderPush'));  
                }
                if (daftplugInstantify::getSetting('pwaPushWooLowStock') == 'on') {
                    add_action('woocommerce_thankyou', array($this, 'doWooLowStockPush'));  
                }
                if (daftplugInstantify::getSetting('pwaPushWooAbandonedCart') == 'on') {
                    add_action('wp', array($this, 'doWooAbandonedCartPush'));
                }
            }

            if (daftplugInstantify::isBuddyPressActive()) {
                if (daftplugInstantify::getSetting('pwaPushBpMemberMention') == 'on') {
                    add_action('bp_activity_sent_mention_email', array($this, 'doBpMemberMentionPush'), 10, 5);
                }
                if (daftplugInstantify::getSetting('pwaPushBpMemberReply') == 'on') {
                    add_action('bp_activity_sent_reply_to_update_notification', array($this, 'doBpMemberCommentPush'), 10, 4);
                    add_action('bp_activity_sent_reply_to_reply_notification', array($this, 'doBpMemberReplyPush'), 10, 4);
                }
                if (daftplugInstantify::getSetting('pwaPushBpNewMessage') == 'on') {
                    add_action('messages_message_sent', array($this, 'doBpNewMessagePush'), 10, 1);
                }
                if (daftplugInstantify::getSetting('pwaPushBpFriendRequest') == 'on') {
                    add_action('friends_friendship_requested', array($this, 'doBpFriendRequestPush'), 1, 4);
                }
                if (daftplugInstantify::getSetting('pwaPushBpFriendAccepted') == 'on') {
                    add_action('friends_friendship_accepted', array($this, 'doBpFriendAcceptedPush'), 1, 4);
                }
            }

            if (daftplugInstantify::isPeepsoActive()) {
                if (daftplugInstantify::getSetting('pwaPushPeepsoNotifications') == 'on') {
                    add_filter('peepso_notifications_data_before_add', array($this, 'doPeepsoNotification'), 99);
                }
            }

            if (daftplugInstantify::isUltimateMemberActive() && daftplugInstantify::isUltimateMemberActive('um-notifications')) {
                if (daftplugInstantify::getSetting('pwaPushUmNewComment') == 'on' || daftplugInstantify::getSetting('pwaPushUmGuestComment') == 'on' || daftplugInstantify::getSetting('pwaPushUmCommentReply') == 'on') {
                    add_action('comment_post', array($this, 'doUmCommentReplyPush'), 10, 2);
                }
                if (daftplugInstantify::getSetting('pwaPushUmProfileView') == 'on' || daftplugInstantify::getSetting('pwaPushUmGuestProfileView') == 'on') {
                    add_action('wp_head', array($this, 'doUmProfileViewsPush'), 100);
                }
                if (daftplugInstantify::isUltimateMemberActive('um-messaging') && daftplugInstantify::getSetting('pwaPushUmPrivateMessage') == 'on') {
                    add_action('um_after_new_message', array($this, 'doUmPrivateMessagePush'), 50, 4);
                }
                if (daftplugInstantify::isUltimateMemberActive('um-followers') && daftplugInstantify::getSetting('pwaPushUmNewFollow') == 'on') {
                    add_action('um_followers_after_user_follow', array($this, 'doUmNewFollowPush'), 10, 2);
                }
                if (daftplugInstantify::isUltimateMemberActive('um-friends')) {
                    if (daftplugInstantify::getSetting('pwaPushUmFriendRequest') == 'on') {
                        add_action('um_friends_after_user_friend_request', array($this, 'doUmFriendRequestPush'), 10, 2);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmFriendRequestAccepted') == 'on') {
                        add_action('um_friends_after_user_friend', array($this, 'doUmFriendRequestAcceptedPush'), 10, 2);
                    }
                }
                if (daftplugInstantify::isUltimateMemberActive('um-social-activity')) {
                    if (daftplugInstantify::getSetting('pwaPushUmWallPost') == 'on') {
                        add_action('um_activity_after_wall_post_published', array($this, 'doUmWallPostPush'), 90, 3);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmWallComment') == 'on') {
                        add_action('um_notification_activity_comment', array($this, 'doUmWallCommentPush'), 90, 4);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmPostLike') == 'on') {
                        add_action('um_activity_after_wall_post_liked', array($this, 'doUmPostLikePush'), 90, 2);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmNewMention') == 'on') {
                        add_action('um_followers_new_mention', array($this, 'doUmNewMentionPush'), 10, 3);
                        add_action('um_following_new_mention', array($this, 'doUmNewMentionPush'), 10, 3);
                        add_action('um_friends_new_mention', array($this, 'doUmNewMentionPush'), 10, 3);
                    }
                }
                if (daftplugInstantify::isUltimateMemberActive('um-groups')) {
                    if (daftplugInstantify::getSetting('pwaPushUmGroupApprove') == 'on') {
                        add_action('um_groups_after_member_changed_status__approved', array($this, 'doUmGroupApprovePush'), 1, 5);
                        add_action('um_groups_after_member_changed_status__hidden_approved', array($this, 'doUmGroupApprovePush'), 1, 5);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmGroupJoinRequest') == 'on') {
                        add_action('um_groups_after_member_changed_status__pending_admin_review', array($this, 'doUmGroupJoinRequestPush'), 1, 3);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmGroupInvitation') == 'on') {
                        add_action('um_groups_after_member_changed_status__pending_member_review', array($this, 'doUmGroupInvitationPush'), 1, 3);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmGroupRoleChange') == 'on') {
                        add_action('um_groups_after_member_changed_role', array($this, 'doUmGroupRoleChangePush'), 1, 4);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmGroupPost') == 'on') {
                        add_action('um_groups_after_wall_post_published', array($this, 'doUmGroupPostPush'), 55, 3);
                    }
                    if (daftplugInstantify::getSetting('pwaPushUmGroupComment') == 'on') {
                        add_action('um_groups_after_wall_comment_published', array($this, 'doUmGroupCommentPush'), 55, 4);
                    }
                }
            }

            add_action(self::$optionName."_send_scheduled_notification", array($this, 'sendScheduledNotification'), 10, 1);

            if (daftplugInstantify::getSetting('pwaPushButton') == 'on') {
                add_filter(self::$optionName."_public_html", array($this, 'renderPushButton'));
            }

            if (daftplugInstantify::getSetting('pwaPushPrompt') == 'on') {
                add_filter(self::$optionName."_public_html", array($this, 'renderPushPrompt'));
            }           
        }

        public function handleSubscription() {
            $subscribedDevices = self::$subscribedDevices;
            $endpoint = $_REQUEST['endpoint'];
            $userKey = $_REQUEST['userKey'];
            $userAuth = $_REQUEST['userAuth'];
            $deviceInfo = $_REQUEST['deviceInfo'];
            $date = date('j M Y');
            $country = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.$_SERVER['REMOTE_ADDR']), true);
            $method = $_REQUEST['method'];
            $user = (is_user_logged_in() ? get_current_user_id() : 'Unregistered');
            $roles = (is_user_logged_in() ? (array)wp_get_current_user()->roles : array());
            $cartItemCount = (daftplugInstantify::isWooCommerceActive()) ? WC()->cart->get_cart_contents_count() : 0;
            $lastUpdated = date('Y-m-d H:i:s');
            
            switch ($method) {
                case 'add':
                    $subscribedDevices[$endpoint] = array(
                        'endpoint' => $endpoint,
                        'userKey' => $userKey,
                        'userAuth' => $userAuth,
                        'deviceInfo' => $deviceInfo,
                        'date' => $date,
                        'country' => @$country['geoplugin_countryName'],
                        'user' => $user,
                        'roles' => $roles,
                        'cartItemCount' => $cartItemCount,
                        'lastUpdated' => $lastUpdated,
                    );
                    break;
                case 'update':
                    if (array_key_exists($endpoint, $subscribedDevices)) {
                        foreach ($subscribedDevices as $key => $value) {
                            $subscribedDevices[$endpoint]['userKey'] = $userKey;
                            $subscribedDevices[$endpoint]['userAuth'] = $userAuth;
                            $subscribedDevices[$endpoint]['deviceInfo'] = $deviceInfo;
                            $subscribedDevices[$endpoint]['user'] = $user;
                            $subscribedDevices[$endpoint]['roles'] = $roles;
                            $subscribedDevices[$endpoint]['cartItemCount'] = $cartItemCount;
                            $subscribedDevices[$endpoint]['lastUpdated'] = $lastUpdated;
                        }
                    } else {
                        $subscribedDevices[$endpoint] = array(
                            'endpoint' => $endpoint,
                            'userKey' => $userKey,
                            'userAuth' => $userAuth,
                            'deviceInfo' => $deviceInfo,
                            'date' => $date,
                            'country' => @$country['geoplugin_countryName'],
                            'user' => $user,
                            'roles' => $roles,
                            'cartItemCount' => $cartItemCount,
                            'lastUpdated' => $lastUpdated,
                        );
                    }
                    break;
                case 'remove':
                    unset($subscribedDevices[$endpoint]);
                    break;
                default:
                    echo 'Error: method not handled';
                    return;
            }

            $handled = update_option(self::$optionName."_subscribed_devices", $subscribedDevices);

            if ($handled) {
                wp_die('1');
            } else {
                wp_die('0');
            }
        }

        public function saveSubscriberAnalytics() {
            $data = get_transient(self::$optionName."_subscriber_analytics");
            $data[(date('j M Y'))] += 1;
            set_transient(self::$optionName."_subscriber_analytics", $data, 31556926);

            wp_die();
        }

        public function doWooNewOrderPush($orderId) {
            if (!$orderId) {
                return;
            }

            $order = wc_get_order($orderId);
            $pushData = array(
                'title' => esc_html__('WooCommerce New Order', self::$textDomain),
                'body' => sprintf(__('You have new order for total %s%s. Click on notification to see it.', self::$textDomain), html_entity_decode (get_woocommerce_currency_symbol($order->get_currency())), $order->get_total()),
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => $order->get_view_order_url(),
                ),
            );

            self::sendNotification($pushData, 'wooNewOrder');
        }

        public function doWooLowStockPush($orderId) {
            if (!$orderId) {
                return;
            }
        
            $order = wc_get_order($orderId);
            $items = $order->get_items();
            
            foreach ($items as $item) {
                if ($item['variation_id'] > 0) {
                    $productId = $item['variation_id'];
                    $stock = get_post_meta($item['variation_id'], '_stock', true);
                    $sku = get_post_meta($item['variation_id'], '_sku', true);
                    $lowStockThreshold = get_post_meta($item['variation_id'], '_low_stock_amount', true);
                } else {
                    $productId = $item['product_id'];
                    $stock = get_post_meta( $item['product_id'], '_stock', true);
                    $sku = get_post_meta( $item['product_id'], '_sku', true);
                    $lowStockThreshold = get_post_meta($item['product_id'], '_low_stock_amount', true);
                }

                $lowStockThreshold = (!empty($lowStockThreshold) ? $lowStockThreshold : daftplugInstantify::getSetting('pwaPushWooLowStockThreshold'));

                if ($stock <= $lowStockThreshold && !get_post_meta($orderId, 'pwaPushWooLowStock', true)) {
                    update_post_meta($orderId, 'pwaPushWooLowStock', 1);
                    $pushData = array(
                        'title' => esc_html__('WooCommerce Low Stock', self::$textDomain),
                        'body' => sprintf(__('The product "%s" is running out of stock. Currently left %s in stock. Click on notification to see it.', self::$textDomain), $item['name'], $stock),
                        'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                        'data' => array(
                            'url' => get_permalink($productId),
                        ),
                    );

                    self::sendNotification($pushData, 'wooLowStock');
                }
            }
        }

        public function doWooAbandonedCartPush() {
            foreach (self::$subscribedDevices as $subscribedDevice) {
                if (((time() - strtotime($subscribedDevice['lastUpdated'])) > (daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval') * 3599)) && $subscribedDevice['cartItemCount'] > 0) {
                    $pushData = array();
                    $cartItemCount = $subscribedDevice['cartItemCount'];
                    $itemWord = ($cartItemCount > 1) ? esc_html__('items', self::$textDomain) : esc_html__('item', self::$textDomain);
                    $pushData = array(
                        'segment' => $subscribedDevice['endpoint'],
                        'title' => esc_html__('Your cart is waiting!', self::$textDomain),
                        'body' => sprintf(__('You have left %s %s you love in your cart. We are holding on them, but not for long!', self::$textDomain), $cartItemCount, $itemWord),
                        'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                        'data' => array(
                            'url' => wc_get_cart_url(),
                        ),
                    );
                    
                    if (!get_transient(self::$optionName."_sent_abandoned_cart_notification_".hash('crc32', $subscribedDevice['endpoint'], false))) {
                        $sendAbandonedCartNotification = self::sendNotification($pushData, 'wooAbandonedCart');
                        if ($sendAbandonedCartNotification) {
                            set_transient(self::$optionName."_sent_abandoned_cart_notification_".hash('crc32', $subscribedDevice['endpoint'], false), 'yes', daftplugInstantify::getSetting('pwaPushWooAbandonedCartInterval') * 3599);
                        }
                    }
                }
            }
        }

        public function doBpMemberMentionPush($activity, $subject, $message, $content, $receiverUserId) {
            $currentUser = get_userdata($activity->user_id);
            $friendDetail = get_userdata($receiverUserId);

            if ($activity->type == 'activity_comment') {
                $body = sprintf(__('%s has just mentioned you in a comment.', self::$textDomain), $currentUser->display_name);
            } else {
                $body = sprintf(__('%s has just mentioned you in an update.', self::$textDomain), $currentUser->display_name);
            }
    
            $pushData = array(
                'title' => sprintf(__('New mention from %s', self::$textDomain), $currentUser->display_name),
                'body' => $body,
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => get_permalink(get_option('bp-pages')['members']).$friendDetail->user_nicename.'/activity/mentions/',
                ),
            );

            self::sendNotification($pushData, 'private', $receiverUserId);
        }

        public function doBpMemberCommentPush($activity, $commentId, $commenterId, $params) {
            $currentUser = get_userdata($commenterId);
            $receiverDetail = get_userdata($activity->user_id);
            $pushData = array(
                'title' => sprintf(__('New comment from %s', self::$textDomain), $currentUser->display_name),
                'body' => sprintf(__('%s has just commented on your post.', self::$textDomain), $currentUser->display_name),
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => get_permalink(get_option('bp-pages')['members']).$receiverDetail->user_nicename.'/activity/'.$activity->id.'/#acomment-'.$commentId,
                ),
            );

            self::sendNotification($pushData, 'private', $activity->user_id);
        }

        public function doBpMemberReplyPush($activity, $commentId, $commenterId, $params) {
            $currentUser = get_userdata($commenterId);
            $pushData = array(
                'title' => sprintf(__('New reply from %s', self::$textDomain), $currentUser->display_name),
                'body' => sprintf(__('%s has just replied you.', self::$textDomain), $currentUser->display_name),
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => get_permalink(get_option('bp-pages')['activity']).'p/'.$activity->item_id.'/#acomment-'.$commenterId,
                ),
            );

            self::sendNotification($pushData, 'private', $activity->user_id);
        }

        public function doBpNewMessagePush($params) {
            $senderDetail = get_userdata($params->sender_id);
            foreach ($params->recipients as $r) {
                $recipientDetail = get_userdata($r->user_id);
                $pushData = array(
                    'title' => sprintf(__('New message from %s', self::$textDomain), $senderDetail->display_name),
                    'body' => substr(strip_tags($params->message), 0, 77).'...',
                    'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                    'data' => array(
                        'url' => get_permalink(get_option('bp-pages')['members']).$recipientDetail->user_nicename.'/messages/view/'.$params->thread_id,
                    ),
                );
                
                self::sendNotification($pushData, 'private', $r->user_id);
            }
        }

        public function doBpFriendRequestPush($id, $userId, $friendId, $friendship) {
            $friendDetail = get_userdata($friendId);
            $currentUser = get_userdata($userId);
            $pushData = array(
                'title' => sprintf(__('New friend request from %s', self::$textDomain), $currentUser->display_name),
                'body' => sprintf(__('%s has just sent you a friend request.', self::$textDomain), $currentUser->display_name),
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => get_permalink(get_option('bp-pages')['members']).$friendDetail->user_nicename.'/friends/requests/?new',
                ),
            );
            
            self::sendNotification($pushData, 'private', $friendId);
        }

        public function doBpFriendAcceptedPush($id, $userId, $friendId, $friendship) {
            $friendDetail = get_userdata($userId);
            $currentUser = get_userdata($friendId);
            $pushData = array(
                'title' => sprintf(__('%s accepted your friend request', self::$textDomain), $currentUser->display_name),
                'body' => sprintf(__('%s has just accepted your friend request.', self::$textDomain), $currentUser->display_name),
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => get_permalink(get_option('bp-pages')['members']).$friendDetail->user_nicename.'/friends',
                ),
            );
            
            self::sendNotification($pushData, 'private', $userId);
        }

        public function doPeepsoNotification($notification) {
            self::$noteData = $notification;
            if (self::$noteData['not_external_id'] > 0) {
                self::$noteData['post_title'] = get_the_title(self::$noteData['not_external_id']);
            }
            $PeepSoUser = PeepSoUser::get_instance(self::$noteData['not_from_user_id']);
            $notificationArgs = self::peepsoNotificationLink(false);
            $pushData = array(
                'title' => sprintf(__('New Notification From %s', self::$textDomain), $PeepSoUser->get_firstname()),
                'body' => strip_tags($PeepSoUser->get_firstname().' '.self::$noteData['not_message'].$notificationArgs['message']),
                'icon' => esc_url_raw(wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0]),
                'data' => array(
                    'url' => $notificationArgs['link'],
                ),
            );

            self::sendNotification($pushData, 'private', self::$noteData['not_user_id']);

            return $notification;
        }

        private static function peepsoNotificationLink($echo = true) {
            $link = PeepSo::get_page('activity_status') . self::$noteData['post_title'] . '/';
            $link = apply_filters('peepso_profile_notification_link', $link, self::$noteData);
            $isComment = 0;
            if ('user_comment' === self::$noteData['not_type']) {
                $isComment = 1;
            }
            if ('like_post' == self::$noteData['not_type']) {
                global $wpdb;
                $sql = 'SELECT COUNT(id) as `is_comment_like` FROM `' . $wpdb->prefix . 'posts` WHERE `post_type`=\'peepso-comment\' AND ID=' . self::$noteData['not_external_id'];
                $res = $wpdb->get_row($sql);
                $isComment = $res->is_comment_like;
            }
            $printLink = '';
            $activityType = array(
                'type' => 'post',
                'text' => __('post', 'peepso-core')
            );
            if ('stream_reply_comment' === self::$noteData['not_type']) {
                $activities = PeepSoActivity::get_instance();
                $notActivity = $activities->get_activity_data(self::$noteData['not_external_id'], self::$noteData['not_module_id']);
                $commentActivity = $activities->get_activity_data($notActivity->act_comment_object_id, $notActivity->act_comment_module_id);
                $postActivity = $activities->get_activity_data($commentActivity->act_comment_object_id, $commentActivity->act_comment_module_id);
    
                if (is_object($commentActivity) && is_object($postActivity)) {
                    $parentComment = $activities->get_activity_post($commentActivity->act_id);
                    $parentPost = $activities->get_activity_post($postActivity->act_id);
                    $parentId = $parentComment->act_external_id;
                    $postLink = PeepSo::get_page('activity_status') . $parentPost->post_title . '/';
                    $commentLink = $postLink . '?t=' . time() . '#comment.' . $postActivity->act_id . '.' . $parentComment->ID . '.' . $commentActivity->act_id . '.' . $notActivity->act_external_id;
                    if (0 === intval($echo)) {
                        $hyperlink = $commentLink;
                    }
                    ob_start();
                    echo ' ';
                    $postContent = __('a comment', 'peepso-core');
                    if (intval($parentComment->post_author) === get_current_user_id()) {
                        $postContent = (self::$noteData['not_message'] != __('replied to', 'peepso-core')) ? __('on ', 'peepso-core') : '';
                        $postContent .= __('your comment', 'peepso-core');
                    }
                    echo $postContent;
                    $printLink = ob_get_clean();
                }
            } elseif('profile_like' === self::$noteData['not_type']) {
                $author = PeepSoUser::get_instance(self::$noteData['not_from_user_id']);
                $link = $author->get_profileurl();
                if (0 === intval($echo)) {
                    $hyperlink = $link;
                }
            } elseif (1 == $isComment) {
                $activities = PeepSoActivity::get_instance();
                $notActivity = $activities->get_activity_data(self::$noteData['not_external_id'], self::$noteData['not_module_id']);
                $parentActivity = $activities->get_activity_data($notActivity->act_comment_object_id, $notActivity->act_comment_module_id);
                if (is_object($parentActivity)) {
                    $notPost = $activities->get_activity_post($notActivity->act_id);
                    $parentPost = $activities->get_activity_post($parentActivity->act_id);
                    $parentId = $parentPost->act_external_id;
                    $activityType = apply_filters('peepso_notifications_activity_type', $activityType, $parentId, NULL);
                    if ($parentPost->post_type == 'peepso-comment') {
                        $commentActivity = $activities->get_activity_data($notActivity->act_comment_object_id, $notActivity->act_comment_module_id);
                        $postActivity = $activities->get_activity_data($commentActivity->act_comment_object_id, $commentActivity->act_comment_module_id);
                        $parentPost = $activities->get_activity_post($postActivity->act_id);
                        $parentComment = $activities->get_activity_post($commentActivity->act_id);
                        $parentLink = PeepSo::get_page('activity_status') . $parentPost->post_title . '/?t=' . time() . '#comment.' . $postActivity->act_id . '.' . $parentComment->ID . '.' . $commentActivity->act_id . '.' . $notActivity->act_external_id;
                    } else {
                        $parentLink = PeepSo::get_page('activity_status') . $parentPost->post_title . '/#comment.' . $parentActivity->act_id . '.' . $notPost->ID . '.' . $notActivity->act_external_id;
                    }
                    if (0 === intval($echo)) {
                        $hyperlink = $parentLink;
                    }
                    ob_start();
                    $postContent = '';
                    $on = '';
                    if ($activityType['type'] == 'post') {
                        $on = ' ' . __('on', 'peepso-core');
                        $postContent = sprintf(__('a %s', 'peepso-core') , $activityType['text']);
                    }
                    if (intval($parentPost->post_author) === get_current_user_id() || (intval($parentPost->post_author) === get_current_user_id() && in_array($activityType['type'], array('cover', 'avatar')))) {
                        $on = ' ' . __('on', 'peepso-core');
                        $postContent = sprintf(__('your %s', 'peepso-core') , $activityType['text']);
                    }
                    if (in_array($activityType['type'], array('cover', 'avatar')) && (intval($parentPost->post_author) !== get_current_user_id())) {
                        $on = ' ' . __('on', 'peepso-core');
                        if (preg_match('/^[aeiou]/i', strtolower($activityType['text']))) {
                            $postContent = sprintf(__('an %s', 'peepso-core') , $activityType['text']);
                        } else {
                            $postContent = sprintf(__('a %s', 'peepso-core') , $activityType['text']);
                        }
                    }
                    echo $on, ' ';
                    echo $postContent;
                    $printLink = ob_get_clean();
                }
            } else {
                if (0 === intval($echo)) {
                    $hyperlink = $link;
                }
                if ('share' === self::$noteData['not_type']) {
                    $activities = PeepSoActivity::get_instance();
                    $repost = $activities->get_activity_data(self::$noteData['not_external_id'], self::$noteData['not_module_id']);
                    $originalPost = $activities->get_activity_post($repost->act_repost_id);
                    $activityType = apply_filters('peepso_notifications_activity_type', $activityType, $originalPost->ID, NULL);
                    ob_start();
                    echo ' ', sprintf(__('your %s', 'peepso-core') , $activityType['text']);
                    $printLink = ob_get_clean();
                }
            }
    
            $printLink = apply_filters('peepso_modify_link_item_notification', array(
                $printLink,
                $link
            ) , self::$noteData);
    
            if (is_array($printLink)) {
                return array('message' => $printLink[0], 'link' => $hyperlink);
            } else {
                return array('message' => $printLink, 'link' => $hyperlink);
            }
        }

        public function doUmCommentReplyPush($comment_ID, $status) {
            $vars = array();
            if ($status == 1) {
                $comment = get_comment($comment_ID);
                $parent = $comment->comment_parent;
                if ($parent) {
                    $parentc = get_comment($parent);
                    $author = $parentc->user_id;
                    if ($author == $comment->user_id) return;
                    if ($comment->user_id == 0 || $author == 0) return;
                    $pushData = array(
                        'title' => sprintf(__('%s has replied to one of your comments', self::$textDomain), um_user('display_name')),
                        'body' => sprintf(__('Comment: %s', self::$textDomain), get_comment_excerpt($comment->comment_ID)),
                        'icon' => um_get_avatar_url(get_avatar($comment->user_id, 40)),
                        'data' => array(
                            'url' => get_comment_link($comment->comment_ID),
                        ),
                    );
                    if (daftplugInstantify::getSetting('pwaPushUmCommentReply') == 'on') {
                        self::sendNotification($pushData, 'private', $author);
                    }
                } else {
                    $post = get_post($comment->comment_post_ID);
                    $author = $post->post_author;
                    $pushData = array(
                        'body' => sprintf(__('Comment: %s', self::$textDomain), get_comment_excerpt($comment->comment_ID)),
                        'data' => array(
                            'url' => get_comment_link($comment->comment_ID),
                        ),
                    );
                    if ($comment->user_id == $author && is_user_logged_in()) return;
                    if ($comment->user_id > 0) {
                        um_fetch_user($comment->user_id);
                        $pushData['title'] = sprintf(__('%s has commented on your post', self::$textDomain), um_user('display_name'));
                        $pushData['icon'] = um_get_avatar_url(get_avatar($comment->user_id, 40));
                        if (daftplugInstantify::getSetting('pwaPushUmNewComment') == 'on') {
                            self::sendNotification($pushData, 'private', $author);
                        }
                    } else {
                        $pushData['title'] = __('A guest has commented on your post', self::$textDomain);
                        $pushData['icon'] = '';
                        if (daftplugInstantify::getSetting('pwaPushUmGuestComment') == 'on') {
                            self::sendNotification($pushData, 'private', $author);
                        }
                    }
                }
            }
        }

        public function doUmProfileViewsPush($args) {
            if (!um_is_core_page('user')) {
                return;
            }
            global $post;
            if (is_user_logged_in() && get_current_user_id() != um_profile_id()) {
                um_fetch_user(get_current_user_id());
                $pushData = array(
                    'title' => sprintf(__('%s has viewed your profile', self::$textDomain), um_user('display_name')),
                    'body' => sprintf(__('%s has viewed your profile', self::$textDomain), um_user('display_name')),
                    'icon' => um_get_avatar_url(get_avatar(get_current_user_id(), 40)),
                    'data' => array(
                        'url' => um_user_profile_url(),
                    ),
                );
                um_fetch_user(um_profile_id());
                if (daftplugInstantify::getSetting('pwaPushUmProfileView') == 'on') {
                    self::sendNotification($pushData, 'private', um_profile_id());
                }
            }
        
            if (!is_user_logged_in() && isset($post->ID)) {
                if (UM()->access()->is_restricted($post->ID)) {
                    return;
                }
                $pushData = array(
                    'title' => __('A guest has viewed your profile.', self::$textDomain),
                    'body' => __('A guest has viewed your profile.', self::$textDomain),
                    'icon' => um_get_avatar_url(get_avatar('123456789', 40)),
                    'data' => array(
                        'url' => '',
                    ),
                );
                um_fetch_user(um_profile_id());
                if (daftplugInstantify::getSetting('pwaPushUmGuestProfileView') == 'on') {
                    self::sendNotification($pushData, 'private', um_profile_id());
                }
            }
        }
        
        public function doUmPrivateMessagePush($to, $from, $conversation_id, $message_data = array()) {
            um_fetch_user($from);

            $pushData = array(
                'title' => __('New Message', self::$textDomain),
                'body' => sprintf(__('%s has just sent you a private message.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($from, 40)),
            );
        
            um_fetch_user($to);
        
            $notification_uri = add_query_arg('profiletab', 'messages', um_user_profile_url());
            $notification_uri = add_query_arg('conversation_id', $conversation_id, $notification_uri);
            $pushData['data']['url'] = $notification_uri;

            self::sendNotification($pushData, 'private', $to);
        }

        public function doUmNewFollowPush($user_id1, $user_id2) {
            um_fetch_user($user_id2);
            $pushData = array(
                'title' => __('New Follow', self::$textDomain),
                'body' => sprintf(__('%s has just followed you!', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($user_id2, 40)),
                'data' => array(
                    'url' => um_user_profile_url(),
                ),
            );
            um_fetch_user($user_id1);

            self::sendNotification($pushData, 'private', $user_id1);
        }

        public function doUmFriendRequestPush($user_id1, $user_id2) {
            um_fetch_user($user_id2);
            $pushData = array(
                'title' => __('New Friend Request', self::$textDomain),
                'body' => sprintf(__('%s has sent you a friendship request.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($user_id2, 40)),
                'data' => array(
                    'url' => um_user_profile_url(),
                ),
            );
            um_fetch_user($user_id1);

            self::sendNotification($pushData, 'private', $user_id1);
        }

        public function doUmFriendRequestAcceptedPush($user_id1, $user_id2) {
            um_fetch_user($user_id2);
            $pushData = array(
                'title' => __('Friend Request Accepted', self::$textDomain),
                'body' => sprintf(__('%s has accepted your friendship request.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($user_id2, 40)),
                'data' => array(
                    'url' => um_user_profile_url(),
                ),
            );
            um_fetch_user($user_id1);

            self::sendNotification($pushData, 'private', $user_id1);
        }

        public function doUmWallPostPush($post_id, $writer, $wall) {
            if ($writer == $wall) return false;
            um_fetch_user($writer);
            $pushData = array(
                'title' => __('New Wall Post', self::$textDomain),
                'body' => sprintf(__('%s has posted on your wall.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($writer, 80)),
            );
            um_fetch_user($wall);
            $pushData['data']['url'] = UM()->Activity_API()->api()->get_permalink($post_id);

            self::sendNotification($pushData, 'private', $wall);
        }

        public function doUmWallCommentPush($comment_id, $comment_parent, $post_id, $user_id) {
            if ($comment_parent > 0) return false;
            $author = UM()->Activity_API()->api()->get_author($post_id);
            if ($author == $user_id) return false;
            um_fetch_user($user_id);
            $pushData = array(
                'title' => __('New Wall Post Comment', self::$textDomain),
                'body' => sprintf(__('%s has commented on your wall post.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($user_id, 80)),
            );
            um_fetch_user($author);
            $pushData['data']['url'] = UM()->Activity_API()->api()->get_permalink($post_id);

            self::sendNotification($pushData, 'private', $author);
        }

        public function doUmPostLikePush($post_id, $user_id) {
            $author = UM()->Activity_API()->api()->get_author($post_id);
            if ($author == $user_id) return false;
            um_fetch_user($user_id);
            $pushData = array(
                'title' => __('New Wall Post Like', self::$textDomain),
                'body' => sprintf(__('%s likes your wall post.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($user_id, 80)),
            );
            um_fetch_user($author);
            $pushData['data']['url'] = UM()->Activity_API()->api()->get_permalink($post_id);
        
            self::sendNotification($pushData, 'private', $author);
        }

        public function doUmNewMentionPush($user_id1, $user_id2, $post_id) {
            um_fetch_user($user_id1);
            $pushData = array(
                'title' => __('New Mention', self::$textDomain),
                'body' => sprintf(__('%s has just mentioned you.', self::$textDomain), um_user('display_name')),
                'icon' => um_get_avatar_url(get_avatar($user_id1, 80)),
                'data' => array(
                    'url' => UM()->Activity_API()->api()->get_permalink($post_id),
                ),
            );
        
            self::sendNotification($pushData, 'private', $user_id2);
        }

        public function doUmGroupApprovePush($user_id, $group_id, $invited_by_user_id, $group_role, $new_group) {
            if ($new_group) {
                return;
            }
            um_fetch_user($user_id);
            $pushData = array(
                'title' => __('Group Join Request Approved', self::$textDomain),
                'body' => sprintf(__('Your request to join %s have been approved.', self::$textDomain), ucwords(get_the_title($group_id))),
                'icon' => UM()->Groups()->api()->get_group_image($group_id, 'default', 50, 50, true),
                'data' => array(
                    'url' => UM()->Activity_API()->api()->get_permalink($post_id),
                ),
            );
        
            self::sendNotification($pushData, 'private', $user_id);
            um_reset_user();
        }

        public function doUmGroupJoinRequestPush($user_id, $group_id, $invited_by_user_id) {        
            if ($user_id == $invited_by_user_id) {
                um_fetch_user($user_id);
                $pushData = array(
                    'title' => __('New Group Join Request', self::$textDomain),
                    'body' => sprintf(__('%s has requested to join %s.', self::$textDomain), um_user('display_name'), ucwords(get_the_title($group_id))),
                    'icon' => um_get_avatar_url(get_avatar($user_id, 40)),
                    'data' => array(
                        'url' => get_the_permalink($group_id).'?tab=requests',
                    ),
                );
        
                $moderators = UM()->Groups()->member()->get_moderators($group_id);
                foreach($moderators as $key => $mod) {
                    um_fetch_user($mod->uid);
                    self::sendNotification($pushData, 'private', $mod->uid);
                }
                um_reset_user();
            }
        }

        public function doUmGroupInvitationPush($invited_user_id, $group_id, $invited_by_user_id) {       
            um_fetch_user($invited_by_user_id);
            $pushData = array(
                'title' => __('New Group Join Invitation', self::$textDomain),
                'body' => sprintf(__('%s has invited you to join %s.', self::$textDomain), um_user('display_name'), ucwords(get_the_title($group_id))),
                'icon' => um_get_avatar_url(get_avatar($invited_by_user_id, 40)),
                'data' => array(
                    'url' => get_the_permalink($group_id),
                ),
            );

            self::sendNotification($pushData, 'private', $invited_user_id);
            um_reset_user();
        }

        public function doUmGroupRoleChangePush($user_id, $group_id, $new_role, $old_role) {
            um_fetch_user($user_id);
            $group_member_roles = UM()->Groups()->api()->get_member_roles();
            $pushData = array(
                'title' => __('Group Role Changed', self::$textDomain),
                'body' => sprintf(__('Your group role %s has been changed to %s in %s.', self::$textDomain), $group_member_roles[$new_role], $group_member_roles[$old_role], ucwords(get_the_title($group_id))),
                'icon' => UM()->Groups()->api()->get_group_image($group_id, 'default', 50, 50, true),
                'data' => array(
                    'url' => get_the_permalink($group_id),
                ),
            );

            self::sendNotification($pushData, 'private', $user_id);
            um_reset_user();
        }

        public function doUmGroupPostPush($post_id, $user_id, $wall_id) {
            $key = 'groups_new_post';
            if (!UM()->options()->get("log_$key")) {
                return;
            }
        
            global $wpdb;
            $table_name = UM()->Groups()->setup()->db_groups_table;
            $group_id = get_post_meta($post_id, '_group_id', true);
            $members = $wpdb->get_col("SELECT `user_id1` FROM $table_name WHERE `group_id` = $group_id AND `status` = 'approved'");
        
            foreach ($members as $i => $member_id) {
                if ($user_id == $member_id) {
                    unset($members[$i]);
                    continue;
                }
                $prefs = get_user_meta($user_id, '_notifications_prefs', true);
                if (isset($prefs[$key]) && !$prefs[$key]) {
                    unset($members[$i]);
                    continue;
                }
            }

            if (empty($members)) {
                return;
            }
        
            um_fetch_user($user_id);
            $pushData = array(
                'title' => __('New Group Post', self::$textDomain),
                'body' => sprintf(__('%s has just posted on the group %s.', self::$textDomain), um_user('display_name'), ucwords(get_the_title($group_id))),
                'icon' => um_get_avatar_url(get_avatar($user_id, 40)),
                'data' => array(
                    'url' => UM()->Groups()->discussion()->get_permalink($post_id),
                ),
            );
        
            foreach ($members as $member_id) {
                self::sendNotification($pushData, 'private', $member_id);
            }
    
            um_reset_user();
        }

        public function doUmGroupCommentPush($commentid, $comment_parent, $post_id, $user_id) {
            $key = 'groups_new_comment';
        
            if (!UM()->options()->get( "log_$key")) {
                return;
            }
        
            global $wpdb;
            $table_name = UM()->Groups()->setup()->db_groups_table;
            $group_id = get_post_meta( $post_id, '_group_id', true );
            $members = $wpdb->get_col("SELECT `user_id1` FROM $table_name WHERE `group_id` = $group_id AND `status` = 'approved'");
        
            foreach ($members as $i => $member_id) {
                if ($user_id == $member_id) {
                    unset( $members[$i] );
                    continue;
                }
                $prefs = get_user_meta( $user_id, '_notifications_prefs', true);
                if (isset($prefs[$key]) && !$prefs[$key]) {
                    unset($members[$i]);
                    continue;
                }
            }
            if (empty( $members)) {
                return;
            }
        
            um_fetch_user($user_id);
            $pushData = array(
                'title' => __('New Group Post Comment', self::$textDomain),
                'body' => sprintf(__('%s has just commented on post in %s.', self::$textDomain), um_user('display_name'), ucwords(get_the_title($group_id))),
                'icon' => um_get_avatar_url(get_avatar($user_id, 40)),
                'data' => array(
                    'url' => UM()->Groups()->discussion()->get_comment_link($post_url, $commentid),
                ),
            );
        
            foreach ($members as $member_id) {
                self::sendNotification($pushData, 'private', $member_id);
            }
    
            um_reset_user();
        }
        
        public function sendScheduledNotification($notificationData) {
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

            $pushData['segment'] = $notificationData['pushSegment'];

            return self::sendNotification($pushData, 'scheduled');
        }

        public function addPushToServiceWorker($serviceWorker) {
            $serviceWorker .= "self.addEventListener('push', (event) => {
                                    if (event.data) {
                                        const pushData = event.data.json();
                                        event.waitUntil(self.registration.showNotification(pushData.title, pushData));
                                        navigator.setAppBadge(1).catch((error) => {
                                            console.log('Error setting App badge');
                                        });
                                        console.log(pushData);
                                    } else {
                                        console.log('No push data fetched');
                                    }
                                });
                                
                                self.addEventListener('notificationclick', (event) => {
                                    event.notification.close();
                                    switch (event.action) {
                                        case 'action1':
                                            event.waitUntil(clients.openWindow(event.notification.data.pushActionButton1Url));
                                        break;
                                        case 'action2':
                                            event.waitUntil(clients.openWindow(event.notification.data.pushActionButton2Url));
                                        break;
                                        default:
                                            event.waitUntil(clients.openWindow(event.notification.data.url));
                                        break;
                                    }
                                    navigator.clearAppBadge().catch((error) => {
                                        console.log('Error clearing App badge');
                                    });
                                });\n";

            return $serviceWorker;
        }

        public function addPushJsVars($vars) {
            $vars['pwaPublicKey'] = self::$vapidKeys['pwaPublicKey'];
            $vars['pwaSubscribeOnMsg'] = esc_html__('Notifications are turned on', self::$textDomain);
            $vars['pwaSubscribeOffMsg'] = esc_html__('Notifications are turned off', self::$textDomain);

            return $vars;
        }

        public function renderPushPrompt() {
            if (daftplugInstantify::isAmpPage() || !daftplugInstantifyPwa::isPwaAvailable()) {
                return;
            }
            
            include_once($this->daftplugInstantifyPwaPublic->partials['pushPrompt']);
        }

        public function renderPushButton() {
            if (daftplugInstantify::isAmpPage() || !daftplugInstantifyPwa::isPwaAvailable()) {
                return;
            }
            
            include_once($this->daftplugInstantifyPwaPublic->partials['pushButton']);
        }

        public static function sendNotification($pushData, $type, $targetUserId = null) {
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

            switch ($type) {
                case 'wooNewOrder':
                    $subscriptions = array();
                    foreach (self::$subscribedDevices as $subscribedDevice) {
                        if (in_array(daftplugInstantify::getSetting('pwaPushWooNewOrderRole'), $subscribedDevice['roles'])) {
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
                case 'wooLowStock':
                    $subscriptions = array();
                    foreach (self::$subscribedDevices as $subscribedDevice) {
                        if (in_array(daftplugInstantify::getSetting('pwaPushWooLowStockRole'), $subscribedDevice['roles'])) {
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
                case 'wooAbandonedCart':
                    $subscription = array(
                        'subscription' => Subscription::create(
                            array(
                                'endpoint' => self::$subscribedDevices[$pushData['segment']]['endpoint'],
                                'publicKey' => self::$subscribedDevices[$pushData['segment']]['userKey'],
                                'authToken' => self::$subscribedDevices[$pushData['segment']]['userAuth'],
                            )
                        ),
                        'payload' => null
                    );

                    $webPush->queueNotification(
                        $subscription['subscription'],
                        json_encode($pushData)
                    );
                    break;
                case 'private':
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
                    break;
                case 'scheduled':
                    switch ($pushData['segment']) {
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
                        case substr($pushData['segment'], 0, 7) === 'Country':
                            $country = str_replace('Country - ', '', $pushData['segment']);
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
                                        'endpoint' => self::$subscribedDevices[$pushData['segment']]['endpoint'],
                                        'publicKey' => self::$subscribedDevices[$pushData['segment']]['userKey'],
                                        'authToken' => self::$subscribedDevices[$pushData['segment']]['userAuth'],
                                    )
                                ),
                                'payload' => null
                            );
        
                            $webPush->queueNotification(
                                $subscription['subscription'],
                                json_encode($pushData)
                            );
                    }
                    break;   
                default:
                    echo 'Undefined Push Type.';
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
    }
}