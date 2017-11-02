/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('NewsletterSubscriptorListCtrl', [
  '$location', '$uibModal', '$scope', '$timeout', 'http', 'messenger', 'oqlEncoder', 'queryManager', '$controller',
  function($location, $uibModal, $scope, $timeout, http, messenger, oqlEncoder, queryManager, $controller) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentListCtrl', {$scope: $scope}));

    /**
     * Permanently removes a contents by using a confirmation dialog
     */
    $scope.delete = function(content) {
      var modal = $uibModal.open({
        templateUrl: 'modal-delete',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              content: content
            };
          },
          success: function() {
            return function() {
              var route = {
                name:   'backend_ws_newsletter_subscriptor_delete',
                params: { id: content.id }
              };

              return http.get(route);
            };
          }
        }
      });

      modal.result.then(function(response) {
        messenger.post(response.data.messages);

        if (response.success) {
          $scope.list($scope.route);
        }
      });
    };

    /**
     * Permanently removes a list of contents by using a confirmation dialog
     */
    $scope.deleteSelected = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $uibModal.open({
        templateUrl: 'modal-delete-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              return http.post('backend_ws_newsletter_subscriptor_batch_delete',
                { selected: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        messenger.post(response.data.messages);

        $scope.selected.total = 0;
        $scope.selected.contents = [];

        if (response.success) {
          $scope.list($scope.route);
        }
      });
    };

    /**
     * Updates the array of contents.
     *
     * @param string route Route name.
     */
    $scope.list = function(route) {
      $scope.loading = 1;
      $scope.selected = { all: false, contents: [] };

      oqlEncoder.configure({
        placeholder: {
          name: 'name ~ "%[value]%" or email ~ "%[value]%"',
        }
      });

      var oql   = oqlEncoder.getOql($scope.criteria);
      var route = {
        name: $scope.route,
        params:  {
          contentType: $scope.criteria.content_type_name,
          oql: oql
        }
      };

      $location.search('oql', oql);

      http.get(route).then(function(response) {
        $scope.total = parseInt(response.data.total);
        $scope.contents         = response.data.results;
        $scope.map              = response.data.map;

        if (response.data.hasOwnProperty('extra')) {
          $scope.extra = response.data.extra;
        }

        // Disable spinner
        $scope.loading = 0;
      }, function () {
        $scope.loading = 0;

        messenger.post({
          message: 'Error while fetching data from backend',
          type:    'error'
        });
      });
    };
}]);
