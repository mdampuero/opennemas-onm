/**
* onmContentRating jQuery plugin - v.0.1
*
* A simple plugin for rating contents
*/
;(function($) {

    var helpers = {
        find_container : function(input) {
            return input.closest('.control-group');
        },

        loadContent: function(elem) {
            var settings = $(elem).data('onmContentRating');

            $.ajax({
                url: settings.url,
                method: 'GET',
                data: { 'content_id' : settings.content_id },
                success: function(data) {
                    elem.html(data);
                }
            })
        },
        vote: function (parent, elem) {
            var settings = parent.data('onmContentRating');

            var value = $(elem).data('vote');
            var id    = settings.content_id;

            $.ajax({
                url: settings.url,
                type: 'POST',
                data: {
                    'vote_value' : value,
                    'content_id' : id
                },
                success: function(result){
                    parent.html(result);
                }
            });
        },

        onHover: function(parent, elem) {
            var settings = parent.data('onmContentRating');

            var brothers = parent.find(settings.clickable);
            var hovered_index = brothers.index(elem);

            var smaller = brothers.find(':lt('+hovered_index+')')
            parent.find('li a:lt('+(hovered_index)+') > *').addClass('active')
            parent.find('li a:gt('+(hovered_index)+') > *').removeClass('active')

        }
    };

    $.fn.onmContentRating = function(method) {

        var methods = {
            init : function(options) {
                var common_settings = $.extend({}, this.onmContentRating.defaults, options);
                return this.each(function() {
                    var $element = $(this),
                        element = this,
                        settings = $.extend({}, common_settings);

                    $element.data('onmContentRating', settings);
                    $element.onmContentRating('initialize');
                });
            },
            initialize: function() {
                return this.each(function() {
                    var $element = $(this),
                        element = this,
                        settings = $(this).data('onmContentRating');

                    var elem = $(this);
                    helpers.loadContent(elem);

                    elem
                        .on('mouseenter', settings.clickable, function (e, ui) {
                            helpers.onHover(elem, $(this));
                        })
                        .on('mouseleave', function (e, ui) {
                            // reset
                            log('reset current value');
                        })
                        .on('click', settings.clickable, function (e, ui) {
                            helpers.vote(elem, $(this));
                        });
                });
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in onmContentRating plugin!');
        }
    };

    $.fn.onmContentRating.defaults = {
        url:     "/content/rate/",
        clickable: "li a"
    };
})(jQuery);