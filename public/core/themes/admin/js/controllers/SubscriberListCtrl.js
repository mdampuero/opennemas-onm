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
         *   Opens a modal window to start the subscriber import process.
         *   This process involves uploading a CSV file and selecting a list
         *   to import the subscribers into. The CSV file is read on the client side
         *   and then sent to the backend for processing. Appropriate validations are
         *   performed to ensure the required data is provided.
         *
         * @param {Object} subscriptions - The lists to import subscribers to.
         *
         * @returns {Promise} A promise that resolves when the import process is
         * successfully completed and the modal is closed.
         */
        $scope.import = function(subscriptions) {
          var modal = $uibModal.open({
            templateUrl: 'modal-import',
            backdrop: 'static',
            controller: 'SubscriberModalCtrl',
            resolve: {
              template: function() {
                const validLists = angular.copy(subscriptions);

                // Delete list "any"
                delete validLists[0];

                return {
                  type: 2,
                  lists: validLists
                };
              },
              success: function() {
                return function(modal, template) {
                  const reader = new FileReader();

                  var route = {
                    name: $scope.routes.importSubscriber,
                  };

                  if (!template.selectList) {
                    return messenger.post({
                      type: 'error',
                      message: 'Please select a list to import subscribers.'
                    });
                  }

                  if (template.file.type !== 'text/csv') {
                    return messenger.post({
                      type: 'error',
                      message: 'Invalid file type. Please upload a CSV file.'
                    });
                  }

                  reader.readAsText(template.file);
                  reader.onload = function() {
                    var content = reader.result;

                    return http.put(route, {
                      csv_file: content,
                      newsletter: template.selectList
                    }).then(function(response) {
                      modal.close();
                      messenger.post(response.data.messages[0]);
                      $scope.list();
                    });
                  };
                };
              }
            }
          });
        };
      }
    ]);
})();
