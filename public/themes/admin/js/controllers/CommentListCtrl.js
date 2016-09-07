(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  CommentListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires $timeout
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires $http
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('CommentListCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing',
      function($controller, $scope, http, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

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
    }]);
})();
