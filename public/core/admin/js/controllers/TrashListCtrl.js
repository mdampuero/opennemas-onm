(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TrashListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires localizer
     * @requires messenger
     *
     * @description
     *   Controller for Trash list.
     */
    .controller('TrashListCtrl', [
      '$controller', 'http', '$uibModal', '$scope', 'localizer', 'messenger', 'oqlEncoder',
      function($controller, http, $uibModal, $scope, localizer, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: null,
          epp: 10,
          in_litter: 1,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @memberOf AlbumListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_trash_delete_item',
          deleteList: 'api_v1_backend_trash_delete_list',
          emptyList:  'api_v1_backend_trash_empty_list',
          getList:    'api_v1_backend_trash_get_list',
          patchItem:  'api_v1_backend_trash_restore_item',
          patchList:  'api_v1_backend_trash_restore_list'
        };

        /**
         * @function init
         * @memberOf AlbumListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({
            placeholder: {
              title: '[key] ~ "%[value]%"'
            }
          });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
          $scope.localize($scope.data.extra.categories, 'categories');
        };

        /**
         * @function empty
         * @memberOf TrashListCtrl
         *
         * @description
         *   Confirms empty action.
         */
        $scope.empty = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-empty',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return null;
              },
              success: function() {
                return function() {
                  return http.put($scope.routes.emptyList);
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
          });
        };

        /**
         * @function restore
         * @memberOf TrashListCtrl
         *
         * @description
         *   Confirms restore action.
         *
         * @param {Integer} id The item id.
         */
        $scope.restore = function(id) {
          var modal = $uibModal.open({
            templateUrl: 'modal-restore',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return null;
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.patchItem,
                    params: { id: id }
                  };

                  return http.patch(route, { in_litter: 0 });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
          });
        };

        /**
         * @function restoreSelected
         * @memberOf TrashListCtrl
         *
         * @description
         *   Confirms restore action.
         */
        $scope.restoreSelected = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-restore',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function() {
                  var route = $scope.routes.patchList;

                  return http.patch(route, { ids: $scope.selected.items,
                    in_litter: 0 });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.selected = { all: false, items: [] };
              $scope.list();
            }
          });
        };
      }
    ]);
})();
