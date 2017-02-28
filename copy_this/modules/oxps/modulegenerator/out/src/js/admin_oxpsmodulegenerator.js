/**
 * jQuery widget for Module Generation form.
 * Handles data validation, edited module data loading, etc.
 *
 * TODO: The widget below to be implemented, current code is just demo, how to create widgets
 */
jQuery.widget(
    'oxpsmodulegenerator.wizard',
    {

        /**
         * Widget options with default values.
         */
        options: {
            responseUrl: ''
        },

        /**
         * [Info].
         *
         * @private
         */
        _constOrProp: 'value',

        /**
         * Constructor.
         * Initialize widget and Module Generation form.
         *
         * @private
         */
        _create: function () {
            var _this = this;

            jQuery("input[name='modulegenerator_module_name']").keyup(function () {
                var moduleName = jQuery(this).val();
                jQuery.ajax({
                    type: 'post',
                    url: _this.options.responseUrl,
                    data: {moduleName: moduleName},
                    success: function (data) {
                        console.log(data);
                    }
                });
            });
        },

        /**
         * If the wall placeholder is inside hidden list item content, move it to visible list item title.
         *
         * @private
         */
        _privateMethod: function () {
            //     console.log(jQuery(this._constOrProp));
            //     console.log(jQuery(this.options.someParam));
        }
    }
);
