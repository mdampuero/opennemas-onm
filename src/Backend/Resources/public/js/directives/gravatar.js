/**
 * Directive to get an avatar from gravatar given an email.
 */
angular.module('BackendApp.directives')
    .directive('gravatar', function ($compile) {
        return {
            restrict: 'AE',
            link: function ($scope, $element, $attrs) {
                var baseUrl;
                var html = '';

                var size = $attrs.size ? $attrs.size : '16';
                var d    = 'mm'; // mm, identicon, 404, monsterid, wavatar
                var r    = 'g';
                var img  = false;
                var atts = [];

                var default_icon = "&d="
                    + encodeURIComponent($attrs["image_dir"] + "favicon.png");

                    atts['height'] = size;
                    atts['width']  = size;

                if ($attrs.image) {
                    img = $attrs.image;
                }

                var url = 'http://www.gravatar.com/avatar/';

                url += hex_md5($attrs['email'].trim().toLowerCase());
                url += "?s=" + size + "&d=" + d + "&r=" + r;
                if (img) {
                    html = '<img src="' + url + '"';
                    for (var i in atts) {
                        html += ' ' + i + '="' + atts[i] + '"';
                    }

                    html += ' />';
                }

                // Compile template and replace elements
                var e = $compile(html)($scope);
                $element.replaceWith(e);
            }
        };
    });
