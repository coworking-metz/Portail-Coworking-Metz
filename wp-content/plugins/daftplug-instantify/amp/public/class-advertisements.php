<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyAmpPublicAdvertisements')) {
    class daftplugInstantifyAmpPublicAdvertisements {
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

            if (daftplugInstantify::getSetting('ampAdSenseAutoAds') == 'on') {
                add_filter("{$this->optionName}_public_html", array($this, 'injectAutoAds'));
            }

            if (daftplugInstantify::getSetting('ampAdAboveContent') == 'on') {
                add_filter('the_content', array($this, 'injectAdAboveContent'));
            }

            if (daftplugInstantify::getSetting('ampAdInsideContent') == 'on') {
                add_filter('the_content', array($this, 'injectAdInsideContent'));
            }

            if (daftplugInstantify::getSetting('ampAdBelowContent') == 'on') {
                add_filter('the_content', array($this, 'injectAdBelowContent'));
            }
        }

        public function injectAutoAds() {
            $dataAdClient = daftplugInstantify::getSetting('ampAdSenseAutoAdsClient');
            $adCode = '<amp-auto-ads type="adsense" data-ad-client="'.$dataAdClient.'"></amp-auto-ads>';

            if (daftplugInstantify::isAmpPage()) {
                return $adCode;
            } else {
                return false;
            }
        }

        public function injectAdAboveContent($content) {
            $dataAdClient = daftplugInstantify::getSetting('ampAdAboveContentClient');
            $dataAdSlot = daftplugInstantify::getSetting('ampAdAboveContentSlot');
            $dataAdSize = explode('x', daftplugInstantify::getSetting('ampAdAboveContentSize'));

            if (daftplugInstantify::getSetting('ampAdAboveContentSize') == 'responsive') {
                $ad = '<amp-ad
                         width="100vw"
                         height="320"
                         type="adsense"
                         data-ad-client="'.$dataAdClient.'"
                         data-ad-slot="'.$dataAdSlot.'"
                         data-auto-format="rspv"
                         data-full-width>
                           <div overflow></div>
                       </amp-ad>';
            } else {
                $ad = '<amp-ad
                         width="'.$dataAdSize[0].'"
                         height="'.$dataAdSize[1].'"
                         type="adsense"
                         data-ad-client="'.$dataAdSlot.'"
                         data-ad-slot="1234567890">
                           <div overflow></div>
                       </amp-ad>';
            }

            if (daftplugInstantify::isAmpPage()) {
                return $ad . $content;
            } else {
                return $content;
            }
        }

        public function injectAdInsideContent($content) {
            $dataAdClient = daftplugInstantify::getSetting('ampAdInsideContentClient');
            $dataAdSlot = daftplugInstantify::getSetting('ampAdInsideContentSlot');
            $dataAdSize = explode('x', daftplugInstantify::getSetting('ampAdInsideContentSize'));

            if (daftplugInstantify::getSetting('ampAdInsideContentSize') == 'responsive') {
                $ad = '<amp-ad 
                         width="100vw"
                         height="320"
                         type="adsense"
                         data-ad-client="'.$dataAdClient.'"
                         data-ad-slot="'.$dataAdSlot.'"
                         data-auto-format="rspv"
                         data-full-width>
                           <div overflow></div>
                       </amp-ad>';
            } else {
                $ad = '<amp-ad
                         width="'.$dataAdSize[0].'"
                         height="'.$dataAdSize[1].'"
                         type="adsense"
                         data-ad-client="'.$dataAdSlot.'"
                         data-ad-slot="1234567890">
                           <div overflow></div>
                       </amp-ad>';
            }

            if (daftplugInstantify::isAmpPage()) {
                return $this->insertAfterParagraph($ad, 2, $content);
            } else {
                return $content;
            }
        }

        public function injectAdBelowContent($content) {
            $dataAdClient = daftplugInstantify::getSetting('ampAdBelowContentClient');
            $dataAdSlot = daftplugInstantify::getSetting('ampAdBelowContentSlot');
            $dataAdSize = explode('x', daftplugInstantify::getSetting('ampAdBelowContentSize'));

            if (daftplugInstantify::getSetting('ampAdBelowContentSize') == 'responsive') {
                $ad = '<amp-ad 
                         width="100vw" 
                         height="320"
                         type="adsense"
                         data-ad-client="'.$dataAdClient.'"
                         data-ad-slot="'.$dataAdSlot.'"
                         data-auto-format="rspv"
                         data-full-width>
                           <div overflow></div>
                       </amp-ad>';
            } else {
                $ad = '<amp-ad 
                         width="'.$dataAdSize[0].'"
                         height="'.$dataAdSize[1].'"
                         type="adsense"
                         data-ad-client="'.$dataAdSlot.'"
                         data-ad-slot="1234567890">
                           <div overflow></div>
                       </amp-ad>';
            }

            if (daftplugInstantify::isAmpPage()) {
                return $content . $ad;
            } else {
                return $content;
            }
        }

        // Helpers
        public function insertAfterParagraph($insertion, $paragraphId, $content) {
            $closingP = '</p>';
            $paragraphs = explode($closingP, $content);

            foreach ($paragraphs as $index => $paragraph) {
                if (trim($paragraph)) {
                    $paragraphs[$index] .= $closingP;
                }

                if ($paragraphId == $index + 1) {
                    $paragraphs[$index] .= $insertion;
                }
            }
            
            return implode('', $paragraphs);
        }
    }
}