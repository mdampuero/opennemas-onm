(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name UserListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in users list.
     */
    .controller('UserListCtrl', [
      '$controller', '$scope', '$uibModal', 'oqlEncoder',
      function($controller, $scope, $uibModal, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_user_delete_item',
          deleteList: 'api_v1_backend_user_delete_list',
          getList:    'api_v1_backend_user_get_list',
          patchItem:  'api_v1_backend_user_patch_item',
          patchList:  'api_v1_backend_user_patch_list'
        };

        /**
         * @function confirm
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.confirm = function(property, value, item) {
          if (!value || $scope.backup.master) {
            if (item) {
              $scope.patch(item, property, value);
              return;
            }

            $scope.patchSelected(property, value);
            return;
          }

          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  name:  $scope.id ? 'update' : 'create',
                  value: 1,
                  extra: $scope.data.extra,
                };
              },
              success: function() {
                return null;
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              if (item) {
                $scope.patch(item, property, value);
                return;
              }

              $scope.patchSelected(property, value);
            }
          });
        };

        /**
         * @function init
         * @memberOf UserListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or username ~ "[value]"',
              user_group_id: '([key] = "[value]" and status != 0)',
            }
          });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function(item) {
          return $scope.backup.master ||
            $scope.getItemId(item) !== $scope.backup.id;
        };
      }
    ]);
})();
