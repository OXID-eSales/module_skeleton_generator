/**
 * jQuery widget for Module Generation form.
 * Handles data validation, edited module data loading, etc.
 */
jQuery.widget(
    'oxpsmodulegenerator.wizard',
    {
        options: {
            moduleNameValidationUrl: '',
            extendClassesValidationUrl: '',

            notificationSuccessText: '',
            notificationErrorText: '',
            notificationErrorExcludedModuleText: '',
            notificationWarningText: '',
            notificationValidClassesText: '',

            notificationExistingClasses: '',
            notificationExistingControllers: '',
            notificationExistingModels: '',
            notificationExistingLists: '',
            notificationExistingWidgets: '',
            notificationExistingBlocks: '',
            notificationExistingSettings: '',

            notificationSlideDownSpeed: 800
        },

        /**
         * List of excluded modules from Edit Mode.
         */
        _excludedModuleNames: [
            'ModuleGenerator'
        ],

        _moduleNameSelector: "input[name='modulegenerator_module_name']",
        _moduleClassesSelector: "textarea[name='modulegenerator_extend_classes']",
        _moduleControllersSelector: "textarea[name='modulegenerator_controllers']",
        _moduleModelsSelector: "textarea[name='modulegenerator_models']",
        _moduleListsSelector: "textarea[name='modulegenerator_lists']",
        _moduleWidgetsSelector: "textarea[name='modulegenerator_widgets']",
        _moduleBlocksSelector: "textarea[name='modulegenerator_blocks']",
        _moduleSettingsNameSelector: "input[name^='modulegenerator_settings']",
        _moduleSettingsAllInputSelector: "[name^='modulegenerator_settings[']",

        _moduleClassesSelectorNoticeDiv: ".component-existing-classes",
        _moduleControllersSelectorNoticeDiv: ".component-existing-controllers",
        _moduleModelsSelectorNoticeDiv: ".component-existing-models",
        _moduleListsSelectorNoticeDiv: ".component-existing-lists",
        _moduleWidgetsSelectorNoticeDiv: ".component-existing-widgets",
        _moduleBlocksSelectorNoticeDiv: ".component-existing-blocks",
        _moduleSettingsNameSelectorNoticeDiv: ".component-existing-settings",

        _cssEditModeSelectorClass: ".editMode",
        _cssNoticeSelectorClass: ".notice",
        _cssAddSettingsLineButtonId: "#addNewSettingsLine",
        _cssSettingsBodyId: "#settingsBody",
        _cssSettingsLineClass: ".settingsLine",
        _cssSettingsLineId: "settingsLine",
        _cssRemoveSettingsLineButtonClass: ".removeLineButton",

        /**
         * Widget Constructor
         */
        _create: function () {
            this._bindEvents();
        },

        /**
         * Bind all events required for input fields with different logic.
         *
         * TODO: Would be nice to split it into several methods:
         * TODO: e.g. move out module name event, setting name and setting button events.
         */
        _bindEvents: function () {
            var self = this;


            this._clearFormInputValuesOnSuccessfulSubmit();

            // From jQuery 1.7+ live() is deprecated and should be changed to on() method after jQuery version update.
            jQuery(this._moduleNameSelector).live('keyup change', function () {
                if (self._isEmptyField(this)) {
                    self._hideNotification(this);
                    self._hideExistingComponentNotification();
                }
                else if (self._isExcludedName(this)) {
                    self._showNotification(this, 'notice', self.options.notificationErrorExcludedModuleText);
                    self._hideExistingComponentNotification();
                }
                else if (self._validateCamelCaseName(this)) {
                    self._requestModuleNameJsonResponse(this);
                } else {
                    self._showNotification(this, 'error', self.options.notificationErrorText);
                    self._hideExistingComponentNotification();
                }
            });

            jQuery(this._moduleClassesSelector).live('keyup', function () {
                self._requestExtendClassesJsonResponse(this);
            });

            jQuery(this._moduleControllersSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleModelsSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleListsSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleWidgetsSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleBlocksSelector).live('keyup', function () {
                self._validateBlocksFieldEntry(this);
            });

            jQuery(this._moduleSettingsNameSelector).live('keyup', function () {
                if (self._isEmptyField(this)) {
                    jQuery(this).removeClass().addClass('default-settings-color');
                }
                else if (self._camelCaseRegex(jQuery(this).val())) {
                    jQuery(this).removeClass().addClass('correct-settings-color');
                } else {
                    jQuery(this).removeClass().addClass('invalid-settings-color');
                }
            });

            jQuery(this._cssAddSettingsLineButtonId).live('click', function () {
                // Get last settings line's ID
                var sLastLineId = jQuery(self._cssSettingsLineClass + ':last').attr('id');

                // Subtract text leaving only number
                var iCleanId = parseInt(sLastLineId.replace(self._cssSettingsLineId, ''));

                // Adding +1 to last ID for new line
                iCleanId++;

                // Clone, replace unique id, append below and clear existing values from last line.
                jQuery(self._cssSettingsLineClass + ':last')
                    .clone()
                    .attr('id', self._cssSettingsLineId + iCleanId)
                    .find(self._moduleSettingsAllInputSelector)
                    .each(function () {
                        jQuery(this).attr('name', jQuery(this).attr("name").replace(/\d+/, iCleanId))
                    }).end()
                    .find(self._cssRemoveSettingsLineButtonClass).remove().end()
                    .appendTo(self._cssSettingsBodyId)
                    .append('<input type="button" value="REMOVE" class="removeLineButton" id="' + iCleanId + '">')
                    .find("input[type='text'], textarea").val('').removeClass()
                ;
            });

            jQuery(self._cssRemoveSettingsLineButtonClass).live('click', function () {
                jQuery(this).closest('tr').remove();
            });
        },

        /**
         * Return AJAX response with metadata depending on module name entered.
         *
         * @param {object} oElement
         */
        _requestModuleNameJsonResponse: function (oElement) {
            var self = this;

            jQuery.ajax({
                dataType: 'json',
                type: 'post',
                url: self.options.moduleNameValidationUrl,
                data: {moduleName: jQuery(oElement).val()},
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        self._showModuleNameHtmlResponse(data);
                    } else {
                        self._hideExistingComponentNotification();
                    }
                },
                error: function () {
                    self._hideExistingComponentNotification();
                }
            });
        },

        /**
         * Method for showing notifications with existing module metadata.
         *
         * @param {object} oData
         */
        _showModuleNameHtmlResponse: function (oData) {
            var self = this;

            var sExtendClasses = self._buildHtmlResponse(oData['aExtendClasses'], true, '<br />');
            var sNewControllers = self._buildHtmlResponse(oData['aNewControllers'], false, '<br />');
            var sNewModels = self._buildHtmlResponse(oData['aNewModels'], false, '<br />');
            var sNewLists = self._buildHtmlResponse(oData['aNewLists'], false, '<br />');
            var sNewWidgets = self._buildHtmlResponse(oData['aNewWidgets'], false, '<br />');
            var sNewBlocks = self._buildSelectiveHtmlResponse(oData['aNewBlocks'], true);
            var sNewSettings = self._buildSelectiveHtmlResponse(oData['aModuleSettings'], false);

            jQuery(self._cssEditModeSelectorClass).slideDown(self.options.notificationSlideDownSpeed);

            if (sExtendClasses) {
                jQuery(self._moduleClassesSelectorNoticeDiv)
                    .html(self.options.notificationExistingClasses + sExtendClasses)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewControllers) {
                jQuery(self._moduleControllersSelectorNoticeDiv)
                    .html(self.options.notificationExistingControllers + sNewControllers)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewModels) {
                jQuery(self._moduleModelsSelectorNoticeDiv)
                    .html(self.options.notificationExistingModels + sNewModels)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewLists) {
                jQuery(self._moduleListsSelectorNoticeDiv)
                    .html(self.options.notificationExistingLists + sNewLists)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewWidgets) {
                jQuery(self._moduleWidgetsSelectorNoticeDiv)
                    .html(self.options.notificationExistingWidgets + sNewWidgets)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewBlocks) {
                jQuery(self._moduleBlocksSelectorNoticeDiv)
                    .html(self.options.notificationExistingBlocks + sNewBlocks)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewSettings) {
                jQuery(self._moduleSettingsNameSelectorNoticeDiv)
                    .html(self.options.notificationExistingSettings + sNewSettings)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }
        },

        /**
         * Return JSON response with extendable classes if exist.
         *
         * @param {object} oElement
         */
        _requestExtendClassesJsonResponse: function (oElement) {
            var self = this;

            jQuery.ajax({
                dataType: 'json',
                type: 'post',
                url: self.options.extendClassesValidationUrl,
                data: {extendClasses: jQuery(oElement).val()},
                success: function (data) {
                    if (null !== data) {
                        self._showExtendClassesHtmlResponse(oElement, data);
                    }
                }
            });
        },

        /**
         * @param {object} oElement
         * @param {object} oData
         */
        _showExtendClassesHtmlResponse: function (oElement, oData) {
            if (this._isEmptyField(oElement)) {
                this._hideNotification(oElement);
            } else {
                var response = this.options.notificationValidClassesText + this._buildHtmlResponse(oData, true, ', ');

                this._showNotification(oElement, 'info', response);
            }
        },

        /**
         * Build proper HTML response depending on requested object and notification space requirement in between.
         *
         * @param {object} oMetaObject
         * @param {boolean} blKeys
         * @param {string} sSpaceType
         *
         * @returns {string}
         */
        _buildHtmlResponse: function (oMetaObject, blKeys, sSpaceType) {
            var aObjectData = [];
            var aFormattedValue = [];

            if (true === blKeys) {
                aObjectData = Object.keys(oMetaObject);
            } else {
                aObjectData = Object.values(oMetaObject);
            }

            for (var i in aObjectData) {
                aFormattedValue.push(aObjectData[i]);
            }

            return aFormattedValue.join(sSpaceType);
        },

        /**
         * Different logic of Html Response required for Settings and Blocks objects of Metadata.
         *
         * @param {object} oMetaObject
         * @param {boolean} blBlock
         *
         * @returns {string}
         */
        _buildSelectiveHtmlResponse: function (oMetaObject, blBlock) {
            var sFormattedValue = '';
            var aObjectData = Object.keys(oMetaObject);

            for (var i in aObjectData) {
                if (blBlock) {
                    sFormattedValue += oMetaObject[aObjectData[i]]['block']
                        + "@"
                        + oMetaObject[aObjectData[i]]['template']
                        + "<br />";
                } else {
                    sFormattedValue += oMetaObject[aObjectData[i]]['name']
                        + " | "
                        + oMetaObject[aObjectData[i]]['type']
                        + " | "
                        + oMetaObject[aObjectData[i]]['value']
                        + "<br />";
                }
            }

            return sFormattedValue;
        },

        /**
         * @param {object} oElement
         *
         * @returns {boolean}
         */
        _validateCamelCaseName: function (oElement) {
            return this._showCorrectNotification(oElement, '_camelCaseRegex');
        },

        /**
         * Simple block field validation.
         *
         * @param {object} oElement
         *
         * @returns {boolean}
         */
        _validateBlocksFieldEntry: function (oElement) {
            return this._showCorrectNotification(oElement, '_blocksRegex');
        },

        /**
         * Show notification depending on various states of input field.
         *
         * TODO: This logic could be refactored to smaller parts
         *
         * @param oElement
         * @param sRegexFunction
         *
         * @returns {boolean}
         */
        _showCorrectNotification: function (oElement, sRegexFunction) {
            var self = this;
            var sEnteredInput = jQuery(oElement).val();

            if (self._isEmptyField(oElement)) {
                self._hideNotification(oElement);
            } else if ((self._countNewLines(sEnteredInput)) > 0) {
                if (Object.values(self._splitNewLines(sEnteredInput, sRegexFunction)).indexOf(false) !== -1) {
                    self._showNotification(oElement, 'error', self.options.notificationErrorText);
                } else {
                    self._showNotification(oElement, 'success', self.options.notificationSuccessText);

                    return true;
                }
            } else if (self[sRegexFunction](sEnteredInput)) {
                self._showNotification(oElement, 'success', self.options.notificationSuccessText);

                return true;
            } else {
                self._showNotification(oElement, 'error', self.options.notificationErrorText);
            }
        },

        /**
         * Validate if input field or textarea is empty
         *
         * @param oElement
         */
        _isEmptyField: function (oElement) {
            if ('' === jQuery(oElement).val()) {
                return true;
            }
        },

        /**
         * @param oElement
         *
         * @returns {boolean}
         */
        _isExcludedName: function (oElement) {
            if (this._inArrayIn(jQuery(oElement).val(), this._excludedModuleNames, 0) !== -1) {
                return true;
            }
        },

        /**
         * @param {object} oElement
         * @param {string} sNoticeType
         * @param {string} sNoticeText
         */
        _showNotification: function (oElement, sNoticeType, sNoticeText) {
            jQuery(oElement).siblings(this._cssNoticeSelectorClass)
                .fadeIn(1000)
                .attr('class', 'notice')
                .addClass('notice-' + sNoticeType)
                .text(sNoticeText)
            ;
        },

        /**
         * @param oElement
         */
        _hideNotification: function (oElement) {
            jQuery(oElement).siblings(this._cssNoticeSelectorClass).fadeOut(500);
        },

        /**
         * Same backend validation in \oxpsModuleGeneratorValidator::validateCamelCaseName
         *
         * @param sInput
         *
         * @returns {boolean}
         */
        _camelCaseRegex: function (sInput) {
            return /^([A-Z])([a-zA-Z0-9]{1,63})$/.test(sInput);
        },

        /**
         * Checks if @ exists inside string and allows any word character including "_", "/", "." and "-" symbols from
         * both sides.
         *
         * @param sInput
         *
         * @returns {boolean}
         */
        _blocksRegex: function (sInput) {
            return /^([\w\/\-.]+)(@)([\w\/\-.]+)$/.test(sInput);
        },

        /**
         * Split lines of entered data if more than one component name have been entered.
         *
         * @param sEnteredInput
         * @param {string} regexFunction
         *
         * @returns {object}
         */
        _splitNewLines: function (sEnteredInput, regexFunction) {
            var self = this;
            var aValidatedInput = {};
            var aSplitInput = sEnteredInput.split(/\n/);

            aSplitInput.forEach(function (sInput) {
                if (sInput.trim() !== '') {
                    aValidatedInput[sInput] = !(!(self[regexFunction](sInput)));
                }
            });

            return aValidatedInput;
        },

        /**
         * @param sEnteredInput
         *
         * @returns {int}
         */
        _countNewLines: function (sEnteredInput) {
            return (sEnteredInput.match(/\n/g) || []).length;
        },

        /**
         * Hide notifications with metadada if module name was not found.
         */
        _hideExistingComponentNotification: function () {
            var self = this;

            jQuery(self._cssEditModeSelectorClass).slideUp();
            jQuery('div').filter(function () {
                return this.className.match(/\bcomponent-existing/);
            }).slideUp();

        },

        /**
         * Case insensitive $.inArray (http://api.jquery.com/jquery.inarray/)
         * $.inArrayIn(value, array [, fromIndex])
         *
         * @param {string}  elem        The value to search for
         * @param {Array}   arr         An array through which to search.
         * @param {int}     i           The index of the array at which to begin the search. (Default: 0)
         *
         * @returns {int}
         */
        _inArrayIn: function (elem, arr, i) {
            // not looking for a string anyways, use default method
            if (typeof elem !== 'string') {
                return jQuery.inArray.apply(this, arguments);
            }
            // check if array is populated
            if (arr) {
                var len = arr.length;
                i = i ? (i < 0 ? Math.max(0, len + i) : i) : 0;
                elem = elem.toLowerCase();
                for (i; i < len; i++) {
                    if (i in arr && arr[i].toLowerCase() === elem) {
                        return i;
                    }
                }
            }
            // stick with inArray/indexOf and return -1 on no match
            return -1;
        },

        /**
         * Check for 'messagebox' div (appears on form submit success) and clear input fields if true.
         */
        _clearFormInputValuesOnSuccessfulSubmit: function () {
            if (jQuery('.messagebox').length) {
                jQuery(':input', '#modulegenerator_form')
                    .not(":button, :submit, :reset, [name='modulegenerator_module_name']")
                    // .removeAttr('checked')
                    // .removeAttr('selected')
                    .val('');
            }
        }
    }
);