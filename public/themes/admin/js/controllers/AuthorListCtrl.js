(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name AuthorListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $uibModal
     * @requires http
     * @requires oqlEncoder
     * @requires webStorage
     *
     * @description
     *   Handles all actions in users listing.
     */
    .controller('AuthorListCtrl', [
      '$controller', '$location', '$scope', '$uibModal', 'http', 'oqlEncoder',
      function($controller, $location, $scope, $uibModal, http, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('UserListCtrl', {
          $scope:   $scope,
        }));

        /**
         * @function list
         * @memberOf AuthorListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: { name: 'name ~ "[value]" or username ~ "[value]"' }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'api_v1_backend_authors_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.extra   = response.data.extra;
            $scope.items   = response.data.items;
            $scope.total   = response.data.total;

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };
      }
    ]);
})();
