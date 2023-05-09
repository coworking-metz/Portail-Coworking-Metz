jQuery(function() {
    'use strict';
    var daftplugAdmin = jQuery('.daftplugAdmin[data-daftplug-plugin="daftplug_instantify"]');
    var optionName = daftplugAdmin.attr('data-daftplug-plugin');
    var objectName = window[optionName + '_admin_js_vars'];

    // Handle AMP custom CSS editor
    var ampCustomCssTextarea = daftplugAdmin.find('#ampCustomCss');
    var ampCustomCssCmEditorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
    ampCustomCssCmEditorSettings.codemirror = _.extend(
        {},
        ampCustomCssCmEditorSettings.codemirror,
        {
            lineNumbers: true,
            mode: 'css',
            indentUnit: 4,
            tabSize: 4,
            autoRefresh:true,
            lint: true,
        }
    );
    var ampCustomCssCmEditor = wp.codeEditor.initialize(ampCustomCssTextarea, ampCustomCssCmEditorSettings);
    daftplugAdmin.on('keyup paste', '.CodeMirror-code', function(e) {
        ampCustomCssTextarea.html(ampCustomCssCmEditor.codemirror.getValue()).trigger('change');
    });
});