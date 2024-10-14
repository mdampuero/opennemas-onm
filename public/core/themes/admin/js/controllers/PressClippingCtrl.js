(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PressClippingCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('PressClippingCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing', 'oqlEncoder', '$location',
      function($controller, $scope, http, messenger, routing, oqlEncoder, $location) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          epp: 10,
          orderBy: { fk_content:  'desc' },
          page: 1,
        };

        /**
         * @memberof PressClippingCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getList: 'api_v1_backend_pressclipping_get_list'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.expandFields();
        };

        /**
         * @function init
         * @memberof PressClippingCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          $scope.app.columns.hidden = [];
          $scope.app.columns.selected = _.uniq($scope.app.columns.selected
            .concat([ 'pressclipping_status', 'pressclipping_sended' ]));

          oqlEncoder.configure({
            placeholder: {
              title: 'and title like "%[value]%"'
            }
          });

          $scope.list();
        };

        /**
         * @function list
         * @memberOf RestListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.http.loading = 1;

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data = response.data;

            if (!response.data.items) {
              $scope.data.items = [];
            }

            $scope.items = $scope.data.items;
            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.items = [];
          });
        };
      }
    ]);
})();
