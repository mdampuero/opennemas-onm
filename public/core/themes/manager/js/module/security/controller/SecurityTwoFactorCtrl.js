(function() {
  'use strict';

  angular.module('ManagerApp.controllers')
    .controller('SecurityTwoFactorCtrl', [
      '$controller', '$location', '$scope', '$timeout', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.columns = {
          collapsed: 1,
          selected: [
            'name',
            'internal_name',
            'two_factor_enabled'
          ]
        };

        $scope.criteria = { epp: 25, page: 1 };

        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or internal_name ~ "[value]" or contact_mail ~ "[value]" or domains ~ "[value]"'
            }
          });

          var oql = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_security_two_factor',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.items = response.data.results;
            $scope.total = response.data.total;
            $scope.extra = response.data.extra || {};
            $scope.filteredItems = $scope.items;

            $('body').animate({ scrollTop: '0px' }, 1000);
          }, function(response) {
            $scope.loading = 0;
            messenger.post(response.data);
          });
        };

        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1 };
          $scope.list();
        };

        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.set('security-two-factor-columns', $scope.columns);
          }
        }, true);

        if (webStorage.local.get('security-two-factor-columns')) {
          $scope.columns = webStorage.local.get('security-two-factor-columns');
        }

        oqlDecoder.configure({
          ignore: [ 'internal_name', 'contact_mail', 'domains' ]
        });

        if ($location.search().oql) {
          $scope.criteria = angular.extend(
            {},
            $scope.criteria,
            oqlDecoder.decode($location.search().oql)
          );
        }

        $scope.getItemId = function(item) {
          return item.id;
        };

        $scope.toggleTwoFactor = function(item) {
          if (item.twoFactorLoading) {
            return;
          }

          item.twoFactorLoading = true;
          var targetState       = !item.two_factor_enabled;

          var route = {
            name: 'manager_ws_security_two_factor_save',
            params: {
              id: item.id,
              enabled: targetState ? 1 : 0
            }
          };

          http.put(route).then(function(response) {
            item.two_factor_enabled = targetState;
            messenger.post(response.data);
            item.twoFactorLoading = false;
          }, function(response) {
            messenger.post(response.data);
            item.twoFactorLoading = false;
          });
        };

        $scope.list();
      }
    ]);
})();
