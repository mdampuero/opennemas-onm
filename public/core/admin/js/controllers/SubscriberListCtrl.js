(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in subscribers list.
     */
    .controller('SubscriberListCtrl', [
      '$controller', '$scope', '$uibModal', 'oqlEncoder',
      function($controller, $scope, $uibModal, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriberListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_subscriber_delete_item',
          deleteList: 'api_v1_backend_subscriber_delete_list',
          getList:    'api_v1_backend_subscriber_get_list',
          patchItem:  'api_v1_backend_subscriber_patch_item',
          patchList:  'api_v1_backend_subscriber_patch_list'
        };

        /**
         * @function confirm
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Shows a modal to confirm subscriber update.
         */
        $scope.confirm = function(property, value, item) {
          var hasUsers = item ? item.type !== 1 : $scope.items
            .filter(function(e) {
              return $scope.selected.items.indexOf(e.id) !== -1 && e.type !== 1;
            }).length > 0;

          if (!value || !hasUsers || $scope.backup.master) {
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
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: {
              name: '(name ~ "[value]" or email ~ "[value]")',
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
