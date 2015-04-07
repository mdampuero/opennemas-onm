/**
 * Directive to get an avatar from gravatar given an email.
 */
angular.module('onm.paginator', []).directive('onmPaginator', function ($compile) {
    return {
        restrict: 'E',
        link: function ($scope, $element, $attrs) {
            // [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            console.log($element);
        }
    };
});
