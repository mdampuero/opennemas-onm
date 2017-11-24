angular.module('BackendApp.controllers')
  /**
   * @ngdoc controller
   * @name  ImageListCtrl
   *
   * @requires $controller
   * @requires $scope
   * @requires http
   * @requires $timeout
   * @requires messenger
   * @requires oqlEncoder
   *
   * @description
   *   Controller for opinion list.
   */
  .controller('ImageListCtrl', [
    '$controller', '$location', '$scope', 'http', '$timeout', 'messenger', 'oqlEncoder',
    function($controller, $location, $scope, http, $timeout, messenger, oqlEncoder) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

      /**
       * Updates the array of contents.
       *
       * @param {String}  route The route name.
       * @param {Boolean} reset Whether to reset the list.
       */
      $scope.list = function(route, reset) {
        if (!reset && $scope.mode === 'grid') {
          $scope.loadingMore = 1;
        } else {
          $scope.loading = 1;
          $scope.contents = [];
          $scope.selected = { all: false, contents: [] };
        }

        oqlEncoder.configure({
          placeholder: {
            title: '(title ~ "%[value]%" or metadata ~ "%[value]%" or' +
              ' description ~ "%[value]%") ',
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
          $scope.map = response.data.map;

          if (response.data.hasOwnProperty('extra')) {
            $scope.extra = response.data.extra;
          }

          if (!reset && $scope.mode === 'grid') {
            $scope.contents = $scope.contents.concat(response.data.results);
          } else {
            $scope.contents = response.data.results;
          }

          // Disable spinner
          $scope.loading = 0;
          $scope.loadingMore = 0;
        }, function() {
          $scope.loading = 0;
          $scope.loadingMore = 0;

          messenger.post({
            message: 'Error while fetching data from backend',
            type:    'error'
          });
        });
      };

      /**
       * Saves the last selected item description.
       */
      $scope.saveDescription = function() {
        $scope.saving = true;

        var data = { description: $scope.selected.lastSelected.description };

        var route = {
          name: 'backend_ws_picker_save_description',
          params:  {
            id: $scope.selected.lastSelected.id
          }
        };

        http.post(route, data).then(function() {
          $scope.saving = false;
          $scope.saved = true;

          $timeout(function() {
            $scope.saved = false;
          }, 2000);

          return true;
        }, function() {
          $scope.saving = false;
          $scope.saved = false;
          $scope.error = true;

          $timeout(function() {
            $scope.error = false;
          }, 2000);

          return false;
        });
      };
    }]);
