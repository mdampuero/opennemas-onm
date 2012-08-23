;(function($) {

    var helpers = {
        find_container : function(input) {
            return input.closest('.control-group');
        },
        remove_validation_markup: function(input) {
            var container = helpers.find_container(input);
            container.removeClass('error success warning');
            $('.help-inline.error, .help-inline.success, .help-inline.warning', container).remove();
        },
        add_validation_markup: function (input, css_class, caption) {
            var container = helpers.find_container(input);
            container.addClass(css_class);
            input.addClass(css_class);

            if (caption) {
                var msg = $('<span class="help-inline"/>');
                msg.addClass(css_class);
                msg.text(caption);
                if (input.parent().is('.input-prepend, .input-append')) {
                    input.parent().after(msg);
                } else {
                    input.after(msg);
                }
            }
        },
        remove_all_validation_markup: function (container) {
            $('.help-inline.error, .help-inline.success, .help-inline.warning', container).remove();
            $('.error, .success, .warning', container).removeClass('error success warning');
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
                                var panel = input.closest('.ui-tabs-panel');
                                if (panel) {
                                    var panel_id = panel.attr('id');
                                    var tabs = $(panel.closest('.ui-tabs')).tabs('select', panel_id);
                                }
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