/******************************************
 * Onm Editor
 *
 * Wrapper for configuring WYSIWYG editors
 *
 * @author          Openhost developers <developers@openhost.es>
 * @version         1.0
 *
 ******************************************/
;(function(document, window, $){
    $.extend({
        cookiesHint: function( options ) {


            var overlay = $('#cookie_overlay');

            overlay.find('.closeover').on('click', function() {
                overlay.hide();
            })

            $.cookie('cookieoverlay_accepted', true);
        }
    });
})(document, window, jQuery);
$.cookiesHint({});
