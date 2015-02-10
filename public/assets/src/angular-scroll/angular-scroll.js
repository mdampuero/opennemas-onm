/**
 * onm.scroll module to bind actions to execute when an element is scrolled.
 */
angular.module('onm.scroll', [])
 .directive('whenScrolled', function() {
    return function(scope, elm, attr) {
      var raw = elm[0];

      elm.bind('scroll', function() {
        if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
          scope.$apply(attr.whenScrolled);
        }
      });
    };
});
