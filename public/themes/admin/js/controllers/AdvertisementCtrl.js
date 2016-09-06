(function () {
  'use strict';

  /**
   * @ngdoc controller
   * @name  AdvertisementCtrl
   *
   * @requires $controller
   * @requires $uibModal
   * @requires $scope
   *
   * @description
   *   Handles actions for advertisement inner.
   */
  angular.module('BackendApp.controllers').controller('AdvertisementCtrl', [
    '$controller', '$uibModal', '$scope', 'messenger',
    function($controller, $uibModal, $scope, messenger) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      $scope.sizes = [ { width: 0, height: 0 } ];

      /**
       * @function init
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Initialize list of other DFP ads sizes.
       *
       * @param Object params The advertisement params
       */
      $scope.init = function(params) {
        if (!angular.isArray(params.width) || !angular.isArray(params.height)) {
          return;
        }

        $scope.sizes = [];

        for (var i = 0; i < params.width.length; i++) {
          $scope.sizes.push({
            width:  parseInt(params.width[i]),
            height: parseInt(params.height[i])}
          );
        }
      };

      /**
       * @function addSize
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Add new input for advertisement size
       */
      $scope.addSize = function() {
        $scope.sizes.push({ width: 0, height: 0 });
      };

      /**
       * @function removeInput
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Removes an advertisement size input.
       *
       * @param integer index The index of the input to remove.
       */
      $scope.removeSize = function(index) {
        $scope.sizes.splice(index, 1);
      };

      /**
       * @function checkGoogleDFP
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Checks if the string contains a Google DFP advertisement.
       *
       * @param {String} str The string to check.
       */
      $scope.checkGoogleDFP = function(str) {
        var pattern = /googletag\.defineSlot\('([^\']*)\',\s*\[(\d*),\s*(\d*)\]/;
        var matches = str.match(pattern);

        if (pattern.test(str)) {
          var modal = $uibModal.open({
            templateUrl: 'modal-dfp-detected',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  googledfp_unit_id: matches[1],
                  params_width: parseInt(matches[2]),
                  params_height: parseInt(matches[3])
                };
              },
              success: function() {
                return false;
              }
            }
          });

          modal.result.then(function(response) {
            if (!response) {
              return;
            }

            var matches = str.match(pattern);

            $scope.with_script = 3;
            $scope.googledfp_unit_id = matches[1];
            $scope.params_width = parseInt(matches[2]);
            $scope.params_height = parseInt(matches[3]);

            $scope.script = null;
          });
        }
      };

      // Updates params_width and params_height when sizes change
      $scope.$watch('sizes', function(nv) {
        if (!angular.isArray(nv)) {
          return;
        }

        var height = [];
        var width  = [];

        for (var i = 0; i < nv.length; i++) {
          width.push(nv[i].width);
          height.push(nv[i].height);
        }

        $scope.params_height = angular.toJson(height);
        $scope.params_width  = angular.toJson(width);
      }, true);

      // Watch script to detect Google DFP advertisement.
      $scope.$watch('script', function(nv, ov) {
        if (nv === ov) {
          return;
        }

        $scope.checkGoogleDFP(nv);
      });

      // Track all radio buttons type_advertisement and update the model property
      // in the $scope
      var type_advertisement_el = $('input[name=type_advertisement]');
      $scope.type_advertisement = parseInt(type_advertisement_el.val());
      type_advertisement_el.on('change', function() {
        var value = parseInt(this.value);
        $scope.$apply(function(){
          $scope.type_advertisement = value;
        });
      });
    }
  ]);
})();

