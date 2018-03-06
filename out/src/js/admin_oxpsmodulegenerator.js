/**
 * This file is part of OXID Module Skeleton Generator module.
 *
 * OXID Module Skeleton Generator module is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * OXID Module Skeleton Generator module is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Module Skeleton Generator module.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category      module
 * @package       ModuleGenerator
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 *
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
            notificationBlockErrorText: '',
            notificationErrorExcludedModuleText: '',
            notificationWarningText: '',
            notificationValidClassesText: '',
            notificationErrorTextOfRepeating: '',

            notificationExistingClasses: '',
            notificationExistingControllers: '',
            notificationExistingModels: '',
            notificationExistingLists: '',
            notificationExistingWidgets: '',
            notificationExistingBlocks: '',
            notificationExistingSettings: '',

            notificationExistingSettingsName: '',
            notificationExistingSettingsType: '',
            notificationExistingSettingsValue: '',

            notificationSlideDownSpeed: 500
        },

        /**
         * List of excluded modules from Edit Mode.
         */
        _excludedModuleNames: [
            'ModuleGenerator'
        ],
        _errorMessageExamples:[
            {
                element: 'modulegenerator_module_name',
                example: document.querySelector('.notification-error-examples').dataset.moduleName
            },
            {
                element: 'modulegenerator_controllers',
                example: document.querySelector('.notification-error-examples').dataset.controllerName
            },
            {
                element: 'modulegenerator_models',
                example: document.querySelector('.notification-error-examples').dataset.modelName
            },
            {
                element: 'modulegenerator_lists',
                example: document.querySelector('.notification-error-examples').dataset.listName
            },
            {
                element: 'modulegenerator_widgets',
                example: document.querySelector('.notification-error-examples').dataset.widgetName
            },
            {
                element: 'modulegenerator_blocks',
                example: document.querySelector('.notification-error-examples').dataset.blockName
            },
            {
                element: 'modulegenerator_settings',
                example: document.querySelector('.notification-error-examples').dataset.settingName
            }
        ],
        _errorText: '',
        _moduleNameSelector: "input[name='modulegenerator_module_name']",
        _moduleClassesSelector: "textarea[name='modulegenerator_extend_classes']",
        _moduleControllersSelector: "textarea[name='modulegenerator_controllers']",
        _moduleModelsSelector: "textarea[name='modulegenerator_models']",
        _moduleListsSelector: "textarea[name='modulegenerator_lists']",
        _moduleWidgetsSelector: "textarea[name='modulegenerator_widgets']",
        _moduleBlocksSelector: "textarea[name='modulegenerator_blocks']",
        _moduleSettingsNameSelector: "input[name^='modulegenerator_settings']",
        _moduleSettingsAllInputSelector: "[name^='modulegenerator_settings[']",
        _moduleSubmitButton: "input[name^='modulegenerator_submit']",

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
        _settingsRowNumber: 0,

        /**
         * Widget Constructor
         */
        _create: function () {
            this._bindEvents();
        },

        /**
         * Bind all events required for input fields with different logic.
         */
        _bindEvents: function () {
            var self = this;
            // Trigger Edit Mode if entered module name exists.
            self._validateEnteredModuleName(self._moduleNameSelector);

            // Clear input values on successful Module (re)generation.
            this._clearFormInputValuesOnSuccessfulSubmit();
            this._moduleSettingsButtonListener();
            this._validateComponentName();
            this._validateSettingsName();
        },

        /**
         * this._errorText gets error message which should be render if user value is invalid.
         * This message combines from two strings: translatable error message text and example by field which one is invalid
         *
         * Check if entered module exists and show appropriate notifications
         *
         * @param oElement
         */
        _validateEnteredModuleName: function (oElement) {
            if (this._isEmptyField(oElement)) {
                this._hideNotification(oElement);
                this._hideExistingComponentNotification();
            } else if (this._validateCamelCaseName(oElement)) {
                // Check if entered module name is in excluded array
                if (this._isExcludedName(oElement)) {
                    this._showNotification(oElement, 'notice', this.options.notificationErrorExcludedModuleText);
                    this._hideExistingComponentNotification();
                } else {
                    this._requestModuleNameJsonResponse(oElement);
                }
            } else {
                //combines two strings: translatable error text and camelCase examples by field
                this._errorText = this.options.notificationErrorText + ' ' + this._errorMessageExamples.find(function(variable) {
                    return variable.element === jQuery(oElement).attr('name');
                }).example;
                this._showNotification(oElement, 'error', this._errorText);
                this._hideExistingComponentNotification();
            }
        },

        /**
         * Listen for click action to add or remove new Settings line of input fields.
         */
        _moduleSettingsButtonListener: function () {
            var self = this;

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
                    .find("input[type='text'], textarea").val('')
                ;

                //Using to hide error messages.
                var notice = document.querySelectorAll('.js-notice-block');
                jQuery(notice[iCleanId])
                    .removeClass()
                    .addClass('notice notice-hidden js-notice-block')
                    .text('')
                ;
                notice[iCleanId].style.display = "none";
            });

            jQuery(self._cssRemoveSettingsLineButtonClass).live('click', function () {
                jQuery(this).closest('tr').remove();
            });
        },

        _getArrayFromObject:function(obj){
            var array = [];
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    array.push(obj[key]);
                }
            }

            return array;
        },

        _orExist:function(array, inputValue){
            return array.find(function(variable) {
                return (variable === inputValue)
            });
        },

        //TODO: make button disabled, clear not needed methods
        _validateEnteredValueFromRepeat: function(oData, oElement){
            var namesArray = this._getArrayFromObject(oData);
            var submitButton = document.querySelector(this._moduleSubmitButton);

            if ( typeof this._orExist(namesArray, jQuery(oElement).val()) !== 'undefined') {
                submitButton.disabled = true;
                this._validateRepeatInput(oElement, 'red', 'red');
                this._showNotification(oElement, 'error', this.options.notificationErrorTextOfRepeating);
            } else {
                submitButton.disabled = false;
                this._validateRepeatInput(oElement, '#808080', 'black');
            }

        },

        _validateRepeatInput: function(oElement, borderColor, textColor){
            jQuery(oElement)
                .css('border-color', borderColor)
                .css('color', textColor)
            ;
        },

        /**
         * Validate entered various components' names.
         * (Module name, extended class, controller, model, list, widget and block)
         */
        _validateComponentName: function (oData) {
            var self = this;
            // From jQuery 1.7+ live() is deprecated and should be changed to on() method after jQuery version update.
            jQuery(this._moduleNameSelector).live('keyup change', function () {
                self._validateEnteredModuleName(this);
            });

            jQuery(this._moduleClassesSelector).live('keyup', function () {
                self._requestExtendClassesJsonResponse(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aExtendClasses'], this);
                }
            });

            jQuery(this._moduleControllersSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aNewControllers'], this);
                }
            });

            jQuery(this._moduleModelsSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aNewModels'], this);
                }
            });

            jQuery(this._moduleListsSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aNewLists'], this);
                }
            });

            jQuery(this._moduleWidgetsSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aNewWidgets'], this);
                }
            });

            jQuery(this._moduleBlocksSelector).live('keyup', function () {
                self._validateBlocksFieldEntry(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aNewBlocks'], this);
                }
            });
            jQuery(this._moduleSettingsNameSelector).live('keyup', function () {
                self._validateCamelCaseName(this);
                if (typeof oData !== 'undefined') {
                    self._validateEnteredValueFromRepeat(oData['aModuleSettings'], this);
                }
            });
        },

        /**
         * Validate entered Settings name.
         */
        _validateSettingsName: function () {
            var self = this;

            jQuery(this._moduleSettingsNameSelector).live('keyup', function () {
                if (self._isEmptyField(this)) {
                    jQuery(this).removeClass().addClass('default-settings-color js-setting-element');
                }
                else if (self._camelCaseRegex(jQuery(this).val())) {
                    jQuery(this).removeClass().addClass('correct-settings-color js-setting-element');
                } else {
                    jQuery(this).removeClass().addClass('invalid-settings-color js-setting-element');
                }
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
                cache: false,
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
                    .html(self.options.notificationExistingClasses + '<hr>' + sExtendClasses)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewControllers) {
                jQuery(self._moduleControllersSelectorNoticeDiv)
                    .html(self.options.notificationExistingControllers + '<hr>' + sNewControllers)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewModels) {
                jQuery(self._moduleModelsSelectorNoticeDiv)
                    .html(self.options.notificationExistingModels + '<hr>' + sNewModels)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewLists) {
                jQuery(self._moduleListsSelectorNoticeDiv)
                    .html(self.options.notificationExistingLists + '<hr>' + sNewLists)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewWidgets) {
                jQuery(self._moduleWidgetsSelectorNoticeDiv)
                    .html(self.options.notificationExistingWidgets + '<hr>' + sNewWidgets)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewBlocks) {
                jQuery(self._moduleBlocksSelectorNoticeDiv)
                    .html(self.options.notificationExistingBlocks + '<hr>' + sNewBlocks)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            if (sNewSettings) {
                jQuery(self._moduleSettingsNameSelectorNoticeDiv)
                    .html(self.options.notificationExistingSettings + '<hr>' + sNewSettings)
                    .slideDown(self.options.notificationSlideDownSpeed);
            }

            self._validateComponentName(oData);
        },


        /**
         * Return JSON response with extendable classes if exist.
         *
         * @param {object} oElement
         */
        _requestExtendClassesJsonResponse: function (oElement) {
            var self = this;

            jQuery.ajax({
                cache: false,
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
         *
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

            if (aFormattedValue.length === 0)
                aFormattedValue.push("none");

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

            if (blBlock) {
                for (var b in aObjectData) {
                    sFormattedValue += oMetaObject[aObjectData[b]]['block']
                        + "@"
                        + oMetaObject[aObjectData[b]]['template']
                        + "<br />";
                }
            } else {
                if (aObjectData.length > 0) {
                    sFormattedValue += "<table class='settings-notification'>" +
                        "<tr>" +
                        "<td>" + this.options.notificationExistingSettingsName + "</td>" +
                        "<td>" + this.options.notificationExistingSettingsType + "</td>" +
                        "<td>" + this.options.notificationExistingSettingsValue + "</td>" +
                        "</tr>";
                    for (var s in aObjectData) {
                        sFormattedValue += "<tr><td>" + oMetaObject[aObjectData[s]]['name'] + "</td>"
                            + "<td>" + oMetaObject[aObjectData[s]]['type'] + "</td>"
                            + "<td>" + oMetaObject[aObjectData[s]]['value'] + "</td></tr>"
                        ;
                    }
                    sFormattedValue += "</table>";
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
            return this._showCorrectNotification(oElement, '_blocksRegex','');
        },

        /**
         * Checks or triggered field is a block element
         * And returns a certain error message
         *
         * @param {object} oElement
         * @param {object} self
         * @returns {string}
         */
        _getValidErrorMessage: function(oElement, self){
            var blockElement = document.querySelector(self._moduleBlocksSelector);

            return (oElement === blockElement)? self.options.notificationBlockErrorText: self.options.notificationErrorText;
        },

        /**
         * Checks or object has given through parameters(cls) class
         *
         * @param {object} element
         * @param cls
         * @returns {boolean}
         * @private
         */
        _hasClass: function (element, cls) {
            return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
        },


        /**
         *  Using for slicing settings name input and returning index of it.
         *  For example given name: modulegenerator_settings[0][name] => returns 0
         *
         * @param str
         * @returns {string}
         * @private
         */
        _getIndexFromString: function(str){
            str = str.substring(str.indexOf("[") + 1);
            return str.split(']')[0];
        },

        /**
         * Using for slicing settings name input and returning first part of it.
         * For example given name: modulegenerator_settings[0][name] => returns modulegenerator_settings
         *
         * @param {string} str
         * @returns {string}
         * @private
         */
        _getSettingName:function(str){
            return str.split('[')[0];
        },

        /**
         * Show notification depending on various states of input field.
         *
         * self._errorText gets error message which should be render if user value is invalid.
         * This message combines from two strings: translatable error message text and example by field which one is invalid
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
            var notice = document.querySelectorAll('.js-notice-block');
            self._errorText = self._getValidErrorMessage(oElement, this) + ' ' + self._errorMessageExamples.find(function(variable) {
                return variable.element === self._getSettingName(jQuery(oElement).attr('name'));
            }).example;

            var sEnteredInput = jQuery(oElement).val();
            if (self._isEmptyField(oElement)) {
                self._hideNotification(oElement);

                //If setting field is empty hide it
                if (self._hasClass(oElement, 'js-setting-element'))
                    self._showSettingNotification(notice[self._getIndexFromString(oElement.getAttribute("name"))], 'hidden', '');
            }
            else if ((self._countNewLines(sEnteredInput)) > 0) {
                if (Object.values(self._splitNewLines(sEnteredInput, sRegexFunction)).indexOf(false) !== -1) {
                    self._showNotification(oElement, 'error', self.options.notificationErrorText);
                } else {
                    self._showNotification(oElement, 'success', self.options.notificationSuccessText);
                    return true;
                }
            } else if (self[sRegexFunction](sEnteredInput)) {

                //If setting field is written correctly hide it.
                if (self._hasClass(oElement, 'js-setting-element')) {
                    self._showSettingNotification(notice[self._getIndexFromString(oElement.getAttribute("name"))], 'hidden', '');
                }
                self._showNotification(oElement, 'success', self.options.notificationSuccessText);

                return true;
            } else {
                if (self._hasClass(oElement, 'js-setting-element'))
                    self._showSettingNotification(notice[self._getIndexFromString(oElement.getAttribute("name"))], 'error', self._errorText);

                self._showNotification(oElement, 'error', self._errorText);
            }
        },


        /**
         *
         * Special for settings notification because they have other html structure.
         *
         * @param oElement
         * @param sNoticeType
         * @param sNoticeText
         * @private
         */
        _showSettingNotification: function (oElement, sNoticeType, sNoticeText) {
            jQuery(oElement)
                .fadeIn(1000)
                .attr('class', 'notice')
                .addClass('notice notice-' + sNoticeType+ ' js-notice-block')
                .text(sNoticeText)
            ;

            if ( sNoticeType === 'hidden')
                oElement.style.display = "none";
        },

        /**
         * Validate if input field or textarea is empty
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
            var aLowerExcludedModuleNames = jQuery.map(
                this._excludedModuleNames,
                function (val) {
                    return val.toLowerCase();
                });
            if (this._inArrayIn(jQuery(oElement).val().toLowerCase(), aLowerExcludedModuleNames, 0) !== -1) {
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
            if (typeof 'string' !== elem) {
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
                    .removeAttr('selected')
                    .val('');
            }
        }
    }
);
