(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name SitemapCtrl
     *
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in users listing.
     */
    .controller('SitemapCtrl', [
      '$scope', '$uibModal', 'http', 'messenger',
      function($scope, $uibModal, http, messenger) {
        $scope.save = function() {
          var modal = $uibModal.open(
            {
              templateUrl: '/managerws/template/sitemap:modal.' + appVersion + '.tpl',
              backdrop: 'static',
              controller: 'modalCtrl',
              resolve: {
                template: function() {
                  return {};
                },
                success: function() {
                  return function(modalWindow) {
                    $scope.saving = 1;

                    http.post('manager_ws_sitemap_save', $scope.item).then(function(response) {
                      modalWindow.close({ data: response.data, success: true });
                    }, function(response) {
                      modalWindow.close({ data: response.data, success: false });
                    });
                  };
                }
              }
            });

          modal.result.then(function(response) {
            $scope.saving = 0;
            messenger.post(response.data);
          }, function(response) {
            $scope.saving = 0;
            messenger.post(response.data);
          });
        };

        var route = {
          name: 'manager_ws_sitemap_show'
        };

        return http.get(route).then(function(response) {
          $scope.item = response.data;
        }, function() {
          $scope.item = {};
        });
      }
    ]);
})();
