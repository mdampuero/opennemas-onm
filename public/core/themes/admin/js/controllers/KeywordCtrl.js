(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  KeywordCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('KeywordCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.incomplete = true;

        /**
         * @memberOf KeywordCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
        $scope.item = {
          keyword: '',
          value: null,
          type: null,
        };

        /**
         * @memberOf KeywordCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          updateItem: 'api_v1_backend_keyword_update_item',
          createItem: 'api_v1_backend_keyword_create_item',
          list:       'backend_keywords_list',
          getItem:    'api_v1_backend_keyword_get_item',
          saveItem:   'api_v1_backend_keyword_save_item',
          redirect:   'backend_keyword_show',
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
