/**
 * jQuery widget for Module Generation form.
 * Handles data validation, edited module data loading, etc.
 */
jQuery.widget(
    'oxpsmodulegenerator.wizard',
    {
        options: {
            moduleNameValidationUrl: ''
        },

        _moduleNameSelector: "input[name='modulegenerator_module_name']",
        _moduleClassesSelector: "textarea[name='modulegenerator_extend_classes']",
        _moduleControllersSelector: "textarea[name='modulegenerator_controllers']",
        _moduleModelsSelector: "textarea[name='modulegenerator_models']",
        _moduleListsSelector: "textarea[name='modulegenerator_lists']",
        _moduleWidgetsSelector: "textarea[name='modulegenerator_widgets']",
        _moduleBlocksSelector: "textarea[name='modulegenerator_blocks']",
        _inputValue: '',

        _create: function () {
            this._bindEvents();
        },

        _bindEvents: function () {
            var self = this;

            jQuery(this._moduleNameSelector).keyup(function () {
                self._inputValue = jQuery(this).val();
                self._validateModuleName(self._inputValue);
            });

            jQuery(this._moduleClassesSelector).keyup(function () {
                self._inputValue = jQuery(this).val();

                if (!self._validateCamelCaseName(self._inputValue)) {
                    console.log('false');
                } else {
                    console.log('true');
                }
            });
        },

        // TODO: separate to 2 methods: 1) responsible for validation 2) responsible for JSON response fetch action
        // TODO: remove hardcoded stuff with createElement function
        _validateModuleName: function (inputValue) {
            var self = this;

            jQuery.ajax({
                dataType: 'json',
                type: 'post',
                url: self.options.moduleNameValidationUrl,
                data: {moduleName: inputValue},
                success: function (data) {
                    if (null != data) {
                        console.log(data);
                        jQuery(self._moduleClassesSelector).before('<div><b>' + self._buildHtmlResponse(data['aExtendClasses'], true) + '</b></div>');
                        jQuery(self._moduleControllersSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewControllers'], false) + '</b></div>');
                        jQuery(self._moduleModelsSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewModels'], false) + '</b></div>');
                        jQuery(self._moduleListsSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewLists'], false) + '</b></div>');
                        jQuery(self._moduleWidgetsSelector).before('<div><b>' + self._buildHtmlResponse(data['aNewWidgets'], false) + '</b></div>');
                        jQuery(self._moduleBlocksSelector).before('<div><b>' + self._buildSelectiveHtmlResponse(data['aNewBlocks'], true) + '</b></div>');
                        jQuery(self._moduleBlocksSelector).after('<div><b>' + self._buildSelectiveHtmlResponse(data['aModuleSettings'], false) + '</b></div>');
                    }
                }
            });
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
         * Different logic of Html Response required for Metadata Settings and Blocks objects
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
                if(blocks) {
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
         * @param {string} value
         */
        _validateCamelCaseName: function (value) {
            return value.match(/^([A-Z]{1})([a-zA-Z0-9]{1,63})$/);
        }
    }
);
