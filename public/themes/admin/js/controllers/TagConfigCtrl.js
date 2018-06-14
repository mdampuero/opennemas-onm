(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('TagConfigCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger',
      function($controller, $scope, cleaner, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function init
         * @memberOf TagConfigCtrl
         *
         * @description
         *   initial load of the confit data.
         */
        $scope.saving = false;

        $scope.init = function() {
          $scope.list();
        };

        /**
         * @function list
         * @memberOf TagConfigCtrl
         *
         * @description
         *   get the tag config.
         */
        $scope.list = function() {
          $scope.loading = true;

          http.get('api_v1_backend_tags_config').then(function(response) {
            $scope.blacklist_tag = response.data.blacklist_tag;
            $scope.loading = false;
          }, function() {
            $scope.loading = false;
          });
        };

        /**
         * Updates an item.
         *
         * @param event    $event    triggering event .
         */
        $scope.saveConf = function($event) {
          $event.preventDefault();

          var data = { blacklist_tag: $scope.blacklist_tag };

          $scope.saving = false;
          http.put('api_v1_backend_tag_conf_save', data)
            .then(function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            });
        };
      }
    ]);
})();
