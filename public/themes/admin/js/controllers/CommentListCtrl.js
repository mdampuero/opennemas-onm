(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CommentListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     *
     * @description
     *   Controller for comments listing.
     */
    .controller('CommentListCtrl', [
      '$controller', '$location', '$scope', 'http', 'messenger', 'oqlEncoder',
      function($controller, $location, $scope, http, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          epp: 10,
          orderBy: { date:  'desc' },
          page: 1
        };

        /**
         * @function getExportUrl
         * @memberOf CommentListCtrl
         *
         * @description
         *   Generates the URL to export comments to a CSV file.
         *
         * @return {String} The URL to export comments to a CSV file.
         */
        $scope.getExportUrl = function() {
          var criteria = angular.copy($scope.criteria);

          if (!criteria) {
            return '';
          }

          oqlEncoder.configure({
            placeholder: {
              body: 'body ~ "%[value]%"',
            }
          });

          return $scope.routing.generate('backend_ws_comments_list', {
            format: '.csv',
            oql: oqlEncoder.getOql(criteria)
          });
        };

        /**
         * Updates the array of contents.
         *
         * @param string route Route name.
         */
        $scope.list = function(route) {
          $scope.contents = [];
          $scope.loading  = 1;
          $scope.selected = { all: false, contents: [] };

          oqlEncoder.configure({
            placeholder: {
              body: 'body ~ "%[value]%"',
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.route,
            params:  {
              contentType: 'comment',
              oql: oql
            }
          };

          $location.search('oql', oql);

          http.get(route).then(function(response) {
            $scope.total = parseInt(response.data.total);
            $scope.contents = response.data.results;

            if (response.data.hasOwnProperty('extra')) {
              $scope.extra = response.data.extra;
            }

            // Disable spinner
            $scope.loading = 0;
            $scope.loadingMore = false;
          }, function() {
            $scope.loading = 0;
            var params = {
              id: new Date().getTime(),
              message: 'Error while fetching data from backend',
              type: 'error'
            };

            messenger.post(params);
          });
        };

        /**
         * @function patch
         * @memberOf CommentListCtrl
         *
         * @description
         *   Accepts/rejects a comment.
         *
         * @param {String}  item     The comment object.
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name: 'backend_ws_comment_patch',
            params: { id: item.id }
          };

          http.patch(route, data).then(function(response) {
            item[property + 'Loading'] = 0;
            item[property] = value;
            messenger.post(response.data);
          }, function(response) {
            item[property + 'Loading'] = 0;
            messenger.post(response.data);
          });
        };

        /**
         * @function patchSelected
         * @memberOf CommentListCtrl
         *
         * @description
         *   Accepts/rejects the selected comments.
         *
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          for (var i = 0; i < $scope.contents.length; i++) {
            var id = $scope.contents[i].id;

            if ($scope.selected.contents.indexOf(id) !== -1) {
              $scope.contents[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.contents };

          data[property] = value;

          http.patch('backend_ws_comments_patch', data).then(function() {
            $scope.list('backend_ws_comments_list', true);
          });
        };
      }
    ]);
})();
