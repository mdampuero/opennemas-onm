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
          delete:         'api_v1_backend_subscriber_delete',
          deleteSelected: 'api_v1_backend_subscribers_delete',
          list:           'api_v1_backend_subscribers_list',
          patch:          'api_v1_backend_subscriber_patch',
          patchSelected:  'api_v1_backend_subscribers_patch'
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
            controller: 'modalCtrl',
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
          $scope.columns.key     = 'subscriber-columns';
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
         * @function isSelectable
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Checks if the item is selectable.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the item is selectable. False otherwise.
         */
        $scope.isSelectable = function(item) {
          return $scope.backup.master ||
            $scope.getId(item) !== $scope.backup.id;
        };
      }
    ]);
})();
