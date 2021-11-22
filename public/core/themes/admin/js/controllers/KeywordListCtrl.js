(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  KeywordListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires messenger
     * @requires oqlEncoder
     * @requires queryManager
     *
     * @description
     *   Controller for opinion list.
     */
    .controller('KeywordListCtrl', [
      '$controller', '$location', '$scope', 'http', 'messenger', 'oqlEncoder',
      function($controller, $location, $scope, http, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.routes = {
          getList:     'api_v1_backend_keyword_get_list',
          deleteItem:  'api_v1_backend_keyword_delete',
          deleteList:  'api_v1_backend_keyword_batch_delete',
        };

        /**
         * @inheritdoc
         */
        $scope.criteria = {
          epp: 10,
          orderBy: { keyword:  'asc' },
          page: 1
        };

        /**
         * @inheritdoc
         */
        $scope.getItemId = function(item) {
          return item.id;
        };

        /**
         * @function init
         * @memberOf CommentListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          oqlEncoder.configure({
            placeholder: {
              keyword: 'keyword ~ "%[value]%"',
            }
          });
          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);

          if (!data.items) {
            $scope.data.items = [];
          }

          $scope.items = $scope.data.items;

          $scope.extra = $scope.data.extra;
        };
      }
    ]);
})();
