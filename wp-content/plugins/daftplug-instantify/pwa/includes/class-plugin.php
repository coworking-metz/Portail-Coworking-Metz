<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyPwa')) {
    class daftplugInstantifyPwa {
        public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;
        public $pluginFile;
        public $pluginBasename;
        public $pluginUploadDir;
        public $pluginUploadUrl;

        public $daftplugInstantifyPwaPublic;
        public $daftplugInstantifyPwaPublicGeneral;
        public $daftplugInstantifyPwaPublicAddtohomescreen;
        public $daftplugInstantifyPwaPublicOfflineusage;
        public $daftplugInstantifyPwaPublicAccessibility;
        public $daftplugInstantifyPwaPublicEnhancements;
        public $daftplugInstantifyPwaPublicPushnotifications;

        public $daftplugInstantifyPwaAdmin;
        public $daftplugInstantifyPwaAdminGeneral;
        public $daftplugInstantifyPwaAdminAddtohomescreen;
        public $daftplugInstantifyPwaAdminOfflineusage;
        public $daftplugInstantifyPwaAdminAccessibility;
        public $daftplugInstantifyPwaAdminEnhancements;
        public $daftplugInstantifyPwaAdminPushnotifications;

        public function __construct($config) {
            $this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];
            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];
            $this->pluginUploadDir = $config['plugin_upload_dir'];
            $this->pluginUploadUrl = wp_upload_dir()['baseurl'] . '/' . trailingslashit($config['slug']);
        
            if (daftplugInstantify::isPublic()
            && ((in_array('desktop', (array)daftplugInstantify::getSetting('pwaPlatforms')) && daftplugInstantify::isPlatform('desktop'))
            || (in_array('mobile', (array)daftplugInstantify::getSetting('pwaPlatforms')) && daftplugInstantify::isPlatform('mobile'))
            || (in_array('tablet', (array)daftplugInstantify::getSetting('pwaPlatforms')) && daftplugInstantify::isPlatform('tablet')))) {
	            require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-public.php');
	            $this->daftplugInstantifyPwaPublic = new daftplugInstantifyPwaPublic($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-general.php');
	            $this->daftplugInstantifyPwaPublicGeneral = new daftplugInstantifyPwaPublicGeneral($config, $this->daftplugInstantifyPwaPublic);

	            require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-addtohomescreen.php');
	            $this->daftplugInstantifyPwaPublicAddtohomescreen = new daftplugInstantifyPwaPublicAddtohomescreen($config, $this->daftplugInstantifyPwaPublic);

	            require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-offlineusage.php');
	            $this->daftplugInstantifyPwaPublicOfflineusage = new daftplugInstantifyPwaPublicOfflineusage($config, $this->daftplugInstantifyPwaPublic);

	            require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-accessibility.php');
	            $this->daftplugInstantifyPwaPublicAccessibility = new daftplugInstantifyPwaPublicAccessibility($config, $this->daftplugInstantifyPwaPublic);

	            require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-enhancements.php');
	            $this->daftplugInstantifyPwaPublicEnhancements = new daftplugInstantifyPwaPublicEnhancements($config, $this->daftplugInstantifyPwaPublic);

	            if (!daftplugInstantify::isOnesignalActive() && !daftplugInstantify::isWebpushrActive()) {
	            	if (!version_compare(PHP_VERSION, '7.3', '<') && extension_loaded('mbstring') && extension_loaded('openssl')) {
		                require_once(plugin_dir_path(dirname(__FILE__)) . 'public/class-pushnotifications.php');
		                $this->daftplugInstantifyPwaPublicPushnotifications = new daftplugInstantifyPwaPublicPushnotifications($config, $this->daftplugInstantifyPwaPublic);
	            	}
	            }
            }

            if (daftplugInstantify::isAdmin()) {
                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-general.php');
                $this->daftplugInstantifyPwaAdminGeneral = new daftplugInstantifyPwaAdminGeneral($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-addtohomescreen.php');
                $this->daftplugInstantifyPwaAdminAddtohomescreen = new daftplugInstantifyPwaAdminAddtohomescreen($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-offlineusage.php');
                $this->daftplugInstantifyPwaAdminOfflineusage = new daftplugInstantifyPwaAdminOfflineusage($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-accessibility.php');
                $this->daftplugInstantifyPwaAdminAccessibility = new daftplugInstantifyPwaAdminAccessibility($config);

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-enhancements.php');
                $this->daftplugInstantifyPwaAdminEnhancements = new daftplugInstantifyPwaAdminEnhancements($config);

                if (!daftplugInstantify::isOnesignalActive() && !daftplugInstantify::isWebpushrActive() && !version_compare(PHP_VERSION, '7.3', '<') && extension_loaded('mbstring') && extension_loaded('openssl')) {
	                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-pushnotifications.php');
	                $this->daftplugInstantifyPwaAdminPushnotifications = new daftplugInstantifyPwaAdminPushnotifications($config);
                }

                require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php');
                $this->daftplugInstantifyPwaAdmin = new daftplugInstantifyPwaAdmin($config, $this->daftplugInstantifyPwaAdminGeneral, $this->daftplugInstantifyPwaAdminAddtohomescreen, $this->daftplugInstantifyPwaAdminOfflineusage, $this->daftplugInstantifyPwaAdminAccessibility,
                    $this->daftplugInstantifyPwaAdminEnhancements, $this->daftplugInstantifyPwaAdminPushnotifications);
            }

            if (daftplugInstantify::isWprocketActive()) {
                add_filter('rocket_exclude_js', array($this, 'rocketExcludeUaparser'));
            }

            if (daftplugInstantify::getSetting('pwaPasswordlessLogin') == 'on') {
                require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/libs/passwordless-login/passwordless-login.php');
            }
        }

        public function rocketExcludeUaparser($excludedJs) {
            $excludedJs[] = rocket_clean_exclude_file(plugins_url('pwa/public/assets/js/script-uaparser.js', $this->pluginFile));
    
            return $excludedJs;
        }

        public static function isPwaAvailable() {
            if (daftplugInstantify::getSetting('pwaOnAll') == 'off') {
                if (is_singular((array)daftplugInstantify::getSetting('pwaOnPostTypes'))) {
                    global $post;
                    if (get_post_meta($post->ID, 'pwa', true) == 'disable') {
                        return false;
                    } else {
                        return true;
                    }
                } else {           
                    foreach ((array)daftplugInstantify::getSetting('pwaOnPageTypes') as $PageType) {            
                        if (is_string($PageType) && substr($PageType, 0, 3) === 'is_' && call_user_func($PageType) == true) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                if (is_singular()) {
                    global $post;
                    if (get_post_meta($post->ID, 'pwa', true) == 'disable') {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }

        public static function putContent($file, $content = null) {
            if (is_file($file)) {
                unlink($file);
            }

            if (empty($file)) {
                return false;
            }

            global $wp_filesystem;
            
            if (empty($wp_filesystem)) {
                require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/file.php');
                WP_Filesystem();
            }

            if (!$wp_filesystem->put_contents($file, $content, 0644)) {
                return false;
            }

            return true;
        }

        public static function resizeImage($attachId, $width, $height, $ext, $crop = false) {
            if ('attachment' != get_post_type($attachId)) {
                return false;
            }

            $width  = intval($width);
            $height = intval($height);

            $srcImg = wp_get_attachment_image_src($attachId, 'full');

            list($oldWidth, $oldHeight) = getimagesize($srcImg[0]);

            $srcImgRatio = $oldWidth / $oldHeight;
            $srcImgPath = get_attached_file($attachId);

            if (!file_exists($srcImgPath)) {
                return false;
            }

            $srcImgInfo = pathinfo($srcImgPath);

            if ($crop) {
                $newWidth = $width;
                $newHeight = $height;
            } elseif ($width / $height <= $srcImgRatio) {
                $newWidth = $width;
                $newHeight = 1 / $srcImgRatio * $width;
            } else {
                $newWidth = $height * $srcImgRatio;
                $newHeight = $height;
            }

            $newWidth  = round($newWidth);
            $newHeight = round($newHeight);

            $changeFiletype = false;
            if ($ext && strtolower($srcImgInfo['extension']) != strtolower($ext)) {
                $changeFiletype = true;
            }

            if (($newWidth > $oldWidth || $newHeight > $oldHeight) && !$changeFiletype) {
                return $srcImg;
            }

            $extension = $srcImgInfo['extension'];
            if ($changeFiletype) {
                $extension = $ext;
            }

            $newImgPath = "{$srcImgInfo['dirname']}/{$srcImgInfo['filename']}-{$newWidth}x{$newHeight}.{$extension}";
            $newImgUrl = str_replace(trailingslashit(ABSPATH), trailingslashit(get_site_url()), $newImgPath);

            if (file_exists($newImgPath)) {
                return array(
                    $newImgUrl,
                    $newWidth,
                    $newHeight,
                );
            }

            $image = wp_get_image_editor($srcImgPath);
            if (!is_wp_error($image)) {
                $image->resize($width, $height, $crop);
                $image->save($newImgPath);

                return array(
                    $newImgUrl,
                    $newWidth,
                    $newHeight,
                );
            }

            return false;
        }
    }
}