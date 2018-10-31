(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserGroupCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Handles all actions in user groups listing.
     */
    .controller('UrlCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf UserGroupCtrl
         *
         * @description
         *   The user group object..
         *
         * @type {Object}
         */
        $scope.item = {
          redirection: 1,
          type: 0
        };

        /**
         * @memberOf SubscriptionCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_url_create',
          redirect: 'backend_url_show',
          save:     'api_v1_backend_url_save',
          show:     'api_v1_backend_url_show',
          update:   'api_v1_backend_url_update'
        };

        // Updates item target when selected content from content picker changes
        $scope.$watch('data.extra.content', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }

          $scope.item.target       = nv.pk_content;
          $scope.item.content_type = nv.content_type_name;
        }, true);

        // Updates item when item type changes
        $scope.$watch('item.type', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }

          // Remove selected content when slug or regex to slug
          if ([ 0, 1, 3 ].indexOf(nv) === -1) {
            if ($scope.data.extra.content) {
              delete $scope.data.extra.content;
            }

            $scope.item.content_type = null;
          }
        }, true);
      }
    ]);
})();
