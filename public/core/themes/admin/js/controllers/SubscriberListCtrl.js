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
      '$controller', '$scope', 'oqlEncoder', '$uibModal', 'http', 'messenger',
      function($controller, $scope, oqlEncoder, $uibModal, http, messenger) {
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
          patchList:  'api_v1_backend_subscriber_patch_list',
          importSubscriber: 'api_v1_backend_subscriber_import',
        };

        /**
         * @function confirm
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Confirm subscriber update.
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

        /**
         * @function import
         * @memberOf SubscriberListCtrl
         *
         * @description
         *  Open import modal for starting import process.
         *
         * @returns {Promise}
         */
        $scope.import = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-import',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  subscriber: $scope.subscriber
                };
              },
              success: function() {
                return function(modal, template) {
                  const reader = new FileReader();

                  var route = {
                    name: $scope.routes.importSubscriber,
                  };

                  if (template.file.type !== 'text/csv') {
                    return messenger.post({
                      type: 'error',
                      message: 'Invalid file type. Please upload a CSV file.'
                    });
                  }

                  reader.readAsText(template.file);
                  reader.onload = function() {
                    var content = reader.result;

                    return http.put(route, { csv_file: content }).then(function() {
                      $scope.list();
                    });
                  };
                };
              }
            }
          });

          modal.result.then(function(response) {
            // messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
          });
        };
      }
    ]);
})();
