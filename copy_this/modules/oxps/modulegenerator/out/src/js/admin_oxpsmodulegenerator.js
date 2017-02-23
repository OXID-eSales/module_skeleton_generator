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
            someParam: '',
            otherParam: ''
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
            // actions go here
            console.log('Hello World!');

            this._privateMethod();
        },

        /**
         * If the wall placeholder is inside hidden list item content, move it to visible list item title.
         *
         * @private
         */
        _privateMethod: function () {
            console.log(jQuery(this._constOrProp));
            console.log(jQuery(this.options.someParam));
            console.log(jQuery(this.options.otherParam));
        }
    }
);
