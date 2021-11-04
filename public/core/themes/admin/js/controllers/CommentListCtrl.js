(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CommentListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires localizer
     *
     * @description
     *   Controller for comments listing.
     */
    .controller('CommentListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.criteria = {
          epp: 10,
          orderBy: { date:  'desc' },
          page: 1
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          getList:    'api_v1_backend_comment_get_list',
          patchList:  'api_v1_backend_comment_patch_list',
          patchItem:  'api_v1_backend_comment_patch_item',
          deleteList: 'api_v1_backend_comment_delete_list',
          deleteItem: 'api_v1_backend_comment_delete_item',
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
          $scope.backup.criteria      = $scope.criteria;
          $scope.app.columns.hidden   = [];
          $scope.app.columns.selected =  _.uniq($scope.app.columns.selected
            .concat([ 'comment', 'status' ]));

          oqlEncoder.configure({
            placeholder: {
              body: 'body ~ "%[value]%"',
            }
          });

          $scope.list();

          $scope.options = {
            tooltips: { enabled: false },
            elements: { arc: { borderWidth: 0 } },
          };
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);

          if (!data.items) {
            $scope.data.items = [];
          }

          $scope.localize($scope.data.extra.contents, 'contents');

          $scope.items = $scope.data.items;

          $scope.extra = $scope.data.extra;
        };

        /**
         * @function localizeText
         * @memberOf CommentListCtrl
         *
         * @param {any} String or Object to localize.
         *
         * @return {String} Localized text.
         *
         * @description
         *   Localize and return text
         */
        $scope.localizeText = function(text) {
          if (typeof text === 'object') {
            return text[$scope.config.locale.selected];
          }

          return text;
        };
      }
    ]);
})();
