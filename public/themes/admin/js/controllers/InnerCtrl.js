(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  InnerCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     *
     * @description
     *   This is the base controller for forms when no full SPA edition
     *   implemented.
     */
    .controller('InnerCtrl', [
      '$controller', '$scope', 'http',
      function($controller, $scope, http) {
        $.extend(this, $controller('BaseCtrl', { $scope: $scope }));

        /**
         * @memberOf InnerCtrl
         *
         * @description
         *  The list of overlays.
         *
         * @type {Object}
         */
        $scope.overlay = {};

        /**
         * @function removeImage
         * @memberOf InnerCtrl
         *
         * @description
         *   Removes the given image from the scope.
         *
         * @param string image The image to remove.
         */
        $scope.removeImage = function(image) {
          delete $scope[image];
        };

        /**
         * @function removeItem
         * @memberOf InnerCtrl
         *
         * @description
         *   Removes an item from an array of related items.
         *
         * @param string  from  The array name in the current scope.
         * @param integer index The index of the element to remove.
         */
        $scope.removeItem = function(from, index) {
          var keys  = from.split('.');
          var model = $scope;

          for (var i = 0; i < keys.length - 1; i++) {
            if (!model[keys[i]]) {
              model[keys[i]] = {};
            }

            model = model[keys[i]];
          }

          if (angular.isArray(model[keys[i]])) {
            model[keys[i]].splice(index, 1);
            return;
          }

          model[keys[i]] = null;
        };

        /**
         * @function toggleOverlay
         * @memberOf InnerCtrl
         *
         * @description
         *   Enables/disables an overlay by name.
         *
         * @param {String} name The overlay name.
         */
        $scope.toggleOverlay = function(name) {
          $scope.overlay[name] = !$scope.overlay[name];
        };

        // Initialize the scope with the input/select values.
        $('input, select, textarea').each(function(index, element) {
          var name = $(element).attr('name');
          var value = $(element).val();

          if ($(element).attr('type') === 'number') {
            value = parseInt(value);
          }

          $scope[name] = value;
        });
      }
    ]);
})();
