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
          $scope.company_fields = response.data.company_custom_fields ?
            $scope.parseDataToForm(response.data.company_custom_fields) : [];
          $scope.disableFlags('http');
        }, function() {
          $scope.disableFlags('http');
        });
      };

      /**
       * @function parseDataToForm
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Parse data after receive
       */
      $scope.parseDataToForm = function(data) {
        var formData = [];

        for (var [ dataKey, dataValue ] of Object.entries(data)) {
          formData.push({
            key: dataValue.key,
            values: dataValue.values
          });
        }
        return formData;
      };

      /**
       * @function parseFormToData
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Parse data before send
       */
      $scope.parseFormToData = function(data) {
        var parsedData =  Object.assign({}, data);

        return parsedData;
      };

      /**
       * @function addField
       * @memberOf CompanyConfCtrl
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
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Remove custom field
       */
      $scope.removeField = function(index) {
        $scope.company_fields.splice(index, 1);
      };

      /**
       * @function checkTag
       * @memberOf CompanyConfCtrl
       *
       * @description
       *   Slugify tags
       */
      $scope.checkTag = function(tag) {
        $scope.getSlug(tag.name, function(response) {
          tag.value           = response.data.slug;
        });
        return tag;
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
