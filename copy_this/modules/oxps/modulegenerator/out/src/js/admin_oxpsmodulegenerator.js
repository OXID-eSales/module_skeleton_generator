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
            notificationWarningText: '',
            notificationValidClassesText: ''
        },

        _moduleNameSelector: "input[name='modulegenerator_module_name']",
        _moduleClassesSelector: "textarea[name='modulegenerator_extend_classes']",
        _moduleControllersSelector: "textarea[name='modulegenerator_controllers']",
        _moduleModelsSelector: "textarea[name='modulegenerator_models']",
        _moduleListsSelector: "textarea[name='modulegenerator_lists']",
        _moduleWidgetsSelector: "textarea[name='modulegenerator_widgets']",
        _moduleBlocksSelector: "textarea[name='modulegenerator_blocks']",
        _moduleSettingsNameSelector: "input[name^='modulegenerator_settings']",

        _cssEditModeSelectorClass: ".editMode",
        _cssNoticeSelectorClass: ".notice",
        _cssAddSettingsLineButtonId: "#addNewSettingsLine",
        _cssSettingsBodyId: "#settingsBody",
        _cssSettingsLineClass: ".settingsLine",
        _cssSettingsLineId: "settingsLine",
        _cssRemoveSettingsLineButtonClass: ".removeLineButton",

        _create: function () {
            this._bindEvents();
        },

        _bindEvents: function () {
            var self = this;

            // From jQuery 1.7+ live() is deprecated and should be changed to on() method after jQuery version update.

            jQuery(this._moduleNameSelector).live('keyup', function () {
                if (self._isEmptyField(this)) {
                    self._hideNotification(this);
                    jQuery(self._cssEditModeSelectorClass).slideUp();
                    self._hideExistingComponentNotification();
                }
                else if (self._validateCamelCaseName(this)) {
                    self._requestModuleNameJsonResponse(this);
                } else {
                    self._showNotification(this, 'error', self.options.notificationErrorText);
                    jQuery(self._cssEditModeSelectorClass).slideUp();
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

            // TODO: Refactor THIS!!!
            jQuery(this._moduleSettingsNameSelector).live('keyup', function () {
                if (self._isEmptyField(this)) {
                    jQuery(this).css('backgroundColor', 'white');
                }
                else if (/^([A-Z])([a-zA-Z0-9]{1,63})$/.test(jQuery(this).val())) {
                    jQuery(this).css('backgroundColor', '#EBFFDE');
                } else {
                    jQuery(this).css('backgroundColor', '#FFE2DE');
                }
            });

            // TODO: Refactor THIS!!!
            jQuery(this._cssAddSettingsLineButtonId).live('click', function () {
                // Get last settings line's ID
                var sLastLineId = jQuery(self._cssSettingsLineClass + ':last').attr('id');

                // Subtract text leaving only number
                var iCleanId = parseInt(sLastLineId.replace(self._cssSettingsLineId, ''));

                // Adding +1 to last ID for new line
                iCleanId++;

                // Clone, append new id and clearing existing values from last line
                jQuery(self._cssSettingsLineClass + ':last')
                    .clone()
                    .attr('id', self._cssSettingsLineId + iCleanId)
                    .find(self._cssRemoveSettingsLineButtonClass).remove()
                    .end()
                    .appendTo(self._cssSettingsBodyId)
                    .append('<input type="button" value="REMOVE" class="removeLineButton" id="' + iCleanId + '">')
                    .find("input[type='text'], textarea").val('').css('backgroundColor', 'white')
                ;
            });

            jQuery(self._cssRemoveSettingsLineButtonClass).live('click', function () {
                jQuery(this).closest('tr').remove();
            });
        },

        /**
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
                    if (null != data) {
                        self._showModuleNameHtmlResponse(data);
                    }
                },
                error: function () {
                    jQuery(self._cssEditModeSelectorClass).slideUp();
                    self._hideExistingComponentNotification();
                }
            });
        },

        // TODO: remove hardcoded stuff
        /**
         * @param {object} oData
         */
        _showModuleNameHtmlResponse: function (oData) {
            var self = this;
            jQuery(self._cssEditModeSelectorClass).slideDown();
            // jQuery(self._moduleClassesSelector).before('<div><b>' + self._buildHtmlResponse(oData['aExtendClasses'], true, '<br />') + '</b></div>');
            // jQuery(self._moduleControllersSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewControllers'], false, '<br />') + '</b></div>');
            // jQuery(self._moduleModelsSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewModels'], false, '<br />') + '</b></div>');
            // jQuery(self._moduleListsSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewLists'], false, '<br />') + '</b></div>');
            // jQuery(self._moduleWidgetsSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewWidgets'], false, '<br />') + '</b></div>');
            // jQuery(self._moduleBlocksSelector).before('<div><b>' + self._buildSelectiveHtmlResponse(oData['aNewBlocks'], true) + '</b></div>');
            // jQuery(self._moduleBlocksSelector).after('<div><b>' + self._buildSelectiveHtmlResponse(oData['aModuleSettings'], false) + '</b></div>');
            jQuery('.component-existing-classes')
                .html(self._buildHtmlResponse(oData['aExtendClasses'], true, '<br />'))
                .slideDown()
            ;
            jQuery('.component-existing-controllers')
                .html(self._buildHtmlResponse(oData['aNewControllers'], false, '<br />'))
                .slideDown()
            ;
            jQuery('.component-existing-models')
                .html(self._buildHtmlResponse(oData['aNewModels'], false, '<br />'))
                .slideDown()
            ;
            jQuery('.component-existing-lists')
                .html(self._buildHtmlResponse(oData['aNewLists'], false, '<br />'))
                .slideDown()
            ;
            jQuery('.component-existing-widgets')
                .html(self._buildHtmlResponse(oData['aNewWidgets'], false, '<br />'))
                .slideDown()
            ;
            jQuery('.component-existing-blocks')
                .html(self._buildSelectiveHtmlResponse(oData['aNewBlocks'], true))
                .slideDown()
            ;
        },

        /**
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
                    if (null != data) {
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
            // TODO: Remove last spaceType as it is not required
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
         * Same backend validation in \oxpsModuleGeneratorValidator::validateCamelCaseName
         *
         * @param {object} oElement
         *
         * @returns {boolean}
         */
        // TODO: Refactor THIS!!!
        _validateCamelCaseName: function (oElement) {
            var self = this;
            var sEnteredInput = jQuery(oElement).val();
            var aValidatedInput = {};

            if (self._isEmptyField(oElement)) {
                self._hideNotification(oElement);
            } else if ((sEnteredInput.match(/\n/g) || []).length > 0) {
                var aSplitInput = sEnteredInput.split(/\n/);
                aSplitInput.forEach(function (sInput) {
                    if (sInput.trim() != '') {
                        aValidatedInput[sInput] = !(!(self._camelCaseRegex(sInput)));
                    }
                });

                if (Object.values(aValidatedInput).indexOf(false) > -1) {
                    self._showNotification(oElement, 'error', self.options.notificationErrorText);
                } else {
                    self._showNotification(oElement, 'success', self.options.notificationSuccessText);

                    return true;
                }
            } else if (self._camelCaseRegex(jQuery(oElement).val())) {
                self._showNotification(oElement, 'success', self.options.notificationSuccessText);

                return true;
            } else {
                self._showNotification(oElement, 'error', self.options.notificationErrorText);
            }
        },

        /**
         * Simple block field validation.
         *
         * @param {object} oElement
         */
        _validateBlocksFieldEntry: function (oElement) {
            var self = this;
            var sEnteredInput = jQuery(oElement).val();

            if (self._isEmptyField(oElement)) {
                self._hideNotification(oElement);
            }
            else if (/^(\w+)(@)(\w+)$/.test(sEnteredInput)) {
                self._showNotification(oElement, 'success', self.options.notificationSuccessText);
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
            if ('' == jQuery(oElement).val()) {
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

        _hideNotification: function (oElement) {
            jQuery(oElement).siblings(this._cssNoticeSelectorClass).fadeOut(500);
        },

        _camelCaseRegex: function (sInput) {
            return /^([A-Z])([a-zA-Z0-9]{1,63})$/.test(sInput);
        },

        _hideExistingComponentNotification: function () {
            jQuery('div').filter( function () {
                return this.className.match(/\bcomponent-existing/);
            }).slideUp();

        }
    }
);
