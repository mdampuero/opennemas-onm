(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NewsAgencyListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $modal
     * @requires $scope
     * @requires $timeout
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires $http
     *
     * @description
     *   description
     */
    .controller('NewsAgencyListCtrl', [
      '$controller', '$http', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger',
      function($controller, $http, $modal, $scope, $timeout, itemService, routing, messenger) {

        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', {$scope: $scope}));

        /**
         * @function _import
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Sends a request to import a list of contents.
         *
         * @param {Object} contents The contents to import.
         */
        $scope._import = function(contents) {
          var modal = $modal.open({
            templateUrl: 'modal-import-selected',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  contents: contents
                };
              },
              success: function() {
                return function() {
                  var url = routing.generate('');

                  return $http.post(url, {
                    contents: contents,
                    type: $scope.type,
                    category: $scope.category
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              $scope.renderMessages(response.data.messages);

              if (response.status === 200) {
                $scope.list($scope.route);
              }
            }
          });
        };

        /**
         * @function import
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Opens a modal window to import one item.
         *
         * @param {Object} content The content to import
         */
        $scope.import = function(content) {
          var contents = [ content ];

          $scope._import(contents);
        };

        /**
         * @function importSelected
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Opens a modal window to import the selected contents.
         */
        $scope.importSelected = function() {
          var contents = [];

          for (var i = 0; i < $scope.contents.length; i++) {
            var id = $scope.contents[i].xml_id;
            if ($scope.selected.contents.indexOf(id)) {
              contents.push($scope.contents[i]);
            }
          }

          $scope._import(contents);
        };

        $scope.$watch('extra', function(nv, ov) {
          if (!ov && nv && nv.last_sync) {
            messenger.post(nv.last_sync);
          }
        });
    }]);
})();
