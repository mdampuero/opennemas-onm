/**
 * Service to handle route generation by using FosJsRouting.
 */
angular.module('onm.routing', []).provider('routing', function() {
  /**
   * The base route (before #)
   *
   * @type string
   */
  this.base = '';

  /**
   * Generates a route.
   *
   * @param string route  The route name.
   * @param Object params The route parameters.
   *
   * @return string The generated route.
   *
   * @note This route is used in Ajax requests.
   */
  this.generate = function(route, params) {
    return Routing.generate(route, params);
  };

  /**
   * Generates a route for angular with the hashbang (#).
   *
   * @param string route  The route name.
   * @param Object params The route parameters.
   *
   * @return string The generated route.
   *
   * @note This route is used directly in template.
   */
  this.ngGenerate = function(route, params) {
    var url = Routing.generate(route, params);

    return url.replace(this.base, '#');
  };

  /**
   * Generates a route for angular without the hashbang (#).
   *
   * @param string route  The route name.
   * @param Object params The route parameters.
   *
   * @return string The generated route.
   *
   * @note This route is used by the $location service.
   */
  this.ngGenerateShort = function(route, params) {
    var url = Routing.generate(route, params);

    return url.replace(this.base, '').replace('%3A', ':');
  };

  /**
   * Sets the base route.
   *
   * @param string base The base route.
   */
  this.setBaseRoute = function(base) {
    this.base = base;
  };

  /**
   * Returns the current service.
   *
   * @return Object The current object.
   */
  this.$get = function() {
    return this;
  };
});
