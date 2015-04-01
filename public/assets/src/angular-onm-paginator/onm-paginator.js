/**
 * Directive to get an avatar from gravatar given an email.
 */
angular.module('onm.onm-paginator', []).directive('onm-paginator', function ($compile) {
    return {
        restrict: 'AC',
        link: function ($scope, $element, $attrs) {
            // [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            console.log($element);
        }
    };
});
