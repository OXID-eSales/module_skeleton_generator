/**
 * jQuery widget for Module Generation form.
 * Handles data validation, edited module data loading, etc.
 */
jQuery.widget(
    'oxpsmodulegenerator.wizard',
    {
        options: {
            responseUrl: ''
        },

        _moduleNameSelector: "input[name='modulegenerator_module_name']",

        _create: function () {
            this._bindEvents();
        },

        _bindEvents: function () {
            var self = this;
            jQuery(this._moduleNameSelector).keyup( function () {
                self._validateModuleName();
            });
        },

        _validateModuleName: function () {
            var self = this;
            var moduleName = jQuery(this._moduleNameSelector).val();
            jQuery.ajax({
                type: 'post',
                url: self.options.responseUrl,
                data: {moduleName: moduleName},
                success: function (data) {
                    console.log(data);
                }
            });
        }
    }
);
