/**
 * jQuery widget for Module Generation form.
 * Handles data validation, edited module data loading, etc.
 */
jQuery.widget(
    'oxpsmodulegenerator.wizard',
    {
        options: {
            moduleNameValidationUrl: '',
            extendClassesValidationUrl: ''
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

                if (self._validateModuleName(this)) {
                    self._requestModuleNameJsonResponse(this);
                } else {
                    console.log('CamelCase Name False');
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
        },

        /**
         * Validate JSON data response
         *
         * @param {object} element
         *
         * @returns {boolean}
         */
        _validateModuleName: function (element) {
            return this._validateCamelCaseName(element);
        },

        /**
         * @param {object} element
         * @private
         */
        _requestModuleNameJsonResponse: function (element) {
            var self = this;

            jQuery.ajax({
                dataType: 'json',
                type: 'post',
                url: self.options.moduleNameValidationUrl,
                data: {moduleName: jQuery(element).val()},
                success: function (data) {
                    if (null != data) {
                        self._showModuleNameHtmlResponse(data);
                    }
                }
            });
        },

        // TODO: remove hardcoded stuff with createElement function
        /**
         * @param {object} data
         */
        _showModuleNameHtmlResponse: function (data) {
            var self = this;

            console.log(data);
            jQuery(self._moduleClassesSelector).before('<div><b>' + self._buildHtmlResponse(data['aExtendClasses'], true) + '</b></div>');
            jQuery(self._moduleControllersSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewControllers'], false) + '</b></div>');
            jQuery(self._moduleModelsSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewModels'], false) + '</b></div>');
            jQuery(self._moduleListsSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewLists'], false) + '</b></div>');
            jQuery(self._moduleWidgetsSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewWidgets'], false) + '</b></div>');
            jQuery(self._moduleBlocksSelector).before('<div><b>' + self._buildSelectiveHtmlResponse(data['aNewBlocks'], true) + '</b></div>');
            jQuery(self._moduleBlocksSelector).after('<div><b>' + self._buildSelectiveHtmlResponse(data['aModuleSettings'], false) + '</b></div>');
        },

        /**
         * @param {object} element
         */
        _requestExtendClassesJsonResponse: function (element) {
            var self = this;

            jQuery.ajax({
                dataType: 'json',
                type: 'post',
                url: self.options.extendClassesValidationUrl,
                data: {extendClasses: jQuery(element).val()},
                success: function (data) {
                    if (null != data) {
                        self._showExtendClassesHtmlResponse(data);
                    }
                }
            });
        },

        /**
         * @param {object} data
         */
        _showExtendClassesHtmlResponse: function (data) {
            var self = this;

            console.log(data);
        },

        /**
         * @param {object} oMetaObject
         * @param {boolean} keys
         *
         * @returns {string}
         */
        _buildHtmlResponse: function (oMetaObject, keys) {
            var aObjectData = [];
            var sFormattedValue = '';

            if (true == keys) {
                aObjectData = Object.keys(oMetaObject);
            } else {
                aObjectData = Object.values(oMetaObject);
            }

            for (var i in aObjectData) {
                sFormattedValue += aObjectData[i] + "<br />";
            }

            return sFormattedValue;
        },

        /**
         * Different logic of Html Response required for Settings and Blocks objects of Metadata.
         *
         * @param {object} oMetaObject
         * @param {boolean} blocks
         *
         * @returns {string}
         */
        _buildSelectiveHtmlResponse: function (oMetaObject, blocks) {
            var sFormattedValue = '';
            var aObjectData = Object.keys(oMetaObject);

            for (var i in aObjectData) {
                if (blocks) {
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
         * @param {object} element
         *
         * @returns {boolean}
         */
        _validateCamelCaseName: function (element) {
            if (new RegExp(/^([A-Z])([a-zA-Z0-9]{1,63})$/).test(jQuery(element).val())) {
                console.log(jQuery(element).attr('name') + ': TRUE');
                return true;
            }
            console.log(jQuery(element).attr('name') + ': FALSE');
        }
    }
);