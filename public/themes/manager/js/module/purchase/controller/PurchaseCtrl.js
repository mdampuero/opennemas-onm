(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for purchase edition form
     */
    .controller('PurchaseCtrl', [
      '$filter', '$location', '$uibModal', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($filter, $location, $uibModal, $scope, itemService, routing, messenger, data) {
        /**
         * @memberOf PurchaseCtrl
         *
         * @description
         *   The language to edit.
         *
         * @type {String}
         */
        $scope.language = 'en';
        /**
         * @memberOf PurchaseCtrl
         *
         * @description
         *   The purchase object.
         *
         * @type {Object}
         */
        $scope.purchase = {
          body: {
            en: '',
            es: '',
            gl: '',
          },
          instance_id: '0',
          fixed: '0',
          style: 'info',
          title: {
            en: '',
            es: '',
            gl: '',
          },
          type: 'info'
        };

        $scope.languages = {
          'en': 'English',
          'es': 'Spanish',
          'gl': 'Galician',
        };

        /**
         * @memberOf PurchaseCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.extra = data.extra;

        /**
         * @function changeLanguage
         * @memberOf PurchaseCtrl
         *
         * @description
         *   Changes the current language.
         *
         * @param {String} lang The language value.
         */
        $scope.changeLanguage = function(lang) {
          $scope.language = lang;
        };

        /**
         * @function save
         * @memberOf PurchaseCtrl
         *
         * @description
         *   Creates a new purchase.
         */
        $scope.save = function() {
          if ($scope.purchaseForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          if ($scope.purchase.start && angular.isObject($scope.purchase.start)) {
            $scope.purchase.start = $scope.purchase.start.toString();
          }

          if ($scope.purchase.end && angular.isObject($scope.purchase.end)) {
            $scope.purchase.end = $scope.purchase.end.toString();
          }

          itemService.save('manager_ws_purchase_create', $scope.purchase)
            .then(function (response) {
              messenger.post({ message: response.data, type: 'success' });

              if (response.status === 201) {
                // Get new purchase id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                  'manager_purchase_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            }, function(response) {
              $scope.saving = 0;
              messenger.post({ message: response, type: 'error' });
            });
        };

         /**
         * @function update
         * @memberOf PurchaseCtrl
         *
         * @description
         *   Updates an purchase.
         */
        $scope.update = function() {
          if ($scope.purchaseForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          itemService.update('manager_ws_purchase_update', $scope.purchase.id,
            $scope.purchase).success(function (response) {
              messenger.post({ message: response, type: 'success' });
              $scope.saving = 0;
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
              $scope.saving = 0;
            });
        };

        $scope.$on('$destroy', function() {
          $scope.purchase = null;
        });


        if (data.purchase) {
          $scope.purchase = data.purchase;
        }
      }
    ]);
})();
