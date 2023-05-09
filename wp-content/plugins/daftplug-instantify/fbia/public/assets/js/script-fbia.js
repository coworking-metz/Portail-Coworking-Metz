jQuery(function() {
    'use strict';
	var daftplugPublic = jQuery('.daftplugPublic[data-daftplug-plugin="daftplug_instantify"]');
	var optionName = daftplugPublic.attr('data-daftplug-plugin');
	var objectName = window[optionName + '_public_js_vars'];
});