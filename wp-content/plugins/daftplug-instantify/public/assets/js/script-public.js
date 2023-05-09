jQuery(function() {
    'use strict';
    var daftplugPublic = jQuery('.daftplugPublic[data-daftplug-plugin="daftplug_instantify"]');
    var optionName = daftplugPublic.attr('data-daftplug-plugin');
	var objectName = window[optionName+'_public_js_vars'];

    // Handle tooltips
    daftplugPublic.on('mouseenter mouseleave', '[data-tooltip]', function(e) {
        var self = jQuery(this);
        var tooltip = self.attr('data-tooltip');
        var flow = self.attr('data-tooltip-flow');

        if (e.type === 'mouseenter') {
            self.append(`<span class="daftplugPublicTooltip">${tooltip}</span>`);
            var tooltipEl = self.find('.daftplugPublicTooltip');
            switch (flow) {
                case 'top':
                    tooltipEl.css({
                        'bottom': 'calc(100% + 5px)',
                        'left': '50%',
                        '-webkit-transform': 'translate(-50%, -.5em)',
                        'transform': 'translate(-50%, -.5em)',
                    });
                    break;
                case 'right':
                    tooltipEl.css({
                        'top': '50%',
                        'left': 'calc(100% + 5px)',
                        '-webkit-transform': 'translate(.5em, -50%)',
                        'transform': 'translate(.5em, -50%)',
                    });
                    break;
                case 'bottom':
                    tooltipEl.css({
                        'top': 'calc(100% + 5px)',
                        'left': '50%',
                        '-webkit-transform': 'translate(-50%, .5em)',
                        'transform': 'translate(-50%, .5em)',
                    });
                    break;
                case 'left':
                    tooltipEl.css({
                        'top': '50%',
                        'right': 'calc(100% + 5px)',
                        '-webkit-transform': 'translate(-.5em, -50%)',
                        'transform': 'translate(-.5em, -50%)',
                    });
                    break;
                default:
                    
            }
        }

        if (e.type === 'mouseleave') {
            self.find('.daftplugPublicTooltip').remove();
        }
    });
});