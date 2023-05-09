jQuery(function() {
    'use strict';
	var daftplugAdmin = jQuery('.daftplugAdmin[data-daftplug-plugin="daftplug_instantify"]');
	var optionName = daftplugAdmin.attr('data-daftplug-plugin');
	var objectName = window[optionName + '_admin_js_vars'];

	// Handle FBIA custom rules JS editor
	var fbiaCustomRulesTextarea = daftplugAdmin.find('#fbiaCustomRules');
	var fbiaCustomRulesCmEditorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings) : {};
	fbiaCustomRulesCmEditorSettings.codemirror = _.extend(
		{},
		fbiaCustomRulesCmEditorSettings.codemirror,
		{
			lineNumbers: true,
			mode: 'application/json',
			indentUnit: 4,
			tabSize: 4,
			autoRefresh:true,
		}
	);
	var fbiaCustomRulesCmEditor = wp.codeEditor.initialize(fbiaCustomRulesTextarea, fbiaCustomRulesCmEditorSettings);
	daftplugAdmin.on('keyup paste', '.CodeMirror-code', function(e) {
		fbiaCustomRulesTextarea.html(fbiaCustomRulesCmEditor.codemirror.getValue()).trigger('change');
	});
});