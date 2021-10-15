(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  LetterListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires localizer
     *
     * @description
     *   Controller for comments listing.
     */
    .controller('LetterListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'letter',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: 1,
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          getList:    'api_v1_backend_letter_get_list',
          patchList:  'api_v1_backend_letter_patch_list',
          patchItem:  'api_v1_backend_letter_patch_item',
          deleteList: 'api_v1_backend_letter_delete_list',
          deleteItem: 'api_v1_backend_letter_delete_item',
        };

        /**
         * @function init
         * @memberOf LetterListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"'
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

          $scope.items = $scope.data.items;

          $scope.extra = $scope.data.extra;
        };
      }
    ]);
})();
