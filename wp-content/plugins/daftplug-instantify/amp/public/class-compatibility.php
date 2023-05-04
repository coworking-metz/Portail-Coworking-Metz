<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpPublicCompatibility')) {
    class daftplugInstantifyAmpPublicCompatibility {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $settings;

        public $daftplugInstantifyAmpPublic;

    	public function __construct($config, $daftplugInstantifyAmpPublic) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->settings = $config['settings'];

            $this->daftplugInstantifyAmpPublic = $daftplugInstantifyAmpPublic;

            if (daftplugInstantify::getSetting('ampSidebarMenu') == 'on' && wp_is_mobile()) {
                add_filter("{$this->optionName}_public_html", array($this, 'injectSidebarMenu'));
            }

            if (!empty(daftplugInstantify::getSetting('ampCustomCss'))) {
                add_filter("{$this->optionName}_public_css", array($this, 'injectCustomCss'));
            }
        }

        public function injectSidebarMenu() {
            $bgColor = daftplugInstantify::getSetting('ampSidebarMenuBgColor');
            $position = daftplugInstantify::getSetting('ampSidebarMenuPosition');
            $reversePosition = $position == 'left' ? 'right' : 'left';

            if (daftplugInstantify::isAmpPage()) { ?>
                <div class="daftplugPublic" data-daftplug-plugin="<?php echo $this->optionName; ?>">
                    <amp-sidebar class="daftplugPublicAmpSidebar" id="sidebarmenu" layout="nodisplay" side="<?php echo $reversePosition; ?>" style="background-color: <?php echo $bgColor; ?>;">
                        <div class="daftplugPublicAmpSidebar_close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" on="tap:sidebarmenu.close"
                            role="button" tabindex="0" viewBox="0 0 21.97 21.97">
                                <title><?php esc_html_e('Close Sidebar', $this->textDomain); ?></title>
                                <path fill="none" stroke="#222" stroke-width="4.5" stroke-miterlimit="10" d="M1.25 20.72L20.72 1.25m-19.47 0l19.47 19.47" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <nav>
                            <?php
                                wp_nav_menu(array(
                                    'menu' => daftplugInstantify::getSetting('ampSidebarMenuId'),
                                    'menu_class' => 'daftplugPublicAmpSidebarMenu',
                                    'walker' => new daftplugInstantifyAmpPublicCompatibilityMenuWalker(),
                                ));
                            ?>
                        </nav>
                    </amp-sidebar>
                    <svg xmlns="http://www.w3.org/2000/svg"class="daftplugPublicAmpMenuToggle" on="tap:sidebarmenu.open" role="button" tabindex="0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" style="background: <?php echo $bgColor; ?>; <?php echo $position; ?>: 15px; border-radius: 50%;"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </div>
            <?php } else {
                return false;           
            }
        }

        public function injectCustomCss($css) {
            if (daftplugInstantify::isAmpPage()) {
                $css .= htmlspecialchars(wp_unslash(daftplugInstantify::getSetting('ampCustomCss')));
            }

            return $css;
        }
    }
}

if (!class_exists('daftplugInstantifyAmpPublicCompatibilityMenuWalker')) {
    class daftplugInstantifyAmpPublicCompatibilityMenuWalker extends Walker_Nav_Menu {
        static $count = 0;
        
        public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
            if ($depth == 0 and $args->walker->has_children) {
                $output .= '<li class="daftplugPublicAmpSidebarMenu_item -hasChildren"><amp-accordion disable-session-states><section><h3><a href="'.$item->url.'" role="button" aria-expanded="false" tabindex="0">'.$item->title.'</a></h3><ul>';
            } else {
                $output .= '<li class="daftplugPublicAmpSidebarMenu_item"><a href='.$item->url.'>'.$item->title.'</a>';
            }	
        }
        
        public function end_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
            if ($depth == 0 ) {
                $output .= '</section>';
            } else {
                $output .= '</li>';
            }	
        }
        
        public function end_lvl(&$output, $depth = 0, $args = array()) {
            if($args->walker->has_children) {
                $output .= '</amp-accordion></li>';
            }
            
            $output .= '</li>';
        }
    }
}