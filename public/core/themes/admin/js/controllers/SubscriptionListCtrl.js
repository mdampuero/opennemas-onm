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
         *
         */
        $scope.import = function(newsletter) {
          var modal = $uibModal.open({
            templateUrl: 'modal-import',
            backdrop: 'static',
            controller: 'ModalCtrl',
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
                  const reader = new FileReader();

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

                    return http.put(route,
                      {
                        csv_file: content,
                        newsletter: newsletter
                      }).then(function() {
                      $scope.list();
                    });
                  };
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response.success) {
              $scope.list();
            }
          });
        };
      }
    ]);
})();
