(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ContentRestListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('ContentRestListCtrl', [
      '$controller', '$scope', '$uibModal', 'oqlEncoder',
      function($controller, $scope, $uibModal, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @function getId
         * @memberOf ContentistCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The item id.
         */
        $scope.getId = function(item) {
          return item.pk_content;
        };

        /**
         * @function sendToTrash
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.sendToTrash = function(item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name:     $scope.id ? 'update' : 'create',
                  selected: $scope.selected.items.length,
                  value:    1,
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
                $scope.patch(item, 'in_litter', 1)
                  .then(function() {
                    $scope.list();
                  });
                return;
              }

              $scope.patchSelected('in_litter', 1)
                .then(function() {
                  $scope.list();
                });
            }
          });
        };
      }
    ]);
})();
