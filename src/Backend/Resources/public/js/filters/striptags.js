angular.module('BackendApp.filters', []).filter('striptags', function() {
    return function(input) {
        return input.replace(/(<([^>]+)>)/ig,"");
    };
});
