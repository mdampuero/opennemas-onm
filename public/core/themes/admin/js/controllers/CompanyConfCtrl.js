(function() {
  'use strict';

  /**
   * @ngdoc controller
   * @name  CompanyConfCtrl
   *
   * @requires $controller
   * @requires $scope
   * @requires http
   * @requires messenger
   *
   * @description
   *   Provides actions to list articles.
   */
  angular.module('BackendApp.controllers').controller('CompanyConfCtrl', [
    '$controller', '$scope', 'http', 'messenger',
    function($controller, $scope, http, messenger) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function init
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Initializes the form.
       */
      $scope.init = function() {
        $scope.list();
      };

      /**
       * @function list
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Reloads the configuration.
       */
      $scope.list = function() {
        $scope.flags.http.loading = true;
        http.get('api_v1_backend_company_get_config').then(function(response) {
          $scope.compay_fields = response.data.company_custom_fields ? response.data.company_custom_fields : {};
          $scope.disableFlags('http');
        }, function() {
          $scope.disableFlags('http');
        });
      };

      /**
       * @function save
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Saves the configuration.
       */
      $scope.save = function() {
        $scope.flags.http.saving = true;

        http.put('api_v1_backend_company_save_config', { company_custom_fields: $scope.compay_fields })
          .then(function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
      };
    }
  ]);
})();
