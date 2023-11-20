(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name UserListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires $uibModal
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in users list.
     */
    .controller('UserListCtrl', [
      '$controller', '$scope', 'oqlEncoder', '$uibModal', 'http', 'messenger',
      function($controller, $scope, oqlEncoder, $uibModal, http, messenger) {
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
          patchList:  'api_v1_backend_user_patch_list',
          moveItem:   'api_v1_backend_user_move_item',
        };

        /**
         * @function confirm
         * @memberOf UserCtrl
         *
         * @description
         *   Confirm user update.
         */
        $scope.confirm = function(property, value, item) {
          if (item) {
            $scope.patch(item, property, value);
            return;
          }
          $scope.patchSelected(property, value);
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
          $scope.app.columns.selected = [ 'picture', 'name', 'email', 'usergroups', 'social', 'enabled' ];
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

        /**
         * @function move
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm move action.
         *
         * @param {Integer} id The tag id.
         * @param {Object} tag The tag object.
         */
        $scope.move = function(id, item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-move',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  exclude: [ id ],
                  source: item
                };
              },
              success: function() {
                return function(modal, template) {
                  var route = {
                    name: $scope.routes.moveItem,
                    params: { id: id }
                  };

                  return http.put(route, { target: template.target[0] });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };
      }
    ]);
})();
