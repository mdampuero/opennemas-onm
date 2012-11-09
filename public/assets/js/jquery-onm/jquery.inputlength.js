;(function($) {

    $.fn.inputLengthControl = function(method) {

        var methods = {
            init : function(options) {
                var common_settings = $.extend({}, this.inputLengthControl.defaults, options);

                return this.each(function() {
                    var $element = $(this),
                        element = this,
                        settings = $.extend({}, common_settings);

                    $element.data('inputLengthControl', settings);

                    $element.inputLengthControl('checklength');

                    $element.on('change', function(e, ui){
                        e.stopPropagation();
                        $element.inputLengthControl('checklength');
                    });

                    $element.on('keyup', function(e, ui){
                        e.stopPropagation();
                        $element.inputLengthControl('checklength');
                    });

                });
            },
            checklength: function() {
                return this.each(function() {
                    var $element = $(this),
                        element = this,
                        settings = $(this).data('inputLengthControl'),
                        length = $element.find(settings.input).val().length;

                    for (var i = 0; i < settings.ranges.length; i++) {

                        if (length >= settings.ranges[i].min && length <= settings.ranges[i].max) {
                            $element.find(settings.infobox).css( 'color', settings.ranges[i].class)
                                .text(length);
                        }
                    }
                });
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in inputLengthControl plugin!');
        }
    };

    $.fn.inputLengthControl.defaults = {
        ranges: [
            {'min': 0, 'max': 19, 'class': ''},
            {'min': 20, 'max': 34, 'class': '#A8A400'},
            {'min': 35, 'max': 79, 'class': '#A8A432'},
            {'min': 80, 'max': 149, 'class': '#AB4700'},
            {'min': 150, 'max': 250, 'class': '#AB0B00'}
        ],
        input: 'input',
        infobox: '.add-on'
    };
})(jQuery);