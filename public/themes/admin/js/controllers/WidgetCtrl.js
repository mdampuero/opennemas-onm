(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WidgetCtrl
     *
     * @description
     *   Handles actions in widget inner.
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     */
    .controller('WidgetCtrl', [
      '$compile', '$controller', '$http', '$rootScope', '$sce', '$scope', 'routing',
      function($compile, $controller, $http, $rootScope, $sce, $scope, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        $scope.form = false;

        /**
         * @function addParameter
         * @memberOf WidgetCtrl
         *
         * @description
         *   Adds an empty parameter to the parameters list.
         */
        $scope.addParameter = function() {
          $scope.params.push({ name: '', value: '' });
        };

        /**
         * @function getForm
         * @memberOf WidgetCtrl
         *
         * @description
         *   Gets the form for widgets of uuid.
         *
         * @param {String} uuid The widget uuid.
         */
        $scope.getForm = function(uuid) {
          $scope.formLoading = true;

          $('.widget-form').empty();

          var url = routing.generate('backend_ws_widget_get_form', { uuid: uuid });

          $http.get(url).then(function(response) {
            $scope.form        = $sce.trustAsHtml(response.data);
            $scope.params      = [];
            $scope.formLoading = false;

            var e = $compile(response.data)($scope);

            // Add original parameters to form
            for (var i = 0; i < $scope.params.length; i++) {
              var item = $scope.originalParams.filter(function(e) {
                return e.name === $scope.params[i].name;
              });

              if (item.length > 0) {
                $scope.params[i].value = item[0].value;
              }
            }

            $('.widget-form').append(e);
          }, function() {
            $scope.form        = false;
            $scope.params      = angular.copy($scope.originalParams);
            $scope.formLoading = false;
          });
        };

        /**
         * @function parseParams
         * @memberOf WidgetCtrl
         *
         * @description
         *   Parse the params from template and initialize the scope properly.
         *
         * @param Object params The widget params.
         */
        $scope.parseParams = function(params) {
          if (params === null) {
            params = [];
          }

          $scope.params = [];
          for (var i in params) {
            $scope.params.push({ name: i, value: params[i] });
          }

          $scope.originalParams = angular.copy($scope.params);
        };

        /**
         * @function removeParameter
         * @memberOf WidgetCtrl
         *
         * @description
         *   Removes a parameter from the list given a index.
         *
         * @param {Integer} The parameter index in the list of parameters.
         */
        $scope.removeParameter = function(index) {
          $scope.params.splice(index, 1);
        };

        // Gets the form for widget when widget type changes
        $scope.$watch('intelligent_type', function(nv) {
          $scope.getForm(nv);
        });

        // Updates internal parsedParameters parameter when parameters change
        $scope.$watch('params', function() {
          $scope.parsedParams = [];

          for (var i = $scope.params.length - 1; i >= 0; i--) {
            $scope.parsedParams.push({
              name:  $scope.params[i].name,
              value: $scope.params[i].value
            });
          }

          $scope.parsedParams = JSON.stringify($scope.parsedParams.reverse());
        }, true);
      }
    ]);
})();
