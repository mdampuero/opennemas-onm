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
        if (params == null) {
          $scope.params_width = [];
          $scope.params_height = [];
        } else {
          if (angular.isArray(params.width)) {
            $scope.params_width = params.width;
          } else if (params.width != '') {
            $scope.params_width = [ params.width ];
          } else {
            $scope.params_width = [];
          }

          if (angular.isArray(params.height)) {
            $scope.params_height = params.height;
          } else if (params.height != '')  {
            $scope.params_height = [ params.height ];
          } else {
            $scope.params_height = [];
          }
        }

        $scope.sizes = [];
        for (var i = 0; i < $scope.params_width.length; i++) {
          $scope.sizes.push({
            width: parseInt($scope.params_width[i]),
            height: parseInt($scope.params_height[i])}
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
        $scope.sizes.push({width: '', height: ''});
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
      $scope.removeInput = function(sizes, index) {
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

      // Watch script to detect Google DFP advertisement.
      $scope.$watch('script', function(nv, ov) {
        if (nv === ov) {
          return;
        }

        $scope.checkGoogleDFP(nv);
      });
    }
  ]);
})();

