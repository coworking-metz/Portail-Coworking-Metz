<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyPwaPublicEnhancements')) {
    class daftplugInstantifyPwaPublicEnhancements {
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

        public $daftplugInstantifyPwaPublic;

    	public function __construct($config, $daftplugInstantifyPwaPublic) {
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

            $this->daftplugInstantifyPwaPublic = $daftplugInstantifyPwaPublic;

            if (daftplugInstantify::getSetting('pwaBackgroundSync') == 'on') {
                add_filter("{$this->optionName}_pwa_serviceworker_workbox", array($this, 'addBackgroundSyncToServiceWorker'));
            }

            if (daftplugInstantify::getSetting('pwaPeriodicBackgroundSync') == 'on') {
                add_filter("{$this->optionName}_pwa_serviceworker", array($this, 'addPeriodicBackgroundSyncToServiceWorker'));
            }

            if (daftplugInstantify::getSetting('pwaUrlProtocolHandler') == 'on') {
                add_filter("{$this->optionName}_pwa_manifest", array($this, 'addUrlProtocolHandlerToManifest'));
            }

            if (daftplugInstantify::getSetting('pwaWebShareTarget') == 'on') {
                add_filter("{$this->optionName}_pwa_manifest", array($this, 'addWebShareTargetToManifest'));
            }

            if (daftplugInstantify::getSetting('pwaAdaptiveLoading') == 'on' && !daftplugInstantify::isAmpPluginActive() && daftplugInstantify::getSetting('amp') == 'on'
            && ((in_array('desktop', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms')) && daftplugInstantify::isPlatform('desktop'))
            || (in_array('mobile', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms')) && daftplugInstantify::isPlatform('mobile'))
            || (in_array('tablet', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms')) && daftplugInstantify::isPlatform('tablet'))
            || (in_array('pwa', (array)daftplugInstantify::getSetting('pwaAdaptiveLoadingPlatforms')) && daftplugInstantify::isPwaPage()))) {
                add_filter("{$this->optionName}_public_js_vars", array($this, 'addAmpUrlJsVars'));
            }            
        }
        
        public function addBackgroundSyncToServiceWorker($serviceWorker) {
            $serviceWorker .= "
            workbox.routing.registerRoute(
                new RegExp('/*'),
                new workbox.strategies.NetworkOnly({
                    plugins: [
                        new workbox.backgroundSync.BackgroundSyncPlugin('bgSyncQueue', {
                            maxRetentionTime: 24 * 60
                        })
                    ]
                }),
                'POST'
            );";

            return $serviceWorker;
        }

        public function addPeriodicBackgroundSyncToServiceWorker($serviceWorker) {
            $serviceWorker .= "
                async function fetchAndCacheContent() {
                    var request = '".trailingslashit(strtok(home_url('/', 'https'), '?'))."';
                    return caches.open(CACHE + '-html').then(function(cache) {
                        return fetch(request).then(function(response) {
                            return cache.put(request, response.clone()).then(function() {
                                return response;
                            });
                        });
                    });
                }

                self.addEventListener('periodicsync', (event) => {
                    if (event.tag === 'periodicSync') {
                        event.waitUntil(fetchAndCacheContent());
                    }
                });
            ";

            return $serviceWorker;
        }

        public function addUrlProtocolHandlerToManifest($manifest) {
            $manifest['protocol_handlers'][] = array(
                'protocol' => 'web+'.daftplugInstantify::getSetting('pwaUrlProtocolHandlerProtocol'),
                'url' => daftplugInstantify::getSetting('pwaUrlProtocolHandlerUrl'),
            );

            return $manifest;
        }

        public function addWebShareTargetToManifest($manifest) {
            $manifest['share_target'] = array(
                'action' => daftplugInstantify::getSetting('pwaWebShareTargetAction'),
                'method' => 'GET',
                'enctype' => 'application/x-www-form-urlencoded',
                'params' => array(
                    'title' => 'title',
                    'text' => 'text',
                    'url' => daftplugInstantify::getSetting('pwaWebShareTargetUrlQuery'),
                ),
            );

            return $manifest;
        }

        public function addAmpUrlJsVars($vars) {
            if (amp_is_canonical() || !amp_is_available()) {
                return $vars;
            }

            $vars['ampUrl'] = amp_add_paired_endpoint(remove_query_arg(array_merge(wp_removable_query_args(), array('noamp')), amp_get_current_url()));

            return $vars;
        }
    }
}