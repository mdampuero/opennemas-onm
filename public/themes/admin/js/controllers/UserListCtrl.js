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
          delete:         'api_v1_backend_user_delete',
          deleteSelected: 'api_v1_backend_users_delete',
          list:           'api_v1_backend_users_list',
          patch:          'api_v1_backend_user_patch',
          patchSelected:  'api_v1_backend_users_patch'
        };

        /**
         * @function confirmUser
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.confirm = function(property, value, item) {
          if ($scope.master || !value) {
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
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: $scope.id ? 'update' : 'create',
                  backend_access: true,
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
          $scope.columns.key     = 'user-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or username ~ "[value]"',
              user_group_id: '([key] = "[value]" and status != 0)',
            }
          });

          $scope.list();
        };
      }
    ]);
})();
