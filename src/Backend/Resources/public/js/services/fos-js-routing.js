/**
 * Service to handle route generation by using FosJsRouting.
 */
angular.module('BackendApp.services').service('fosJsRouting', function() {
    /**
     * Generates an URL from a route name and an object with route parameters.
     *
     * @param  string route  Route name
     * @param  object params Object with route parameters
     * @return string        Generated URL.
     */
    this.generate = function(route, params) {
        return Routing.generate(route, params);
    }
})
