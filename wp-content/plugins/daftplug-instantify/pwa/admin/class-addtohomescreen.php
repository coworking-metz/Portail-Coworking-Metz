<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyPwaAdminAddtohomescreen')) {
    class daftplugInstantifyPwaAdminAddtohomescreen {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;
        public $pluginUploadDir;

        public $settings;

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

            $this->settings = $config['settings'];

            add_action("wp_ajax_{$this->optionName}_generate_launch_screens", array($this, 'generateLaunchScreens'));
    	}

		public function generateLaunchScreens() {
			if (isset($this->settings['pwaIcon'])) {
				if (isset($_POST["launchScreen"]) && !empty($_POST["launchScreen"])) {
	            	$img = substr($_POST['launchScreen'], strpos($_POST['launchScreen'], ",") + 1);
	            	$decoded = base64_decode($img);
	            	$fileName = $this->pluginUploadDir . 'img-pwa-apple-launch.png';
	            	$fileType = 'image/png';

	            	$generateFile = daftplugInstantifyPwa::putContent($fileName, $decoded);

                	if (file_exists($fileName)) {
		            	$image = wp_get_image_editor($fileName);
                    	if (!is_wp_error($image)) {
        	            	$sizesArray = array(
								array ('width' => 640, 'height' => 1136, 'crop' => true),
								array ('width' => 750, 'height' => 1334, 'crop' => true),
								array ('width' => 828, 'height' => 1792, 'crop' => true),
								array ('width' => 1125, 'height' => 2436, 'crop' => true),
								array ('width' => 1170, 'height' => 2532, 'crop' => true),
								array ('width' => 1242, 'height' => 2208, 'crop' => true),
								array ('width' => 1242, 'height' => 2688, 'crop' => true),
								array ('width' => 1284, 'height' => 2778, 'crop' => true),        	            		
        	            		array ('width' => 1536, 'height' => 2048, 'crop' => true),
        	                	array ('width' => 1668, 'height' => 2224, 'crop' => true),
        	                	array ('width' => 2048, 'height' => 2732, 'crop' => true),
        	            	);
         
        	            	$image->multi_resize($sizesArray);
                        	$image->save();

                        	wp_die('1');
                    	}
                	}
				}
			} else {
				wp_die('0');
			}
		}
    }
}