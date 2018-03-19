(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AdvertisementCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     * @requires $uibModal
     * @requires timeout
     *
     * @description
     *   Handles actions for advertisement creation and update actions.
     */
    .controller('AdvertisementCtrl', [
      '$controller', '$rootScope', '$scope', '$uibModal', '$timeout',
      function($controller, $rootScope, $scope, $uibModal, $timeout) {
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
        $scope.params = {
          sizes: [
            {
              device: 'desktop',
              height: null,
              width: null
            },
            {
              device: 'tablet',
              height: null,
              width: null
            },
            {
              device: 'phone',
              height: null,
              width: null
            },
          ]
        };

        /**
         * @memberOf AdvertisementCtrl
         *
         * @description
         *  List of positions assigned to the advertisement.
         *
         * @type {Object}
         */
        $scope.positions = [];

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
        $scope.selected = {
          all: {
            categories: false,
            user_groups: false
          }
        };

        /**
         * @memberOf AdvertisementCtrl
         *
         * @description
         *  Object for UI elements.
         *
         * @type {Object}
         */
        $scope.ui = {
          categories: [],
          user_groups: [],
          categories_all: true,
          positions_collapsed: true,
          hidden_elements: 0,
        };

        /**
         * @memberOf AdvertisementCtrl
         *
         * @description
         *  Boolean that tells the UI if it is loading.
         *
         * @type {Object}
         */
        $scope.loading = true;

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
          if ($scope.selected.all.categories) {
            $scope.ui.categories = $scope.extra.categories.map(function(e) {
              return e.id;
            });

            return;
          }

          $scope.ui.categories = [];
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
         * @function countHiddenSelectedPositions
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Returns the number of positions hidden for provided range.
         */
        $scope.countHiddenSelectedPositions = function() {
          var containerHeight = $('.positions-selected-list').height();
          var positions = $('.positions-selected-list .position');

          var hiddenElements = positions.filter(function() {
            return $(this).position().top > containerHeight + 41;
          }).length;

          $scope.ui.hidden_elements = hiddenElements;

          return $scope.ui.hidden_elements;
        };

        /**
         * @function countPositionsSelectedbyRange
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Returns the number of positions selected for provided range.
         *
         * @param {Integer} start the star of the range
         * @param {Integer} end   the end of the range
         */
        $scope.countPositionsSelectedbyRange = function(start, finish) {
          if ($scope.positions === null ||
              !angular.isArray($scope.positions)
          ) {
            return 0;
          }

          return $scope.positions.filter(function(e) {
            return start <= e && (!finish || e <= finish);
          }).length;
        };

        /**
         * @function addSize
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Add new input for advertisement size
         */
        $scope.addSize = function() {
          $scope.params.sizes.push({ width: null, height: null });
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
          var pattern = /googletag\.defineSlot\('([^']*)',\s*\[(\d*),\s*(\d*)\]/;
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

          for (var i = 0; i < $scope.params.sizes.length; i++) {
            if (!$scope.params.sizes[i].width || !$scope.params.sizes[i].height) {
              empty++;
            }
          }

          return empty;
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
        $scope.init = function(params, categories, positions) {
          if (params) {
            $scope.params = params;
          }

          if (!positions || positions.constructor !== Array) {
            positions = [];
          }
          $scope.positions = positions;

          if (!$scope.params.devices ||
            !angular.isObject($scope.params.devices)) {
            $scope.params.devices = {
              desktop: 1,
              tablet: 1,
              phone: 1
            };
          }

          if (!$scope.params.sizes) {
            $scope.params.sizes = [];

            // Force sizes initialization
            $scope.parseDevices($scope.params.devices);
          }

          if ($scope.params.user_groups &&
            angular.isArray($scope.params.user_groups)
          ) {
            $scope.ui.user_groups = $scope.params.user_groups;
          }

          $scope.ui.categories_all = true;

          if (categories && angular.isArray(categories)) {
            if (categories.length > 0) {
              $scope.ui.categories_all = false;
            }

            $scope.ui.categories = categories.map(function(e) {
              return parseInt(e);
            });
          }

          var orientations = [ 'top', 'right', 'bottom', 'left' ];

          if (!$scope.params.orientation ||
            orientations.indexOf($scope.params.orientation) === -1
            ) {
            $scope.params.orientation = 'top';
          }

          // Parse and convert old height and width to new sizes
          if ($scope.params.width && $scope.params.height) {
            $scope.params.sizes = [];

            var devices = [ 'desktop', 'tablet', 'phone' ];

            var totalW = angular.isArray($scope.params.width) ?
            $scope.params.width.length : 1;
            var totalH = angular.isArray($scope.params.height) ?
            $scope.params.height.length : 1;
            var totalD = $scope.params.devices.desktop +
            $scope.params.devices.tablet + $scope.params.devices.phone;
            var total = Math.max(totalH, totalW, totalD);

            if (!angular.isArray($scope.params.height)) {
              var value = $scope.params.height;

              $scope.params.height = _.fill(new Array(total), value);
            }

            if (!angular.isArray($scope.params.width)) {
              var value = $scope.params.width;

              $scope.params.width = _.fill(new Array(total), value);
            }

            for (var i = 0; i < total; i++) {
              var item = {
                height: parseInt($scope.params.height[i]),
                width:  parseInt($scope.params.width[i])
              };

              if (i < 3) {
                item.device = devices[i];
              }

              $scope.params.sizes.push(item);
            }
          }

          $scope.loading = false;
          $scope.collapsed = true;

          $timeout(function() {
            $scope.countHiddenSelectedPositions();
          }, 2000);
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
          return !$scope.params.sizes || !$scope.params.sizes[index] ||
          !$scope.params.sizes[index].width ||
          !$scope.params.sizes[index].height;
        };

        /**
         * @function isInterstitial
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Checks if the advertisement is an interstitial.
         *
         * @return {Boolean} True if the advertisement is an interstitial. False
         *                   otherwise
         */
        $scope.isInterstitial = function() {
          if (!angular.isArray($scope.positions)) {
            return (parseInt($scope.positions) + 50) % 100 === 0;
          }

          for (var i = 0; i < $scope.positions.length; i++) {
            var position = parseInt($scope.positions[i]);

            if ((position + 50) % 100 === 0) {
              return true;
            }
          }

          return false;
        };

        /**
         * @function parseDevices
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Parses the devices and initializes the array of sizes.
         *
         * @param {Object} devices The list of devices.
         */
        $scope.parseDevices = function(devices) {
          var indexes = {
            desktop: 0,
            tablet: 1,
            phone: 2
          };

          for (var i in devices) {
            // Sizes for device
            var sizes = $scope.params.sizes.filter(function(e) { // eslint-disable-line no-loop-func
              return e.device === i;
            });

            if (devices[i] && sizes.length === 0) {
              $scope.params.sizes.splice(
                indexes[i],
                0,
                {
                  height: null,
                  device: i,
                  width: null
                }
              );
            }

            if (!devices[i]) {
              $scope.params.sizes = _.difference($scope.params.sizes, sizes);
            }
          }
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
          $scope.params.sizes.splice(index, 1);
        };

        /**
         * @function togglePosition
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Adds or removes a position from the positions list.
         *
         * @param {Integer} id the id to add or remove.
         */
        $scope.togglePosition = function(id) {
          if (!angular.isArray($scope.positions)) {
            $scope.positions = [];
          }

          if ($scope.positions.indexOf(id) < 0) {
            $scope.positions.push(id);
          } else {
            var index = $scope.positions.indexOf(id);

            $scope.positions.splice(index, 1);
          }
        };

        // Adds/removes sizes when devices changes
        $scope.$watch('params.devices', function(nv, ov) {
          if (!ov || ov === nv) {
            return;
          }

          $scope.parseDevices(nv);
        }, true);

        // Updates params_width and params_height when sizes change
        $scope.$watch('params.sizes', function(nv) {
          if (!angular.isArray(nv)) {
            return;
          }

          $scope.json_sizes = angular.toJson(nv);
        }, true);

        // Watch script to detect Google DFP advertisement.
        $scope.$watch('script', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          $scope.checkGoogleDFP(nv);
        });

        // Updates selected all flag when categories change
        $scope.$watch('ui.categories_all', function() {
          $scope.categories = null;
        });

        // Updates selected all flag when categories change
        $scope.$watch('ui.categories', function(nv) {
          $scope.selected.all.categories = false;

          if (nv.length === $scope.extra.categories.length) {
            $scope.selected.all.categories = true;
          }

          if (!$scope.ui.categories_all) {
            $scope.categories = angular.toJson(nv);
          }
        }, true);

        // Updates selected all flag when groups change
        $scope.$watch('ui.user_groups', function(nv) {
          $scope.selected.all.user_groups = false;

          if (nv.length === $scope.extra.user_groups.length) {
            $scope.selected.all.user_groups = true;
          }

          $scope.user_groups = angular.toJson(nv);
        }, true);

        // Updates hidden selected positions text message when some position changes
        // or the user clicks the collapse button.
        $scope.$watch('[ ui.positions_collapsed, positions ]', function() {
          $timeout(function() {
            $scope.countHiddenSelectedPositions();
          }, 50);
        }, true);
      }
    ]);
})();
