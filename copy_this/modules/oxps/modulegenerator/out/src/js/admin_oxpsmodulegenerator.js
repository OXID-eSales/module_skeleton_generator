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
        _inputDelayValue: 5000,
        _inputValue: '',

        _create: function () {
            this._bindEvents();
        },

        _bindEvents: function () {
            var self = this;
            var delay = this._makeDelay(this._inputDelayValue);

            jQuery(this._moduleNameSelector).keyup(function () {
                self._inputValue = jQuery(this).val();
                delay(self._validateModuleName(self._inputValue));
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
                        jQuery("#helpText_OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES_HINT").after('<div>' + data['aNewClasses'] + '</div>');
                        jQuery("#helpText_OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS_HINT").after('<div>' + data['aNewControllers'] + '</div>');
                        jQuery("#helpText_OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS_HINT").after('<div>' + data['aNewModels'] + '</div>');
                        jQuery("#helpText_OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS_HINT").after('<div>' + data['aNewLists'] + '</div>');
                        jQuery("#helpText_OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS_HINT").after('<div>' + data['aNewWidgets'] + '</div>');
                        jQuery("#helpText_OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS_HINT").after('<div>' + data['aNewBlocks'] + '</div>');
                    }
                }
            });
        },

        // Same backend validation in \oxpsModuleGeneratorValidator::validateCamelCaseName
        _validateCamelCaseName: function (value) {
            return value.match(/^([A-Z]{1})([a-zA-Z0-9]{1,63})$/);
        },

        // TODO: Not working yet
        _makeDelay: function (ms) {
            var timer = 0;
            return function (callback) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        }
    }
);
