/**
 * Service to handle route generation by using FosJsRouting.
 */
angular.module('onm.routing', []).provider('fosJsRouting', function() {
    /**
     * Generates a route.
     *
     * @param string route  The route name.
     * @param Object params The route parameters.
     *
     * @return string The generated route.
     */
    this.generate = function(route, params) {
        return Routing.generate(route, params);
    };

    /**
     * Generates a route for angular from a base route.
     *
     * @param string base   The base route pattern.
     * @param string route  The route name.
     * @param Object params The route parameters.
     *
     * @return string The generated route.
     */
    this.ngGenerate = function(base, route, params) {
        var url = Routing.generate(route, params)
        return url.replace(base, '#');
    }

    /**
     * Generates a route for angular from a base route.
     *
     * @param string base   The base route pattern.
     * @param string route  The route name.
     * @param Object params The route parameters.
     *
     * @return string The generated route.
     */
    this.ngGenerateShort = function(base, route, params) {
        var url = Routing.generate(route, params)
        return url.replace(base, '').replace('%3A', ':');
    }

    /**
     * Returns the current service.
     *
     * @return Object The current object.
     */
    this.$get = function () {
        return this;
    };
});
