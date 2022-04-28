(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CommentCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('CommentCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf CommentCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
        $scope.item = {
          agent: null,
          author: '',
          author_email: '',
          author_ip: '',
          author_url: null,
          body: '',
          content_id: 0,
          content_type_refered: '',
          date: '',
          id: 0,
          parent_id: null,
          status: '',
          type: null,
          user_id: 0,

        };

        /**
         * @memberOf CommentCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getItem:    'api_v1_backend_comment_get_item',
          list:       'backend_comments_list',
          updateItem: 'api_v1_backend_comment_update_item',
        };

        /**
         * @inheritdoc
         */
        $scope.getItemId = function() {
          return $scope.item.id;
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.parseData($scope.data);
        };

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return false;
        };

        /**
         * @inheritdoc
         */
        $scope.parseData = function(data) {
          $scope.configure(data.extra);

          $scope.extra = $scope.data.extra;

          if (!data.item) {
            return data;
          }

          return $scope;
        };

        /**
         * @inheritdoc
         */
        $scope.getData = function() {
          return $scope.item;
        };
      }
    ]);
})();
