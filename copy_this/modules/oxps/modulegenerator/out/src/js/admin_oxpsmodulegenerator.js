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

        _create: function () {
            this._bindEvents();
        },

        _bindEvents: function () {
            var self = this;

            jQuery(this._moduleNameSelector).keyup(function () {
                if (self._validateCamelCaseName(this)) {
                    self._requestModuleNameJsonResponse(this);
                } else {
                    self._showNotification(this, 'error', self.options.notificationErrorText);
                }
            });

            jQuery(this._moduleClassesSelector).keyup(function () {
                self._requestExtendClassesJsonResponse(this);
            });

            jQuery(this._moduleControllersSelector).keyup(function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleModelsSelector).keyup(function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleListsSelector).keyup(function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleWidgetsSelector).keyup(function () {
                self._validateCamelCaseName(this);
            });

            jQuery(this._moduleBlocksSelector).keyup(function () {
                self._validateBlocksFieldEntry(this);
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
                        jQuery('.editMode').css('display', 'none');
                }

            });
        },

        // TODO: remove hardcoded stuff
        /**
         * @param {object} oData
         */
        _showModuleNameHtmlResponse: function (oData) {
            var self = this;
            jQuery('.editMode').css('display', 'block');
            // TODO: DELETE LOG
            console.log(oData);
            jQuery(self._moduleClassesSelector).before('<div><b>' + self._buildHtmlResponse(oData['aExtendClasses'], true, '<br />') + '</b></div>');
            jQuery(self._moduleControllersSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewControllers'], false, '<br />') + '</b></div>');
            jQuery(self._moduleModelsSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewModels'], false, '<br />') + '</b></div>');
            jQuery(self._moduleListsSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewLists'], false, '<br />') + '</b></div>');
            jQuery(self._moduleWidgetsSelector).before('<div><b>' + self._buildHtmlResponse(oData['aNewWidgets'], false, '<br />') + '</b></div>');
            jQuery(self._moduleBlocksSelector).before('<div><b>' + self._buildSelectiveHtmlResponse(oData['aNewBlocks'], true) + '</b></div>');
            jQuery(self._moduleBlocksSelector).after('<div><b>' + self._buildSelectiveHtmlResponse(oData['aModuleSettings'], false) + '</b></div>');
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
            var response = this.options.notificationValidClassesText + this._buildHtmlResponse(oData, true, ', ');

            this._showNotification(oElement, 'info', response);
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
            var sFormattedValue = '';

            if (true === blKeys) {
                aObjectData = Object.keys(oMetaObject);
            } else {
                aObjectData = Object.values(oMetaObject);
            }
            // TODO: Remove last spaceType as it is not required
            for (var i in aObjectData) {
                sFormattedValue += aObjectData[i] + sSpaceType;
            }

            return sFormattedValue;
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
        _validateCamelCaseName: function (oElement) {
            var self = this;
            if (/^([A-Z])([a-zA-Z0-9]{1,63})$/.test(jQuery(oElement).val())) {
                self._showNotification(oElement, 'success', self.options.notificationSuccessText);
                return true;
            }
            self._showNotification(oElement, 'error', self.options.notificationErrorText);
        },

        /**
         * Simple block field validation.
         *
         * @param {object} oElement
         */
        _validateBlocksFieldEntry: function (oElement) {
            var self = this;
            if (/^(\w+)(@)(\w+)$/.test(jQuery(oElement).val())) {
                self._showNotification(oElement, 'success', self.options.notificationSuccessText);
            } else {
                self._showNotification(oElement, 'error', self.options.notificationErrorText);
            }
        },

        /**
         * @param {object} oElement
         * @param {string} sNoticeType
         * @param {string} sNoticeText
         */
        _showNotification: function (oElement, sNoticeType, sNoticeText) {
            jQuery(oElement).siblings('.notice')
                .attr('class', 'notice')
                .addClass('notice-visible notice-' + sNoticeType)
                .text(sNoticeText);
        }
    }
);
