/**
 * Directive to clone a src param for embed tags
 */
angular.module('BackendApp.directives')
    .directive('object', function(){
        return {
            restrict: 'E',
            link: function(scope, element, attrs) {
                var $_current = element;

                scope.$watch('[' + attrs.ngData + ',' + attrs.ngParam + ']', function(newVal) {
                    var $_clone = element.clone().attr('data', newVal[0]);
                    var content = '';

                    angular.forEach(newVal[1], function(value, name) {
                        content += '<param name="' + name + '" value="' + value + '">';
                    });

                    $_current.replaceWith($_clone.html(content += '<embed src="' + newVal[0] + '"></embed>'));
                    $_current = $_clone;
                }, true);
            }
        };
    });
