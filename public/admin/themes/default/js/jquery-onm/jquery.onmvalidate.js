;(function($) {

    var helpers = {
        find_container : function(input) {
            return input.parent().parent();
        },
        remove_validation_markup: function(input) {
            var cont = helpers.find_container(input);
            cont.removeClass('error success warning');
            $('.help-inline.error, .help-inline.success, .help-inline.warning', cont).remove();
        },
        add_validation_markup: function (input, cls, caption) {
            var cont = helpers.find_container(input);
            cont.addClass(cls);
            input.addClass(cls);

            if (caption) {
                var msg = $('<span class="help-inline"/>');
                msg.addClass(cls);
                msg.text(caption);
                input.after(msg);
            }
        },
        remove_all_validation_markup: function (form) {
            $('.help-inline.error, .help-inline.success, .help-inline.warning',
                form).remove();
            $('.error, .success, .warning', form).removeClass('error success warning');
        }
    };

    $.fn.onmValidate = function(method) {

        var methods = {
            init : function(options) {
                var common_settings = $.extend({}, this.onmValidate.defaults, options);
                return this.each(function() {
                    var $element = $(this),
                        element = this,
                        settings = $.extend({}, common_settings);

                    $element.data('onmValidate', settings);

                    $element.onmValidate('validate');

                });
            },
            validate: function() {
                return this.each(function() {
                    var $element = $(this),
                        element = this,
                        settings = $(this).data('onmValidate');

                    var form = $(this);

                    form
                        .validator({
                            'lang' : settings.lang
                        })
                        .bind('reset.validator', function () {
                            helpers.remove_all_validation_markup(form);
                        })
                        .bind('onSuccess', function (e, ok) {
                            $.each(ok, function() {
                                var input = $(this);
                                helpers.remove_validation_markup(input);

                                // uncomment next line to highlight successfully
                                // add_validation_markup(input, 'success');
                            });
                        })
                        .bind('onFail', function (e, errors) {
                            $.each(errors, function() {
                                var err = this;
                                var input = $(err.input);
                                helpers.remove_validation_markup(input);
                                helpers.add_validation_markup(input, 'error', err.messages.join(' '));
                            });
                            return false;
                        });
                });
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in onmValidate plugin!');
        }
    };

    $.fn.onmValidate.defaults = {
        'lang' : 'en'
    };
})(jQuery);