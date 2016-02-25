/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('UserCtrl', [
  '$controller', '$http', '$modal', '$scope', 'routing',
  function($controller, $http, $modal, $scope, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.confirmUser = function() {
      if (
        ($scope.user.activated == '1' && $scope.user.type == '0' && $scope.activated == '0') ||
        ($scope.user.activated == '1' && $scope.user.type == '0' && $scope.type == '1')
      ) {
        var modal = $modal.open({
          templateUrl: 'modal-update-selected',
          backdrop: 'static',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
                name:           $scope.user.id ? 'update' : 'create',
                backend_access: true,
                value:          1,
                checkPhone:     $scope.checkPhone,
                checkVat:       $scope.checkVat,
                extra:          $scope.extra,
                saveBilling:    $scope.saveBilling,
              };
            },
            success: function() {
              return null;
            }
          }
        });

        modal.result.then(function(response) {
          if (response) {
            $('form').submit();
          }
        });
      } else {
        $('form').submit();
      }
    };

    $scope.saveBilling = function(template) {
      var url = routing.generate('backend_ws_store_billing');
      var data = $scope.extra.billing;
      $http.post(url, data).success(function() {
        template.step = 2;
      });
    };

    $scope.checkPhone = function(t) {
      var url = routing.generate('backend_ws_store_check_phone',
          { country: $scope.extra.billing.country, phone: $scope.extra.billing.phone });

      $http.get(url).success(function() {
        t.validPhone = true;
      }).error(function() {
        t.validPhone = false;
      });
    };

    $scope.checkVat = function(t) {
      if (!$scope.extra.billing || !$scope.extra.billing.country ||
          !$scope.extra.billing.phone) {
        t.validPhone = false;
        return;
      }

      var url = routing.generate('backend_ws_store_check_vat',
          { country: $scope.extra.billing.country, vat: $scope.extra.billing.vat });

      $http.get(url).success(function() {
        t.validVat = true;
      }).error(function() {
        t.validVat = false;
      });
    };
  }
]);
