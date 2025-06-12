(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriptionListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in subscriptions list.
     */
    .controller('SubscriptionListCtrl', [
      '$controller', '$scope', 'oqlEncoder', '$uibModal', 'http', 'messenger',
      function($controller, $scope, oqlEncoder, $uibModal, http, messenger) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_subscription_delete_item',
          deleteList: 'api_v1_backend_subscription_delete_list',
          getList:    'api_v1_backend_subscription_get_list',
          patchItem:  'api_v1_backend_subscription_patch_item',
          patchList:  'api_v1_backend_subscription_patch_list',
          importItem: 'api_v1_backend_subscription_import_item'
        };

        /**
         * @inheritdoc
         */
        $scope.getItemId = function(item) {
          return item.pk_user_group;
        };

        /**
         * @function init
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });
          $scope.list();
        };

        /**
         * @function importSelected
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Checks which newsletter lists have been selected and initiates the
         *   import process for those selections.
         *   If no items are selected, it shows an error message using the
         *   messenger service.
         */
        $scope.importSelected = function() {
          var selected = $scope.selected.items;

          if (!selected.length) {
            messenger.post({
              type: 'error',
              message: 'Please select at least one item to import.'
            });
          }

          $scope.import(selected);
        };

        /**
         * @function import
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Opens a modal to handle the import process for a given newsletter list.
         *   The modal allows the user to upload a CSV file which is then read and sent
         *   to the backend for processing. If the uploaded file is not a valid CSV, an
         *   error message is shown. Upon successful import, the modal is closed and
         *   the view is reinitialized.
         *
         * @param {Object} newsletter - The selected newsletter list object to
         * import subscribers into.
         */
        $scope.import = function(newsletter) {
          var modal = $uibModal.open({
            templateUrl: 'modal-import',
            backdrop: 'static',
            controller: 'SubscriberModalCtrl',
            resolve: {
              template: function() {
                // Type 1 is only upload file and type 2 is upload and select.
                return {
                  newsletter: newsletter,
                  type: 1
                };
              },
              success: function() {
                return function(modal, template) {
                  const reader  = new FileReader();

                  var route = {
                    name: $scope.routes.importItem,
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

                    return http.put(route, {
                      csv_file: content,
                      newsletter: newsletter
                    }).then(function(response) {
                      modal.close();
                      messenger.post(response.data);
                      $scope.init();
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
