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

    this.$get = function () {
        return this;
    };
});
