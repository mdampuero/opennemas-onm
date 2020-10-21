/**
 * Strips tags from HTML.
 */
angular.module('BackendApp.filters').filter('striptags', function() {
  'use strict';

  /**
   * Strips tags from HTML.
   *
   * @param  string The string with HTML tags and entities.
   * @return string The string without HTML tags and entities.
   */
   return function(input) {
    input = input.replace(/(<([^>]+)>)/ig,"");
    input = $('<div />').html(input).text();

    return input;
  };
});
