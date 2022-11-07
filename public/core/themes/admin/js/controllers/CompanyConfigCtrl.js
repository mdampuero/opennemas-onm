(function() {
  'use strict';

  /**
   * @ngdoc controller
   * @name  CompanyConfigCtrl
   *
   * @requires $controller
   * @requires $scope
   * @requires http
   * @requires messenger
   *
   * @description
   *   Provides actions to list articles.
   */
  angular.module('BackendApp.controllers').controller('CompanyConfigCtrl', [
    '$controller', '$scope', 'http', 'messenger',
    function($controller, $scope, http, messenger) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function init
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Initializes the form.
       */
      $scope.init = function() {
        $scope.list();
      };

      /**
       * @function list
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Reloads the configuration.
       */
      $scope.list = function() {
        $scope.flags.http.loading = true;
        http.get('api_v1_backend_company_get_config').then(function(response) {
          $scope.company_fields = response.data.company_custom_fields ?
            $scope.parseDataToForm(response.data.company_custom_fields) : [];
          $scope.disableFlags('http');
        }, function() {
          $scope.disableFlags('http');
        });
      };

      /**
       * @function parseDataToForm
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Parse data after receive
       */
      $scope.parseDataToForm = function(data) {
        var formData = [];

        Object.values(data).forEach(function(entry) {
          formData.push({
            key: entry.key,
            values: entry.values
          });
        });
        return formData;
      };

      /**
       * @function parseFormToData
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Parse data before send
       */
      $scope.parseFormToData = function(data) {
        var parsedData = Object.assign({}, data);

        return parsedData;
      };

      /**
       * @function addField
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Add custom field
       */
      $scope.addField = function() {
        if (!$scope.company_fields) {
          $scope.company_fields = [
            {
              key: {
                name: ''
              },
              values: []
            }
          ];
          return;
        }
        $scope.company_fields.push({
          key: {
            name: ''
          },
          values: []
        });
      };

      /**
       * @function removeField
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Remove custom field
       */
      $scope.removeField = function(index) {
        $scope.company_fields.splice(index, 1);
      };

      /**
       * @function checkTag
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Slugify tags
       */
      $scope.checkTag = function(tag) {
        $scope.getSlug(tag.name, function(response) {
          tag.value = response.data.slug;
        });
        return tag;
      };

      /**
       * @function save
       * @memberOf CompanyConfigCtrl
       *
       * @description
       *   Saves the configuration.
       */
      $scope.save = function() {
        $scope.flags.http.saving = true;
        var data = $scope.parseFormToData($scope.company_fields);

        http.put('api_v1_backend_company_save_config', { company_custom_fields: data })
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
