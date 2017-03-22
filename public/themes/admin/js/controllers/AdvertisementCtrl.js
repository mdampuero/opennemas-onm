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
    '$controller', '$uibModal', '$scope',
    function($controller, $uibModal, $scope) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @memberOf AdvertisementCtrl
       *
       * @description
       *  The advertisement parameters.
       *
       * @type {Object}
       */
      $scope.params = {};

      /**
       * @memberOf AdvertisementCtrl
       *
       * @description
       *  Flags for collapsed items.
       *
       * @type {Object}
       */
      $scope.expanded = {};

      /**
       * @memberOf AdvertisementCtrl
       *
       * @description
       *  Object to track selected items.
       *
       * @type {Object}
       */
      $scope.selected = { all: { categories: false, user_groups: false } };

      /**
       * @memberOf AdvertisementCtrl
       *
       * @description
       *  The list of sizes.
       *
       * @type {Array}
       */
      $scope.sizes = [];

      /**
       * @memberOf AdvertisementCtrl
       *
       * @description
       *  Object for UI elements.
       *
       * @type {Object}
       */
      $scope.ui = { categories: [], user_groups: [] };

      /**
       * @function areAllCategoriesSelected
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Checks if all user groups are selected.
       *
       * @return {Boolean} True if all user groups are selected. False
       *                   otherwise.
       */
      $scope.areAllCategoriesSelected = function() {
        $scope.ui.categories = [];

        if ($scope.selected.all.categories) {
          $scope.ui.categories = $scope.extra.categories.map(function(e) {
            return e.id;
          });
        }
      };

      /**
       * @function areAllUserGroupsSelected
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Checks if all user groups are selected.
       *
       * @return {Boolean} True if all user groups are selected. False
       *                   otherwise.
       */
      $scope.areAllUserGroupsSelected = function() {
        $scope.ui.user_groups = [];

        if ($scope.selected.all.user_groups) {
          $scope.ui.user_groups = $scope.extra.user_groups.map(function(e) {
            return e.id;
          });
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

      /**
       * @function init
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Initialize list of other DFP ads sizes.
       *
       * @param Object params The advertisement params
       */
      $scope.init = function(params, categories) {
        if (params) {
          $scope.params = params;
        }

        if ($scope.params.width && angular.isArray($scope.params.width) &&
            $scope.params.height && angular.isArray($scope.params.height)) {
          $scope.sizes = [];
          for (var i = 0; i < params.width.length; i++) {
            $scope.sizes.push({
              width:  parseInt(params.width[i]),
              height: parseInt(params.height[i])}
              );
          }
        }

        if ($scope.params.user_groups &&
            angular.isArray($scope.params.user_groups)) {
          $scope.ui.user_groups = $scope.params.user_groups;
        }

        if (categories && angular.isArray(categories)) {
          // Remove frontpage category
          categories = categories.filter(function(e) {
            return parseInt(e) !== 0;
          });

          $scope.ui.categories = categories.map(function (e) {
            return parseInt(e);
          });
        }

        if (!$scope.params.devices ||
            !angular.isObject($scope.params.devices)) {
          $scope.params.devices = { desktop: 1, tablet: 1, phone: 1 };
        }

        if (!$scope.params.orientation) {
          $scope.params.orientation = 'horizontal';
        }
      };

      /**
       * @function countEmpty
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Counts how many sizes have zero or empty values.
       *
       * @return {Integer} The number of empty sizes.
       */
      $scope.countEmpty = function() {
        var empty = 0;

        for (var i = 0; i < $scope.sizes.length; i++) {
          if (!$scope.sizes[i].width || !$scope.sizes[i].height) {
            empty++;
          }
        }

        return empty;
      };

      /**
       * @function isEmpty
       * @memberOf AdvertisementCtrl
       *
       * @description
       *   Checks if a size in the list is valid.
       *
       * @param {Integer} The index to check.
       *
       * @return {Boolean} True if the size is valid. False otherwise.
       */
      $scope.isEmpty = function(index) {
        return !$scope.sizes || !$scope.sizes[index] ||
          !$scope.sizes[index].width || !$scope.sizes[index].height;
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

      // Adds/removes sizes when devices changes
      $scope.$watch('params.devices', function(nv) {
        var indexes = { desktop: 0, tablet: 1, phone: 2 };

        for (var i in nv) {
          var sizes = $scope.sizes.filter(function(e) {
            return e.type === i;
          });

          if (nv[i] && sizes.length === 0) {
            $scope.sizes.splice(indexes[i], 0, { height: 0, type: i, width: 0 });
          }

          if (!nv[i]) {
            $scope.sizes = _.difference($scope.sizes, sizes);
          }
        }
      }, true);

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

      // Updates selected all flag when categories change
      $scope.$watch('ui.categories', function (nv) {
        $scope.selected.all.categories = false;

        if (nv.length === $scope.extra.categories.length) {
          $scope.selected.all.categories = true;
        }

        $scope.categories = angular.toJson(nv);
      }, true);

      // Updates selected all flag when groups change
      $scope.$watch('ui.user_groups', function (nv) {
        $scope.selected.all.user_groups = false;

        if (nv.length === $scope.extra.user_groups.length) {
          $scope.selected.all.user_groups = true;
        }

        $scope.user_groups = angular.toJson(nv);
      }, true);

      // Track all radio buttons type_advertisement and update the model
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
