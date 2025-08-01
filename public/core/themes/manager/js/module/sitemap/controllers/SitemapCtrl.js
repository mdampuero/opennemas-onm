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
        $scope.item = {
          perpage: 500,
          total: 100,
          limitdays: 2,
          contentyear: 0,
          album: 0,
          article: 0,
          event: 0,
          photo: 0,
          kiosko: 0,
          letter: 0,
          opinion: 0,
          poll: 0,
          tag: 0,
          video: 0,
        };

        /**
         * @memberOf SitemapCtrl
         * @function generateYears
         *
         * @description
         * Generates an array of years starting from the current year and going back 10 years.
         *
         * @returns {Array}
         */
        $scope.generateYears = function() {
          var currentYear     = new Date().getFullYear();
          var years = [];
          var yearsToGenerate = 11;

          for (var i = 0; i < yearsToGenerate; i++) {
            years.push(currentYear - i);
          }

          return years;
        };

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
          $scope.item = Object.assign({}, $scope.item, response.data);
        }, function() {
          $scope.item = {};
        });
      }
    ]);
})();
