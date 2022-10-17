(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PhotoListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for photo list.
     */
    .controller('PhotoListCtrl', [
      '$controller', '$scope', 'http', '$timeout', 'oqlEncoder',
      function($controller, $scope, http, $timeout, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'photo',
          category_id: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @memberOf PhotoListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_photo_delete_item',
          deleteList: 'api_v1_backend_photo_delete_list',
          getList:    'api_v1_backend_photo_get_list',
          patchItem:  'api_v1_backend_photo_patch_item',
          patchList:  'api_v1_backend_photo_patch_list'
        };

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return false;
        };

        /**
         * @function init
         * @memberOf ImageListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [ 'author', 'tags', 'category', 'starttime', 'endtime', 'content_views' ];

          oqlEncoder.configure({
            placeholder: {
              title: '(title ~ "%[value]%" or description ~ "%[value]%")',
              created: '[key] ~ "%[value]%"'
            }
          });

          if ($scope.app.mode === 'grid') {
            $scope.criteria.epp = $scope.getEppInGrid();
          }

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.isModeSupported = function() {
          return true;
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
        };

        /**
         * Saves the last selected item description.
         */
        $scope.saveDescription = function() {
          $scope.saving = true;

          var data = { description: $scope.selected.lastSelected.description };

          var route = {
            name: 'api_v1_backend_photo_patch_item',
            params:  {
              id: $scope.selected.lastSelected.pk_content
            }
          };

          http.put(route, data).then(function() {
            $scope.saving = false;
            $scope.saved = true;

            $timeout(function() {
              $scope.saved = false;
            }, 2000);

            return true;
          }, function() {
            $scope.saving = false;
            $scope.saved = false;
            $scope.error = true;

            $timeout(function() {
              $scope.error = false;
            }, 2000);

            return false;
          });
        };

        // Update epp when mode changes
        $scope.$watch(function() {
          return $scope.app.mode;
        }, function(nv, ov) {
          if (nv === ov) {
            return;
          }

          var epp = $scope.getEppInGrid();

          if (epp !== $scope.criteria.epp) {
            $scope.flags.http.loading = true;
            $scope.criteria.epp = nv === 'grid' ? epp : 10;
          }
        }, true);
      }
    ]);
})();
