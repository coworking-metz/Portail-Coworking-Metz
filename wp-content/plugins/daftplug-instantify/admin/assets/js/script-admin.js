jQuery(function() {
    'use strict';
    var daftplugAdmin = jQuery('.daftplugAdmin[data-daftplug-plugin="daftplug_instantify"]');
    var optionName = daftplugAdmin.attr('data-daftplug-plugin');
    var objectName = window[optionName + '_admin_js_vars'];

    // Navigate to page and subpage
    function navigateTo(pageId, subPageId = '') {
        var page = daftplugAdmin.find('.daftplugAdminPage.-' + pageId);
        var menuItem = daftplugAdmin.find('.daftplugAdminMenu_item.-' + pageId);
        var subPage = page.find('.daftplugAdminPage_subpage.-' + subPageId);
        var subMenuItem = page.find('.daftplugAdminSubmenu_item.-' + subPageId);
        var hasSubPages = page.find('.daftplugAdminPage_subpage').length;
        var firstSubPage = page.find('.daftplugAdminPage_subpage').first();
        var firstSubPageId = firstSubPage.attr('data-subpage');
        var firstSubMenuItem = page.find('.daftplugAdminSubmenu_item').first();
        var errorPage = daftplugAdmin.find('.daftplugAdminPage.-error');

        if (page.length) {
            daftplugAdmin.find('.daftplugAdminPage').removeClass('-active');
            page.addClass('-active');
            daftplugAdmin.find('.daftplugAdminMenu_item').removeClass('-active');
            menuItem.addClass('-active');
            if (hasSubPages) {
                if (subPageId != '') {
                    if (subPage.length) {
                        daftplugAdmin.find('.daftplugAdminPage_subpage').removeClass('-active');
                        subPage.addClass('-active');
                    
                        daftplugAdmin.find('.daftplugAdminSubmenu_item').removeClass('-active');
                        subMenuItem.addClass('-active');
                    } else {
                        page.removeClass('-active');
                        menuItem.removeClass('-active');
                        errorPage.addClass('-active');
                    }
                } else {
                    firstSubPage.addClass('-active');
                    firstSubMenuItem.addClass('-active');
                    location.hash = '#/'+pageId+'-'+firstSubPageId+'/';
                }
            } else {
                location.hash = '#/'+pageId+'/';
            }
        } else {
            daftplugAdmin.find('.daftplugAdminPage').removeClass('-active');
            errorPage.addClass('-active');
        }
    }

    // start plugin intro
    function startIntro() {
        introJs().setOptions({
            dontShowAgain: false,
            showBullets: false,
            showProgress: true,
            hidePrev: true,
            nextToDone: true,
            exitOnEsc: true,
            exitOnOverlayClick: false,
            showStepNumbers: false,
            keyboardNavigation: true,
            scrollToElement: true,
            disableInteraction: true,
            steps: [
                {
                    title: 'Hello 👋!',
                    element: document.querySelector('.daftplugAdminHeader_logo'),
                    intro: 'Let me guide you through Instantify really quick.',
                    position: 'right',
                },
                {
                    title: 'Navigation',
                    element: document.querySelector('.daftplugAdminMenu_list'),
                    intro: 'This is the main navigation menu. You can use it to navigate through the different pages.',
                    position: 'right',
                    onChange: function() { 
                        navigateTo('overview');
                    },
                },
                {
                    title: 'Search',
                    element: document.querySelector('.daftplugAdminHeader_search'),
                    intro: 'You can also use the search button to search for specific settings.',
                    position: 'left',
                },
                {
                    title: 'Global Features',
                    element: document.querySelector('.daftplugAdminPage.-overview > div:nth-child(3) > div'),
                    intro: 'You can enable or disable plugin global features from here.',
                    position: 'right',
                },
                {
                    title: 'License',
                    element: document.querySelector('.daftplugAdminPage.-overview > div:nth-child(4) > div'),
                    intro: 'On this section you can deactivate your license by clicking and holding the button if you\'ll need to use Instantify on another website with your current license code.',
                    position: 'left',
                },
                {
                    title: 'Android & iOS Apps',
                    element: document.querySelector('.daftplugAdminGetAppNotice'),
                    intro: 'From here you can get ready-made Android and iOS apps for your website to publish your PWA website to the Google Play and Apple App Stores as native apps.',
                    position: 'left',
                    onChange: function() { 
                        navigateTo('overview');
                    },
                },
                {
                    title: 'PWA Features',
                    element: document.querySelector('.daftplugAdminPage.-pwa .daftplugAdminSubmenu_list'),
                    intro: 'Use this menu to navigate through the different PWA features and customize them.',
                    position: 'bottom',
                    onChange: function() { 
                        navigateTo('pwa');
                    },
                },
                {
                    title: 'AMP Features',
                    element: document.querySelector('.daftplugAdminPage.-amp .daftplugAdminSubmenu_list'),
                    intro: 'Use this menu to navigate through the different AMP features and customize them.',
                    position: 'bottom',
                    onChange: function() { 
                        navigateTo('amp');
                    },
                },
                {
                    title: 'FBIA Features',
                    element: document.querySelector('.daftplugAdminPage.-fbia .daftplugAdminSubmenu_list'),
                    intro: 'Use this menu to navigate through the different FBIA features and customize them.',
                    position: 'bottom',
                    onChange: function() { 
                        navigateTo('fbia');
                    },
                },
                {
                    title: 'Save Settings',
                    element: document.querySelector('.daftplugAdminPage.-fbia .daftplugAdminSettings_submit'),
                    intro: 'After changing the settings you can save them by clicking this button.',
                    position: 'left',
                    onChange: function() { 
                        navigateTo('fbia');
                    },
                },
                {
                    title: 'FAQ',
                    element: document.querySelector('.daftplugAdminPage.-support > div:nth-child(2) > div'),
                    intro: 'Reading the FAQ is useful when you\'re experiencing a common issue related to the plugin.',
                    position: 'right',
                    onChange: function() { 
                        navigateTo('support');
                    },
                },
                {
                    title: 'Support Ticket',
                    element: document.querySelector('.daftplugAdminPage.-support > div:nth-child(3) > div'),
                    intro: 'If the FAQ didn\'t help and you have a hard time resolving the problem, please submit a ticket.',
                    position: 'left',
                },
                {
                    title: 'Explore PWA',
                    element: document.querySelector('.daftplugAdminPage.-support > div:nth-child(5) > div'),
                    intro: 'PWAs are exciting things and truly the future of the mobile web. You can learn more about them by reading articles here.',
                    position: 'right',
                },
                {
                    title: 'Changelog',
                    element: document.querySelector('.daftplugAdminPage.-support > div:nth-child(6) > div'),
                    intro: 'Lastly, here is the changelog so you can see what has added, improved and fixed in the plugin on each update. Thank you and enjoy the plugin ⚡!',
                },
            ],
        }).onchange(function() {
            if (this._introItems[this._currentStep].onChange) {
                this._introItems[this._currentStep].onChange();
            }
        }).oncomplete(function() {
            localStorage.setItem('introShown', true);
        }).onexit(function() {
            localStorage.setItem('introShown', true);
        }).start();
    }

    // Set cookie
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }
    
    // Get cookie
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Remove cookie
    function removeCookie(name) {
        setCookie(name, '', -1);
    }

    // Handle loader
    daftplugAdmin.find('.daftplugAdminLoader').each(function(e) {
        var self = jQuery(this);
        var size = self.attr('data-size');
        var duration = self.attr('data-duration');

        self.html(`
            <div class="daftplugAdminLoader_box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="daftplugAdminLoader_box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="daftplugAdminLoader_box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="daftplugAdminLoader_box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        `).attr('style', `--size:${size};--duration:${duration}`);
    });

    // Handle navigation and intro load
    jQuery(window).on('load hashchange', function(e) {
        daftplugAdmin.find('.daftplugAdminLoader').fadeOut('fast');
        daftplugAdmin.find('.daftplugAdminHeader').add('.daftplugAdminMain').add('.daftplugAdminFooter').removeClass('-disabled');
        if (daftplugAdmin.find('.daftplugAdminPage.-activation').length) {
            location.hash = '#/activation/';
            daftplugAdmin.find('.daftplugAdminPage.-activation').addClass('-active');
            daftplugAdmin.find('.daftplugAdminHeader').css('justify-content', 'center');
            daftplugAdmin.find('.daftplugAdminHeader_versionText, .daftplugAdminHeader_search, .daftplugAdminButton.-getAppHeader').hide();
        } else {
            if (location.hash) {
                var hash = location.hash.replace(/#|\//g, '').split('-');
                var pageId = hash[0];
                var subPageId = hash[1];
                navigateTo(pageId, subPageId);
            } else {
                location.hash = '#/overview/';
                daftplugAdmin.find('.daftplugAdminPage.-overview').addClass('-active');
                daftplugAdmin.find('.daftplugAdminMenu_item.-overview').addClass('-active');
            }

            if (!localStorage.getItem('introShown')) {
                startIntro();
            }
        }
    });

    // Handle FAQ
    daftplugAdmin.find('.daftplugAdminFaq_item').each(function(e) {
        var self = jQuery(this);
        var question = self.find('.daftplugAdminFaq_question');

        question.on('click', function(e) {
            if (self.hasClass('-active')) {
                self.removeClass('-active');
            } else {
                daftplugAdmin.find('.daftplugAdminFaq_item').removeClass('-active');
                self.addClass('-active');
            }
        });
    });

    // Handle submit button
    daftplugAdmin.find('.daftplugAdminButton.-submit').each(function(e) {
        var self = jQuery(this);
        var submitText = self.attr('data-submit');
        var waitingText = self.attr('data-waiting');
        var submittedText = self.attr('data-submitted');
        var failedText = self.attr('data-failed');

        self.html(`<span class="daftplugAdminButton_iconset">
                       <svg class="daftplugAdminButton_icon -iconSubmit">
                           <use href="#iconSubmit"></use>
                       </svg>
                       <svg class="daftplugAdminButton_icon -iconLoading">
                           <use href="#iconLoading"></use>
                       </svg>
                       <svg class="daftplugAdminButton_icon -iconSuccess">
                           <use href="#iconSuccess"></use>
                       </svg>
                       <svg class="daftplugAdminButton_icon -iconFail">
                           <use href="#iconFail"></use>
                       </svg>
                   </span>
                   <ul class="daftplugAdminButton_textset">
                       <li class="daftplugAdminButton_text -submit">
                           ${submitText}
                       </li>
                       <li class="daftplugAdminButton_text -waiting">
                           ${waitingText}
                       </li>
                       <li class="daftplugAdminButton_text -submitted">
                           ${submittedText}
                       </li>
                       <li class="daftplugAdminButton_text -submitFailed">
                           ${failedText}
                       </li>
                   </ul>`);

        var buttonTexts = self.find('.daftplugAdminButton_textset');
        var buttonText = buttonTexts.find('.daftplugAdminButton_text');
        var longestButtonTextChars = '';

        buttonText.each(function(e) {
            var self = jQuery(this);
			var buttonTextChars = self.text();
			if (buttonTextChars.length > longestButtonTextChars.length) {
				longestButtonTextChars = buttonTextChars;
			}
        });

        buttonTexts.css('width', longestButtonTextChars.trim().length * 7.5 +'px');

        if (self.hasClass('-confirm')) {
            var sureText = self.attr('data-sure');
            var confirmDuration = self.attr('data-duration');
            var clickDuration = 0;

            self.attr('style', `--confirmDuration:${confirmDuration};`);
            self.on('mousedown touchstart', function(e) {
                e.preventDefault();
                buttonText.filter('.-waiting').text(sureText);
                self.addClass('-loading -progress');
                clickDuration = setTimeout(function(e) {
                    buttonText.filter('.-waiting').text(waitingText);
                    self.removeClass('-loading -progress').trigger('submit');
                }, parseInt(confirmDuration));
            }).on('mouseup touchend', function(e) {
                self.removeClass('-loading -progress');
                clearTimeout(clickDuration);
            });
        }
    });

    // Handle add field button
    daftplugAdmin.find('.daftplugAdminButton.-addField').each(function(e) {
        var self = jQuery(this);
        var addTarget = self.attr('data-add');
        var miniFieldset = daftplugAdmin.find('.-miniFieldset[class*="-'+addTarget+'"]');
        var i = 0;

        miniFieldset.prepend(`
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="daftplugAdminMiniFieldset_close -iconClose">
                <g stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="10" cy="10" r="10" id="circle"></circle>
                    <path d="M7,7 L13,13" id="line"></path>
                    <path d="M7,13 L13,7" id="line"></path>
                </g>
            </svg>
        `).each(function(e) {
            var self = jQuery(this);
            self.find('.daftplugAdminInputCheckbox_field').trigger('change');
            var miniFieldsetCheckboxField = self.find('.daftplugAdminInputCheckbox.-hidden').find('.daftplugAdminInputCheckbox_field');
            if (miniFieldsetCheckboxField.is(':checked')) {
                self.show().prop('disabled', false);
                i++;
            } else {
                self.hide().prop('disabled', true);
            }
        });

        var close = miniFieldset.find('.daftplugAdminMiniFieldset_close');

        self.on('click', function(e) {  
            i++;
            miniFieldset.filter('.-miniFieldset[class*="-'+addTarget+i+'"]').show().prop('disabled', false);
            miniFieldset.find('.daftplugAdminInputCheckbox_field[id="'+addTarget+i+'"]').prop('checked', true).trigger('change');
            miniFieldset.find('.daftplugAdminInputCheckbox_field').trigger('change');
            if (!miniFieldset.filter('.-miniFieldset[class*="-'+addTarget+(i+1)+'"]').length) {
                self.hide();
            }
        });

        close.on('click', function(e) {
            self.show();
            miniFieldset.filter('.-miniFieldset[class*="-'+addTarget+i+'"]').hide().prop('disabled', true);
            miniFieldset.find('.daftplugAdminInputCheckbox_field[id="'+addTarget+i+'"]').prop('checked', false).trigger('change');
            if (i != 0) {
                i--;
            }
        });
    });

    // Handle tooltips
    daftplugAdmin.on('mouseenter mouseleave', '[data-tooltip]', function(e) {
        var self = jQuery(this);
        var tooltip = self.attr('data-tooltip');
        var flow = self.attr('data-tooltip-flow');

        if (e.type === 'mouseenter') {
            self.append(`<span class="daftplugAdminTooltip">${tooltip}</span>`);
            var tooltipEl = self.find('.daftplugAdminTooltip');
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
            self.find('.daftplugAdminTooltip').remove();
        }
    });

    // Handle feature pills
    daftplugAdmin.find('.daftplugAdminFieldset[data-feature-type]').each(function(e) {
        var self = jQuery(this);
        var featureType = self.attr('data-feature-type');
        var title = self.find('.daftplugAdminFieldset_title');

        switch(featureType) {
            case 'new':
                title.append(`<span class="daftplugAdminFeaturePill" style="background-color: #ff3a3a;">${featureType}</span>`);
                break;
            case 'beta':
                title.append(`<span class="daftplugAdminFeaturePill" style="background-color: #ffb13e;">${featureType}</span>`);
                break;
            default:
                title.append(`<span class="daftplugAdminFeaturePill" style="background-color: #444f5b;">${featureType}</span>`);
        }
    });

    // Handle articles
	daftplugAdmin.find('.daftplugAdminSupportArticles_list').each(function(e) {
        var self = jQuery(this);
        var title = self.attr('data-title');
        var item = self.find('.daftplugAdminSupportArticles_item');

        self.before(`<h5 class="daftplugAdminSupportArticles_subtitle">${title}</h5>`).not(':last-child').after(`<br>`);

        item.each(function(e) {
            var self = jQuery(this);
            var link = self.attr('data-link');
            var text = self.attr('data-text');

            self.prepend(`
                <a class="daftplugAdminLink" target="_blank" href="${link}" style="font-size:15px; margin-bottom:7px;">${text}</a>
            `);
        });
	});

    // Handle changelog
	daftplugAdmin.find('.daftplugAdminChangelog_list').each(function(e) {
        var self = jQuery(this);
        var title = self.attr('data-title');
        var item = self.find('.daftplugAdminChangelog_item');

        self.before(`<h5 class="daftplugAdminChangelog_date">${title}</h5>`).not(':last-child').after(`<br>`);

        item.each(function(e) {
            var self = jQuery(this);
            var type = self.attr('data-type');
            var text = self.attr('data-text');
            switch(type) {
                case 'added':
                    self.prepend(`
                        <span class="daftplugAdminFeaturePill" style="background-color: #4ad504; margin: 0;">${type}</span>
                        <p class="daftplugAdminChangelog_text"> - ${text}</p>
                    `);
                    break;
                case 'improved':
                    self.prepend(`<span class="daftplugAdminFeaturePill" style="background-color: #4073ff; margin: 0;">${type}</span>
                        <p class="daftplugAdminChangelog_text"> - ${text}</p>
                    `);
                    break;
                case 'fixed':
                    self.prepend(`<span class="daftplugAdminFeaturePill" style="background-color: #ffb13e; margin: 0;">${type}</span>
                        <p class="daftplugAdminChangelog_text"> - ${text}</p>
                    `);
                    break;
                case 'removed':
                    self.prepend(`<span class="daftplugAdminFeaturePill" style="background-color: #ff3a3a; margin: 0;">${type}</span>
                        <p class="daftplugAdminChangelog_text"> - ${text}</p>
                    `);
                    break;
                default:
                    self.prepend(`<span class="daftplugAdminFeaturePill" style="background-color: #444f5b; margin: 0;">${type}</span>
                        <p class="daftplugAdminChangelog_text"> - ${text}</p>
                    `);
            }
        });
	});

    // Handle popup
    daftplugAdmin.find('.daftplugAdminPopup').each(function(e) {
        var self = jQuery(this);
        var openPopup = self.attr('data-popup');
        var popupContainer = self.find('.daftplugAdminPopup_container');

        daftplugAdmin.on('click', '[data-open-popup="'+openPopup+'"]', function(e) {
            self.addClass('-active');
        });

        popupContainer.on('click', function(e) {
            e.stopPropagation();
        }).find('fieldset').not('.-miniFieldset').css('border', 'none');

        self.on('click', function(e) {
            self.removeClass('-active');
        });
    });

    // Handle input has value
    daftplugAdmin.find('.daftplugAdminInputText, .daftplugAdminInputNumber, .daftplugAdminInputTextarea, .daftplugAdminInputColor').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputText_field, .daftplugAdminInputNumber_field, .daftplugAdminInputTextarea_field, .daftplugAdminInputColor_field');

        field.on('change input keyup paste', function() {
            field.val().length ? field.addClass('-hasValue') : field.removeClass('-hasValue');
        }).trigger('change');
    });

    // Handle text input
    daftplugAdmin.find('.daftplugAdminInputText').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputText_field');
        var placeholder = field.attr('data-placeholder');

        field.after('<span class="daftplugAdminInputText_placeholder">' + placeholder + '</span>');

        field.on('invalid', function(e) {
            self.addClass('-invalid');
            setTimeout(function(e) {
                self.removeClass('-invalid');
            }, 2300);
        });
    });

    // Handle textarea
    daftplugAdmin.find('.daftplugAdminInputTextarea').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputTextarea_field');
        var placeholder = field.attr('data-placeholder');

        if (field.attr('data-attachments') == 'true') {
            field.css('padding-bottom', '52.5px');
            field.after(`
                <label for="attachments" class="daftplugAdminInputTextarea_icon" data-tooltip="Attach a photo (5 max)" data-tooltip-flow="left">
                    <svg>
                        <use href="#iconAttachment"></use>
                    </svg>
                    <input id="attachments" type="file" accept="image/*" style="display: none;"/>
                </label>
                <div class="daftplugAdminInputTextarea_attachments"></div>
            `);
            
            if (window.File && window.FileList && window.FileReader) {
                var attachmentCount = 0;
                var attachmentsContainer = self.find('.daftplugAdminInputTextarea_attachments');
                self.on('change', '#attachments', function(e) {
                    var fileInput = document.getElementById('attachments');
                    var reader = new FileReader();
                    reader.fileName = fileInput.files[0].name.length > 7 ? fileInput.files[0].name.substring(0, 7)+'...' : fileInput.files[0].name;
                    reader.onload = function(readerEvent) {
                        if (attachmentCount > 4) {
                            alert('You can only upload a maximum of 5 files.');
                            return;
                        } else if (!fileInput.files[0].type.match(/image.*/)) {
                            alert('You can attach only images.');
                            return;
                        } else {
                            attachmentCount++;
                            attachmentsContainer.append(`
                                <span class="attachment${attachmentCount}">
                                    <input name="attachment${attachmentCount}" id="attachment${attachmentCount}" type="hidden" value="${readerEvent.target.result}"/>
                                    <img class="daftplugAdminInputTextarea_img" src="${readerEvent.target.result}"/>
                                    ${readerEvent.target.fileName}
                                    <svg class="daftplugAdminInputTextarea_xicon"><use href="#iconX"></use></svg>
                                </span>
                            `);
                        }
                    }
                    reader.readAsDataURL(fileInput.files[0]);
                });

                self.on('click', '.daftplugAdminInputTextarea_xicon', function(e) {
                    var self = jQuery(this);
                    self.parent().remove();
                    attachmentCount--;
                    for (var i=0; i<attachmentCount; i++) {
                        attachmentsContainer.children('span').eq(i).attr('class', 'attachment'+(i+1)).find('input').attr('name', 'attachment'+(i+1)).attr('id', 'attachment'+(i+1));
                    }
                });
            }
        }

        field.after('<span class="daftplugAdminInputTextarea_placeholder">' + placeholder + '</span>');

        field.css('height', field.prop('scrollHeight')+'px').on('input', function(e) {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight+1)+'px'; 
        });

        field.on('invalid', function(e) {
            self.addClass('-invalid');
            setTimeout(function(e) {
                self.removeClass('-invalid');
            }, 2300);
        });
    });

    // Handle checkbox
    daftplugAdmin.find('.daftplugAdminInputCheckbox').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputCheckbox_field');
        var dependentDisableD = daftplugAdmin.find('.-' + field.attr('id') + 'DependentDisableD');
        var dependentHideD = daftplugAdmin.find('.-' + field.attr('id') + 'DependentHideD');
        var dependentDisableE = daftplugAdmin.find('.-' + field.attr('id') + 'DependentDisableE');
        var dependentHideE = daftplugAdmin.find('.-' + field.attr('id') + 'DependentHideE');
        var dependentDisableDField = dependentDisableD.find('[class*="_field"]');
        var dependentDisableEField = dependentDisableE.find('[class*="_field"]');
        var dependentHideDField = dependentHideD.find('[class*="_field"]');
        var dependentHideEField = dependentHideE.find('[class*="_field"]');

        dependentDisableDField.add(dependentDisableEField).add(dependentHideDField).add(dependentHideEField).each(function(e) {
        	if (jQuery(this).is('[required]')) {
        		jQuery(this).attr('data-required', 'true');
        	}
        });

        if (self.hasClass('-imgcustom')) {
            var title = self.attr('data-title');
            var img = self.attr('data-img');
            var edit = self.attr('data-edit');
            var name = field.attr('name');
            if (edit == 'disabled') {
                edit = 'Edit';
                var disabledAttrs = 'style="opacity: 0.6;" data-tooltip="Not Editable" data-tooltip-flow="top"';
            }
            self.append(`
                <div class="daftplugAdminInputCheckbox_custom">
                    <svg class="daftplugAdminInputCheckbox_icon -iconCheck"><use href="#iconCheck"></use></svg>
                    <img class="daftplugAdminInputCheckbox_img" src="${img}" />
                    <span class="daftplugAdminInputCheckbox_text">${title}</span>
                    <span class="daftplugAdminInputCheckbox_edit" ${disabledAttrs} onclick="return false;" data-open-popup="${name}">${edit}</span>
                </div>
            `);
        } else {
            field.after(`<span class="daftplugAdminInputCheckbox_background"></span>
                         <span class="daftplugAdminInputCheckbox_grabholder"></span>`);
        }

        field.on('change', function(e) {
        	if (field.is(':checked')) {
        		dependentDisableD.removeClass('-disabled');
                dependentDisableE.addClass('-disabled');
                dependentHideD.show();
                dependentHideE.hide();
                dependentDisableEField.add(dependentHideEField).prop('required', false);
                dependentDisableDField.add(dependentHideDField).each(function(e) {
	        		if (jQuery(this).attr('data-required') == 'true') {
	        			jQuery(this).prop('required', true);
	        		} else {
	        			jQuery(this).prop('required', false);
	        		}
                });
        	} else {
				dependentDisableD.addClass('-disabled');
                dependentDisableE.removeClass('-disabled');
                dependentHideD.hide();
                dependentHideE.show();
        		dependentDisableDField.add(dependentHideDField).prop('required', false);
                dependentDisableEField.add(dependentHideEField).each(function(e) {
	        		if (jQuery(this).attr('data-required') == 'true') {
	        			jQuery(this).prop('required', true);
	        		} else {
	        			jQuery(this).prop('required', false);
	        		}
                });
        	}
        }).trigger('change');
    });

    // Handle number input
    daftplugAdmin.find('.daftplugAdminInputNumber').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputNumber_field');
        var placeholder = field.attr('data-placeholder');
        var step = parseFloat(field.attr('step'));
        var min = parseFloat(field.attr('min'));
        var max = parseFloat(field.attr('max'));

        field.before('<svg class="daftplugAdminInputNumber_icon -iconMinus"><use href="#iconMinus"></use></svg>')
             .after(`<span class="daftplugAdminInputNumber_placeholder" style="left: 42px;">${placeholder}</span>
                     <svg class="daftplugAdminInputNumber_icon -iconPlus"><use href="#iconPlus"></use></svg>`);

        var icon = self.find('.daftplugAdminInputNumber_icon');

        field.on('focus blur', function(e) {
            if(e.type == 'focus' || e.type == 'focusin') { 
              icon.addClass('-focused');
            } else{
              icon.removeClass('-focused');
            }
        });

        self.find('.daftplugAdminInputNumber_icon.-iconMinus').on('click', function(e) {
            var value = parseFloat(field.val());
            if (value > min) {
                field.val(value - step).trigger('change');
            }
        });

        self.find('.daftplugAdminInputNumber_icon.-iconPlus').on('click', function(e) {
            var value = parseFloat(field.val());
            if (field.val().length) {
                if (value < max) {
                    field.val(value + step).trigger('change');
                }
            } else {
                field.val(step).trigger('change');
            }
        });

        field.on('invalid', function(e) {
            self.add(icon).addClass('-invalid');
            setTimeout(function(e) {
                self.add(icon).removeClass('-invalid');
            }, 2300);
        });
    });

    // Handle select input
    daftplugAdmin.find('.daftplugAdminInputSelect').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputSelect_field');
        var fieldOption = field.find('option');
        var label = jQuery('label[for="'+field.attr('id')+'"]');
        var placeholder = field.attr('data-placeholder');

        field.after(`<div class="daftplugAdminInputSelect_dropdown"></div>
                     <span class="daftplugAdminInputSelect_placeholder">${placeholder}</span>
                     <ul class="daftplugAdminInputSelect_list"></ul>
                     <span class="daftplugAdminInputSelect_arrow"></span>`);

        fieldOption.each(function(e) {
            self.find('.daftplugAdminInputSelect_list').append(`<li class="daftplugAdminInputSelect_option" data-value="${jQuery(this).val().trim()}">
                                                                    <a class="daftplugAdminInputSelect_text">${jQuery(this).text().trim()}</a>
                                                                </li>`);
        });

        var dropdown = self.find('.daftplugAdminInputSelect_dropdown');
        var list = self.find('.daftplugAdminInputSelect_list');
        var option = self.find('.daftplugAdminInputSelect_option');

        dropdown.add(list).attr('data-name', field.attr('name'));

        if (field.is('[multiple]')) {
        	dropdown.attr('data-multiple', 'true');
        	if (!field.find('option:selected').length) {
                fieldOption.first().prop('selected', true);
            }
            field.find('option:selected').each(function(e) {
                var self = jQuery(this);
        		dropdown.append(function(e) {
        			return jQuery('<span class="daftplugAdminInputSelect_choice" data-value="'+self.val()+'">'+self.text()+'<svg class="daftplugAdminInputSelect_deselect -iconX"><use href="#iconX"></use></svg></span>').on('click', function(e) {
		            	var self = jQuery(this);
		                e.stopPropagation();
		                self.remove();
		                list.find('.daftplugAdminInputSelect_option[data-value="'+self.attr('data-value')+'"]').removeClass('-selected');
		                list.css('top', dropdown.height() + 5).find('.daftplugAdminInputSelect_noselections').remove();
		                field.find('option[value="'+self.attr('data-value')+'"]').prop('selected', false);
			            if (dropdown.children(':visible').length === 0) {
			            	dropdown.removeClass('-hasValue');
                        }
        			});
        		}).addClass('-hasValue');
                list.find('.daftplugAdminInputSelect_option[data-value="'+self.val()+'"]').addClass('-selected');
            });
            if (!option.not('.-selected').length) {
                list.append('<h5 class="daftplugAdminInputSelect_noselections">No Selections</h5>');
            }
        	list.css('top', dropdown.height() + 5);
        	option.on('click', function(e) {
        		var self = jQuery(this);
				e.stopPropagation();
	        	self.addClass('-selected');
	        	field.find('option[value="'+self.attr('data-value')+'"]').prop('selected', true);
        		dropdown.append(function(e) {
        			return jQuery('<span class="daftplugAdminInputSelect_choice" data-value="'+self.attr('data-value')+'">'+self.children().text()+'<svg class="daftplugAdminInputSelect_deselect -iconX"><use href="#iconX"></use></svg></span>').on('click', function(e) {
		            	var self = jQuery(this);
		                e.stopPropagation();
                        self.remove();
		                list.find('.daftplugAdminInputSelect_option[data-value="'+self.attr('data-value')+'"]').removeClass('-selected');
		                list.css('top', dropdown.height() + 5).find('.daftplugAdminInputSelect_noselections').remove();
		                field.find('option[value="'+self.attr('data-value')+'"]').prop('selected', false);
			            if (dropdown.children(':visible').length === 0) {
			            	dropdown.removeClass('-hasValue');
                        }
        			});
        		}).addClass('-hasValue');
	        	list.css('top', dropdown.height() + 5);
	            if (!option.not('.-selected').length) {
	            	list.append('<h5 class="daftplugAdminInputSelect_noselections">No Selections</h5>');
                }
        	});
	        dropdown.add(label).on('click', function(e) {
                daftplugAdmin.find('.daftplugAdminInputSelect_dropdown, .daftplugAdminInputSelect_list').not(dropdown).not(list).removeClass('-open');
	            e.stopPropagation();
	            e.preventDefault();
	            dropdown.toggleClass('-open');
	            list.toggleClass('-open').scrollTop(0).css('top', dropdown.height() + 5);
	        });
        } else {
	        if (field.find('option:selected').length) {
	            dropdown.attr('data-value', jQuery(this).find('option:selected').val()).text(jQuery(this).find('option:selected').text()).addClass('-hasValue');
	            list.find('.daftplugAdminInputSelect_option[data-value="'+jQuery(this).find('option:selected').val()+'"]').addClass('-selected');
	        }
	        option.on('click', function(e) {
	        	var self = jQuery(this);
	        	option.removeClass('-selected');
            	self.addClass('-selected');
            	fieldOption.prop('selected', false);
            	field.find('option[value="'+self.attr('data-value')+'"]').prop('selected', true);
            	dropdown.text(self.children().text()).addClass('-hasValue');
	        });
	        dropdown.add(label).on('click', function(e) {
                daftplugAdmin.find('.daftplugAdminInputSelect_dropdown, .daftplugAdminInputSelect_list').not(dropdown).not(list).removeClass('-open');
	            e.stopPropagation();
	            e.preventDefault();
	            dropdown.toggleClass('-open');
	            list.toggleClass('-open').scrollTop(0);
	        });
        }

        jQuery(document).add(daftplugAdmin.find('.daftplugAdminPopup_container')).on('click touch', function(e) {
            if (dropdown.hasClass('-open')) {
                dropdown.toggleClass('-open');
                list.removeClass('-open');
            }
        });

        field.on('invalid', function(e) {
        	self.addClass('-invalid');
            setTimeout(function(e) {
                self.removeClass('-invalid');
            }, 2300);
        });
    });

    // Handle range input
    daftplugAdmin.find('.daftplugAdminInputRange').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputRange_field');
        var val = parseFloat(field.val());
        var min = parseFloat(field.attr('min'));
        var max = parseFloat(field.attr('max'));

        field.after('<output class="daftplugAdminInputRange_output">' + val + '</output>');
        var output = self.find('.daftplugAdminInputRange_output');

        field.on('input change', function(e) {
            var val = parseFloat(field.val());
            var fillPercent = (100 * (val - min)) / (max - min);
            field.css('background', 'linear-gradient(to right, #4073ff 0%, #4073ff ' + fillPercent + '%, #d9dbde ' + fillPercent + '%)');
            output.text(val);
        }).trigger('change');
    });

    // Handle color input
    daftplugAdmin.find('.daftplugAdminInputColor').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputColor_field');
        var label = self.prev('.daftplugAdminField_label');
        var color = field.val();
        var placeholder = field.attr('data-placeholder');
        var colorInput = new JSColor(document.getElementById(field.attr('id')), {
            previewPosition: 'right',
            previewSize: 0,
            previewPadding: 0,
            borderColor: '#D9DBDE',
            borderRadius: 4,
            padding: 10,
            width: 180,
            height: 100,
            controlBorderColor: '#D9DBDE',
            pointerBorderColor: 'rgba(0,0,0,0)',
            shadowColor: 'rgba(0,0,0,0.12)',
            shadowBlur: 20,
            zIndex: 999999,
            onInput: 'this.targetElement.style.color = this.isLight() ? "#000" : "#fff"',
        });

        field.after('<span class="daftplugAdminInputColor_placeholder" style="background: '+color+'">' + placeholder + '</span>');
        var elmPlaceholder = self.find('.daftplugAdminInputColor_placeholder');

        label.on('click', function(e) {
        	colorInput.show();
        });

        field.on('input change', function(e) {
            var color = field.val();
            elmPlaceholder.css('background', color);
        });

        colorInput.trigger('input change');
        
        field.on('invalid', function(e) {
            self.addClass('-invalid');
            setTimeout(function(e) {
                self.removeClass('-invalid');
            }, 2300);
        });
    });

    // Handle upload input
    daftplugAdmin.find('.daftplugAdminInputUpload').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputUpload_field');
        var label = jQuery('label[for="'+field.attr('id')+'"]');
        var mimes = field.attr('data-mimes');
        var maxWidth = field.attr('data-max-width');
        var minWidth = field.attr('data-min-width');
        var maxHeight = field.attr('data-max-height');
        var minHeight = field.attr('data-min-height');
        var imageSrc = field.attr('data-attach-url');
        var frame;

        if (imageSrc) {
            jQuery.ajax({
                url: imageSrc,
                type: 'HEAD',
                error: function() {
                    field.val('');
                    field.removeAttr('data-attach-url');
                },
                success: function() {
                    field.addClass('-hasFile');
                }
            });
        }

        field.after(`<div class="daftplugAdminInputUpload_attach">
                        <div class="daftplugAdminInputUpload_upload">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="daftplugAdminInputUpload_icon -iconUpload">
                                <g stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M32,1 L32,1 C49.1208272,1 63,14.8791728 63,32 L63,32 C63,49.1208272 49.1208272,63 32,63 L32,63 C14.8791728,63 1,49.1208272 1,32 L1,32 C1,14.8791728 14.8791728,1 32,1 Z" id="circleActive"></path>
                                    <path d="M22,26 L22,38 C22,42.418278 25.581722,46 30,46 C34.418278,46 38,42.418278 38,38 L38,20 L36,20 L36,38 C36,41.3137085 33.3137085,44 30,44 C26.6862915,44 24,41.3137085 24,38 L24,26 C24,25.4477153 23.5522847,25 23,25 C22.4477153,25 22,25.4477153 22,26 Z" id="clipBack"></path>
                                    <g id="preview"><image preserveAspectRatio="none" width="30px" height="30px" href=\'${imageSrc}\'></image></g>
                                    <path d="M32,25 C32,24.4477153 32.4477153,24 33,24 C33.5522847,24 34,24.4477153 34,25 L34,38 C34,40.209139 32.209139,42 30,42 C27.790861,42 26,40.209139 26,38 L26,20 C26,16.6862915 28.6862915,14 32,14 C35.3137085,14 38,16.6862915 38,20 L36,20 C36,17.790861 34.209139,16 32,16 C29.790861,16 28,17.790861 28,20 L28,38 C28,39.1045695 28.8954305,40 30,40 C31.1045695,40 32,39.1045695 32,38 L32,25 Z" id="clipFront"></path>
                                </g>
                            </svg>
                        </div>
                        <div class="daftplugAdminInputUpload_undo">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="daftplugAdminInputUpload_icon -iconUndo">
                                <g stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="10" cy="10" r="10" id="circle"></circle>
                                    <path d="M7,7 L13,13" id="line"></path>
                                    <path d="M7,13 L13,7" id="line"></path>
                                </g>
                            </svg>
                        </div>
                    </div>`);

        var upload = self.find('.daftplugAdminInputUpload_upload');
        var undo = self.find('.daftplugAdminInputUpload_undo');
        var preview = self.find('#preview');

        upload.add(label).on('click', function(e) {
            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Select or upload a file',
                button: {
                    text: 'Select File'
                },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var errors = [];

                if (mimes !== '') {
                    var mimesArray = mimes.split(',');
                    var fileMime = attachment.subtype;
                    if (jQuery.inArray(fileMime, mimesArray) === -1) {
                        errors.push('This file should be one of the following file types:\n' + mimes);
                    }
                }

                if (maxHeight !== '' && attachment.height > maxHeight) {
                    errors.push('Image can\'t be higher than ' + maxHeight + 'px.');
                }

                if (minHeight !== '' && attachment.height < minHeight) {
                    errors.push('Image should be at least ' + minHeight + 'px high.');
                }

                if (maxWidth !== '' && attachment.width > maxWidth) {
                    errors.push('Image can\'t be wider than ' + maxWidth + 'px.');
                }

                if (minWidth !== '' && attachment.width < minWidth) {
                    errors.push('Image should be at least ' + minWidth + 'px wide.');
                }

                if (errors.length) {
                    alert(errors.join('\n\n'));
                    return;
                }

                if (attachment.type === 'image') {
                    var imageSrc = attachment.url;
                    var image = '<image preserveAspectRatio="none" width="30px" height="30px" href=\'' + imageSrc + '\'></image>';
                } else {
                    var imageSrc = objectName.fileIcon;
                    var image = '<image preserveAspectRatio="none" width="30px" height="30px" href=\'' + imageSrc + '\'></image>';
                }

                field.val(attachment.id).addClass('-active -hasFile');
                field.attr('data-attach-url', imageSrc);
                setTimeout(function() {
                    field.removeClass('-active');
                }, 1000);

                preview.html(image);
            });

            frame.open();
        });

        undo.on('click', function(e) {
            field.val('').removeClass('-hasFile');
            field.removeAttr('data-attach-url');
        });

        field.on('invalid', function(e) {
            self.addClass('-invalid');
            setTimeout(function(e) {
                self.removeClass('-invalid');
            }, 2300);
        });
    });

    // Activate license
    daftplugAdmin.find('.daftplugAdminActivateLicense_form').on('submit', function(e) {
        e.preventDefault();
        var self = jQuery(this);
        var action = optionName + '_activate_license';
        var nonce = self.attr('data-nonce');
        var purchaseCode = self.find('#purchaseCode').val();
        var button = self.find('.daftplugAdminButton.-submit');
        var responseText = self.find('.daftplugAdminField_response');

        jQuery.ajax({
            url: ajaxurl,
            dataType: 'text',
            type: 'POST',
            data: {
                action: action,
                nonce: nonce,
                purchaseCode: purchaseCode
            },
            beforeSend: function() {
                button.addClass('-loading');
            },
            success: function(response, textStatus, jqXhr) {
                if (response == 1) {
                    button.addClass('-success');
                    setTimeout(function() {
                        button.removeClass('-loading -success');
                        daftplugAdmin.find('.daftplugAdminPage.-activation').addClass('-disabled');
                        daftplugAdmin.find('.daftplugAdminLoader').fadeIn('fast');
                        window.location.hash = '#/overview/';
                        window.location.reload();
                    }, 1500);
                } else {
                    button.addClass('-fail');
                    setTimeout(function() {
                        button.removeClass('-loading -fail');
                    }, 1500);
                    responseText.css({
                        'color': '#FF3A3A',
                        'padding-left': '15px'
                    }).html(response).fadeIn('fast');
                }
            },
            complete: function() {},
            error: function(jqXhr, textStatus, errorThrown) {
                button.addClass('-fail');
                setTimeout(function() {
                    button.removeClass('-loading -fail');
                }, 1500);
                responseText.css({
                    'color': '#FF3A3A',
                    'padding-left': '15px'
                }).html('An unexpected error occurred!').fadeIn('fast');
            }
        });
    });

    // Deactivate license
    daftplugAdmin.find('.daftplugAdminButton.-deactivateLicense').on('submit', function(e) {
        e.preventDefault();
        var self = jQuery(this);
        var action = optionName + '_deactivate_license';
        var nonce = self.attr('data-nonce');

        jQuery.ajax({
            url: ajaxurl,
            dataType: 'text',
            type: 'POST',
            data: {
                action: action,
                nonce: nonce
            },
            beforeSend: function() {
                self.addClass('-loading');
                daftplugAdmin.find('.daftplugAdminButton').not(self).add('.daftplugAdminInputCheckbox.-featuresCheckbox').add('.daftplugAdminMenu').addClass('-disabled');
            },
            success: function(response, textStatus, jqXhr) {
                if (response == 1) {
                    self.addClass('-success');
                    setTimeout(function() {
                        self.removeClass('-loading -success');
                        daftplugAdmin.find('.daftplugAdminHeader').add('.daftplugAdminMain').add('.daftplugAdminFooter').addClass('-disabled');
                        daftplugAdmin.find('.daftplugAdminLoader').fadeIn('fast');
                        window.location.hash = '#/activation/';
                        window.location.reload();
                    }, 1500);
                } else {
                    self.addClass('-fail');
                    setTimeout(function() {
                        self.removeClass('-loading -fail');
                        daftplugAdmin.find('.daftplugAdminButton').not(self).add('.daftplugAdminInputCheckbox.-featuresCheckbox').add('.daftplugAdminMenu').removeClass('-disabled');
                    }, 1500);
                }
            },
            complete: function() {},
            error: function(jqXhr, textStatus, errorThrown) {
                self.addClass('-fail');
                setTimeout(function() {
                    self.removeClass('-loading -fail');
                    daftplugAdmin.find('.daftplugAdminButton').not(self).add('.daftplugAdminInputCheckbox.-featuresCheckbox').add('.daftplugAdminMenu').removeClass('-disabled');
                }, 1500);
            }
        });
    });

    // Submit ticket 
    daftplugAdmin.find('.daftplugAdminSupportTicket_form').on('submit', function(e) {
        e.preventDefault();
        var self = jQuery(this);
        var action = optionName + '_send_ticket';
        var nonce = self.attr('data-nonce');
        var purchaseCode = self.find('#purchaseCode').val();
        var firstName = self.find('#firstName').val();
        var contactEmail = self.find('#contactEmail').val();
        var problemDescription = self.find('#problemDescription').val();
        var attachmentsX = self.find('.daftplugAdminInputTextarea_xicon');
        var attachment1 = self.find('#attachment1').val() || '';
        var attachment2 = self.find('#attachment2').val() || '';
        var attachment3 = self.find('#attachment3').val() || '';
        var attachment4 = self.find('#attachment4').val() || '';
        var attachment5 = self.find('#attachment5').val() || '';
        var wordpressUsername = self.find('#wordpressUsername').val();
        var wordpressPassword = self.find('#wordpressPassword').val();
        var button = self.find('.daftplugAdminButton.-submit');
        var responseText = self.find('.daftplugAdminField_response');

        jQuery.ajax({
            url: ajaxurl,
            dataType: 'text',
            type: 'POST',
            data: {
                action: action,
                nonce: nonce,
                purchaseCode: purchaseCode,
                firstName: firstName,
                contactEmail: contactEmail,
                problemDescription: problemDescription,
                attachment1: attachment1,
                attachment2: attachment2,
                attachment3: attachment3,
                attachment4: attachment4,
                attachment5: attachment5,
                wordpressUsername: wordpressUsername,
                wordpressPassword: wordpressPassword
            },
            beforeSend: function() {
                button.addClass('-loading');
            },
            success: function(response, textStatus, jqXhr) {
                if (response == 1) {
                    self.trigger('reset');
                    attachmentsX.trigger('click');
                    button.addClass('-success');
                    setTimeout(function() {
                        button.removeClass('-loading -success');
                    }, 1500);
                    responseText.css({
                        'color': '#4073FF',
                        'padding-left': '15px'
                    }).html('Thank you! We will send our response as soon as possible to your email address.').fadeIn('fast');
                } else {
                    button.addClass('-fail');
                    setTimeout(function() {
                        button.removeClass('-loading -fail');
                    }, 1500);
                    responseText.css('color', '#FF3A3A').html('Submission failed. Please use the <a target="_blank" href="https://codecanyon.net/user/daftplug#contact">Contact Form</a> found on our Codecanyon profile page instead.').fadeIn('fast');
                }
            },
            complete: function() {},
            error: function(jqXhr, textStatus, errorThrown) {
                button.addClass('-fail');
                setTimeout(function() {
                    button.removeClass('-loading -fail');
                }, 1500);
                responseText.css('color', '#FF3A3A').html('Submission failed. Please use the <a target="_blank" href="https://codecanyon.net/user/daftplug#contact">Contact Form</a> found on our Codecanyon profile page instead.').fadeIn('fast');
            }
        });
    });

    // Save settings
    daftplugAdmin.find('.daftplugAdminSettings_form').on('submit', function(e) {
        e.preventDefault();
        var self = jQuery(this);
        var button = self.find('.daftplugAdminButton.-submit');
        var action = optionName + '_save_settings';
        var nonce = self.attr('data-nonce');
        var settings = self.daftplugSerialize();

        jQuery.ajax({
            url: ajaxurl,
            dataType: 'text',
            type: 'POST',
            data: {
                action: action,
                nonce: nonce,
                settings: settings
            },
            beforeSend: function() {
                button.addClass('-loading');
            },
            success: function(response, textStatus, jqXhr) {
                if (response == 1) {
                    button.addClass('-success');
                    setTimeout(function() {
                        button.removeClass('-loading -success');
                    }, 1500);
                } else {
                    button.addClass('-fail');
                    setTimeout(function() {
                        button.removeClass('-loading -fail');
                    }, 1500);
                }
            },
            complete: function() {
            },
            error: function(jqXhr, textStatus, errorThrown) {
                button.addClass('-fail');
                setTimeout(function() {
                    button.removeClass('-loading -fail');
                }, 1500);
                console.log(jqXhr);
            }
        });
    });

    // Export settings
    daftplugAdmin.find('.daftplugAdminButton.-settingsExport').on('click tap', function(e) {
        e.preventDefault();
        var self = jQuery(this);
        var action = optionName + '_export_settings';
        self.addClass('-loading');
        setTimeout(function() {
            self.addClass('-success');
            window.location.href = objectName.adminUrl+'admin-post.php?action='+action;
        }, 700);
        setTimeout(function() {
            self.removeClass('-loading -success');
        }, 2200);
    });

     // Import settings
     daftplugAdmin.find('.daftplugAdminSettingsImport_form').each(function(e) {
        var self = jQuery(this);
        var button = self.find('.daftplugAdminButton.-submit');
        var action = optionName + '_import_settings';
        var nonce = self.attr('data-nonce');
        var settingsFileInput = document.getElementById('settingsFile');

        button.on('click', function(e) {
            e.preventDefault();
            settingsFileInput.click();
        });

        settingsFileInput.addEventListener('change', function() {
            if (settingsFileInput.files.length > 0) {
                var fileReader = new FileReader();
                fileReader.readAsText(settingsFileInput.files[0]);
                fileReader.addEventListener('load', function() {
                    jQuery.ajax({
                        url: ajaxurl,
                        dataType: 'text',
                        type: 'POST',
                        data: {
                            action: action,
                            nonce: nonce,
                            settings: fileReader.result,
                        },
                        beforeSend: function() {
                            button.addClass('-loading');
                            daftplugAdmin.find('.daftplugAdminButton').not(button).add('.daftplugAdminMenu').addClass('-disabled');
                        },
                        success: function(response, textStatus, jqXhr) {
                            var response = JSON.parse(response);
                            if (response.success) {
                                button.addClass('-success');
                                setTimeout(function() {
                                    button.removeClass('-loading -success');
                                    daftplugAdmin.find('.daftplugAdminHeader').add('.daftplugAdminMain').add('.daftplugAdminFooter').addClass('-disabled');
                                    daftplugAdmin.find('.daftplugAdminLoader').fadeIn('fast');
                                    window.location.reload();
                                }, 1500);
                            } else {
                                button.addClass('-fail');
                                setTimeout(function() {
                                    button.removeClass('-loading -fail');
                                    daftplugAdmin.find('.daftplugAdminButton').not(button).add('.daftplugAdminMenu').removeClass('-disabled');
                                }, 1500);
                            }
                        },
                        complete: function() {
                        },
                        error: function(jqXhr, textStatus, errorThrown) {
                            button.addClass('-fail');
                            setTimeout(function() {
                                button.removeClass('-loading -fail');
                                daftplugAdmin.find('.daftplugAdminButton').not(button).add('.daftplugAdminMenu').removeClass('-disabled');
                            }, 1500);
                            console.log(jqXhr);
                        }
                    });
                });
            }
        });
    });

    // Reset settings
    daftplugAdmin.find('.daftplugAdminButton.-settingsReset').on('submit', function(e) {
        e.preventDefault();
        var self = jQuery(this);
        var action = optionName + '_reset_settings';
        var nonce = self.attr('data-nonce');

        jQuery.ajax({
            url: ajaxurl,
            dataType: 'text',
            type: 'POST',
            data: {
                action: action,
                nonce: nonce
            },
            beforeSend: function() {
                self.addClass('-loading');
                daftplugAdmin.find('.daftplugAdminButton').not(self).add('.daftplugAdminMenu').addClass('-disabled');
            },
            success: function(response, textStatus, jqXhr) {
                var response = JSON.parse(response);
                if (response.success) {
                    self.addClass('-success');
                    setTimeout(function() {
                        self.removeClass('-loading -success');
                        daftplugAdmin.find('.daftplugAdminHeader').add('.daftplugAdminMain').add('.daftplugAdminFooter').addClass('-disabled');
                        daftplugAdmin.find('.daftplugAdminLoader').fadeIn('fast');
                        window.location.reload();
                    }, 1500);
                } else {
                    self.addClass('-fail');
                    setTimeout(function() {
                        self.removeClass('-loading -fail');
                        daftplugAdmin.find('.daftplugAdminButton').not(self).add('.daftplugAdminMenu').removeClass('-disabled');
                    }, 1500);
                }
            },
            complete: function() {},
            error: function(jqXhr, textStatus, errorThrown) {
                self.addClass('-fail');
                setTimeout(function() {
                    self.removeClass('-loading -fail');
                    daftplugAdmin.find('.daftplugAdminButton').not(self).add('.daftplugAdminMenu').removeClass('-disabled');
                }, 1500);
            }
        });
    });

    // Save plugin features settings
    daftplugAdmin.find('.daftplugAdminInputCheckbox.-featuresCheckbox').each(function(e) {
        var self = jQuery(this);
        var field = self.find('.daftplugAdminInputCheckbox_field');
        var fieldset = jQuery('.daftplugAdminPluginFeatures');

        field.on('click', function(e) {
            e.preventDefault();
            var action = optionName + '_save_settings';
            var nonce = self.attr('data-nonce');
            var settings = fieldset.daftplugSerialize();

            jQuery.ajax({
                url: ajaxurl,
                dataType: 'text',
                type: 'POST',
                data: {
                    action: action,
                    nonce: nonce,
                    settings: settings
                },
                beforeSend: function() {
                    self.addClass('-loading');
                    daftplugAdmin.find('.daftplugAdminInputCheckbox.-featuresCheckbox').not(self).parent().add('.daftplugAdminButton').add('.daftplugAdminMenu').addClass('-disabled');
                },
                success: function(response, textStatus, jqXhr) {
                    if (response == 1) {
	                    setTimeout(function() {
	                        self.removeClass('-loading');
	                        daftplugAdmin.find('.daftplugAdminInputCheckbox.-featuresCheckbox').not(self).parent().removeClass('-disabled');
	                        if (field.is(':checked')) {
	                        	field.prop('checked', false);
	                        } else {
	                        	field.prop('checked', true);
	                        }
	                        daftplugAdmin.find('.daftplugAdminHeader').add('.daftplugAdminMain').add('.daftplugAdminFooter').addClass('-disabled');
                            daftplugAdmin.find('.daftplugAdminLoader').fadeIn('fast');
	                        window.location.reload();
	                    }, 1500);
                    } else {
	                    setTimeout(function() {
	                        self.removeClass('-loading');
	                        daftplugAdmin.find('.daftplugAdminInputCheckbox.-featuresCheckbox').not(self).parent().add('.daftplugAdminButton').add('.daftplugAdminMenu').removeClass('-disabled');
	                        if (field.is(':checked')) {
	                        	field.prop('checked', true);
	                        } else {
	                        	field.prop('checked', false);
	                        }
                        }, 1500);
                    }
                },
                complete: function() {
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    setTimeout(function() {
                        self.removeClass('-loading');
                        daftplugAdmin.find('.daftplugAdminInputCheckbox.-featuresCheckbox').not(self).parent().add('.daftplugAdminButton').add('.daftplugAdminMenu').removeClass('-disabled');
                        if (field.is(':checked')) {
                        	field.prop('checked', true);
                        } else {
                        	field.prop('checked', false);
                        }
                    }, 1500);
                }
            });
        });
    });
    
	// Handle review modal
	daftplugAdmin.find('[data-popup="reviewModal"]').each(function(e) {
		var self = jQuery(this);
		var secondsSpent = Number(localStorage.getItem('secondsSpent'));
		setInterval(function() {
		    localStorage.setItem('secondsSpent', ++secondsSpent);
		    if (secondsSpent == 400) {
		        self.addClass('-active');
		    }
		}, 1000);
	});

    // Handle get app notice
    daftplugAdmin.find('.daftplugAdminPage_content.-getAppNoticeTable').each(function(e) {
        var self = jQuery(this);
        var getAppNotice = self.find('.daftplugAdminGetAppNotice');
        var getAppNoticeButton = getAppNotice.find('.daftplugAdminButton.-notice');
        var pricingTable = self.find('.daftplugAdminPricingTable');
        var timeout;

        getAppNoticeButton.on('click tap', function(e) {
            getAppNotice.slideUp('fast');
        });

        pricingTable.on('mouseleave', function(e) {
            timeout = setTimeout(function(e) {
                getAppNotice.slideDown('fast');
            }, 10000);
        }).on('mouseenter', function(e) {
            clearTimeout(timeout);
        });
    });

    // Handle get app paypal buttons
    daftplugAdmin.find('.daftplugAdminPaypalButton').each(function(e) {
        var self = jQuery(this);
        var buttonsContainer = self.parent();
        var tooltip = buttonsContainer.attr('data-tooltip');
        var target = self.attr('data-target');
        var price = self.attr('data-price');
        var deliveryEmailField = buttonsContainer.prev().find('#deliveryEmail');
        var responseText = buttonsContainer.next();

        deliveryEmailField.on('input change paste propertychange', function(e) {
            var field = jQuery(this);

            if (field.get(0).validity['valid']) {
                buttonsContainer.removeAttr('data-tooltip');
                self.removeClass('-disabled');
                field.parent().removeClass('-invalid');
            } else {
                buttonsContainer.attr('data-tooltip', tooltip);
                self.addClass('-disabled');
                field.parent().addClass('-invalid');
            }

            if (!field.val()) {
                field.parent().removeClass('-invalid');
            }
        });        

        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'paypal',
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        'custom_id': 'Website - '+objectName.homeUrl+' & Delivery Email - '+deliveryEmailField.val(),
                        'description': 'PWA to '+target.charAt(0).toUpperCase() + target.slice(1)+' App',
                        'amount': {
                            'currency_code': 'USD',
                            'value': price
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(orderData) {
                    buttonsContainer.hide();
                    responseText.css('color', '#4073FF').html('Thank you for the payment! We are starting to create app package for you and will send it as soon as possible.').fadeIn('fast');
                    setTimeout(function() {
                        responseText.hide();
                        buttonsContainer.fadeIn('fast');
                    }, 5000);
                });
            },
            onError: function(err) {
                buttonsContainer.hide();
                responseText.css('color', '#FF3A3A').html('Payment failed! Please try again or contact us on support@daftplug.com').fadeIn('fast');
                setTimeout(function() {
                    responseText.hide();
                    buttonsContainer.fadeIn('fast');
                }, 5000);
                console.log(err);
            }
        }).render(self.get(0));
    });

    // Handle settings search
    daftplugAdmin.find('.daftplugAdminHeader_search').each(function(e) {
        var self = jQuery(this);
        var button = self.find('.daftplugAdminButton.-search');
        var icon = self.find('.daftplugAdminHeader_icon');
        var field = self.find('.daftplugAdminHeader_field');
        var tooltip = button.attr('data-tooltip');
        var searchResults = self.find('.daftplugAdminHeader_results');
        var fieldsets = daftplugAdmin.find('.daftplugAdminSettings').find('.daftplugAdminFieldset[id]');

        fieldsets.each(function(e) {
            var self = jQuery(this);
            var id = self.attr('id');
            var title = self.find('.daftplugAdminFieldset_title').text().replace('beta', '').replace('new', '');
            var pageId = self.closest('.daftplugAdminPage').attr('data-page');
            var pageTitle = self.closest('.daftplugAdminPage').attr('data-title');
            var subpageId = self.closest('.daftplugAdminPage_subpage').attr('data-subpage');
            var subpageTitle = self.closest('.daftplugAdminPage_subpage').attr('data-title');

            searchResults.prepend(`<a class="daftplugAdminHeader_item" href="#/${pageId}-${subpageId}/" data-page="${pageId}" data-subpage="${subpageId}" data-targetid="${id}">${pageTitle} > ${subpageTitle} > ${title}</a>`);
        });
        
        button.on('click tap', function(e) {
            if (button.hasClass('-open')) {
                if (field.val()) {
                    field.val('').focus();
                    searchResults.slideUp('fast');
                } else {
                    searchResults.slideUp('fast', function(e) {
                        field.removeClass('-open');
                        button.removeClass('-open').attr('data-tooltip', tooltip);
                        icon.find('use').attr('href', '#iconSearch');
                    });
                }
            } else {
                field.addClass('-open').focus();
                button.addClass('-open').removeAttr('data-tooltip').find('.daftplugAdminTooltip').remove();
                icon.find('use').attr('href', '#iconX');
            }
        });

        daftplugAdmin.on('click tap', function(e) {
            var target = jQuery(e.target);
            var button = self.find('.daftplugAdminButton.-search');
            if ((!target.closest('.daftplugAdminButton.-search').length && !target.closest('.daftplugAdminHeader_field').length) && button.hasClass('-open')) {
                searchResults.slideUp('fast', function(e) {
                    field.removeClass('-open').val('');
                    button.removeClass('-open').attr('data-tooltip', tooltip);
                    icon.find('use').attr('href', '#iconSearch');
                });
            }
        });

        field.on('change input paste', function(e) {
            if (field.val().length > 2) {
                var searchPhrase = jQuery.trim(field.val()).replace(/ +/g, ' ').toLowerCase();
                searchResults.slideDown('fast').find('.daftplugAdminHeader_item').show().filter(function(e) {
                    var a = jQuery(this).text().replace(/\s+/g, ' ').toLowerCase();
                    return -1 === a.indexOf(searchPhrase)
                }).hide();

                if (!searchResults.find('.daftplugAdminHeader_item').is(':visible')) {
                    searchResults.find('.daftplugAdminHeader_notfound').show();
                } else {
                    searchResults.find('.daftplugAdminHeader_notfound').hide();
                }
            } else {
                searchResults.slideUp('fast');
            }
        });

        daftplugAdmin.on('click tap', '.daftplugAdminHeader_item', function(e) {
            var self = jQuery(this);
            var targetId = self.attr('data-targetid');
            setTimeout(function() {
                jQuery('#'+targetId).get(0).scrollIntoView({behavior: 'smooth', block: 'center'});
            }, 10);
        });
    });

	// Helpers
	jQuery.fn.daftplugSerialize = function() {
	    var data = {};
	    jQuery.each(this.serializeArray(), function() {
            if (data[this.name]) {
                if (!data[this.name].push) {
                    data[this.name] = [data[this.name]];
                }
                data[this.name].push(this.value || '');
            } else if (this.name.includes(']')) {
                var nestedArray = this.name.split("[").map(s => s.replace(']', ''));
                var headName = nestedArray[0];
                nestedArray.shift();
                var nestedValue = nestedArray.reduceRight((all, item) => ({[item]: all}), this.value);
                data[headName] = nestedValue;
            } else {
                data[this.name] = this.value || '';
            }
	    });
	    jQuery.each(jQuery('input[type=radio], input[type=checkbox]', this), function() {
	        if (!data.hasOwnProperty(this.name)) {
	            data[this.name] = 'off';
	        }
	    });
	    return JSON.stringify(data);
	};
});