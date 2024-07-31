(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ModalWidgetEditCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires $timeout
     * @requires fullUrl
     * @requires $compile
     * @requires http
     * @requires widgetID
     *
     * @description
     *   Controller for the widget edit modal.
     */
    .controller('ModalWidgetEditCtrl', [
      '$uibModalInstance', '$controller', '$scope', '$timeout', '$compile', 'http', 'widgetID',
      function($uibModalInstance, $controller, $scope, $timeout, $compile, http, widgetID) {
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));
        // Initialize the item object
        $scope.item = {
          body: '',
          content_type_name: 'widget',
          fk_content_type: 12,
          content_status: 0,
          content: null,
          description: '',
          favorite: 0,
          frontpage: 0,
          created: null,
          starttime: null,
          endtime: null,
          widget_type: null,
          class: null,
          title: '',
          params: [],
          categories: [],
          external_link: '',
        };

        // Initialize data object
        $scope.data = {
          related: [],
          index: null,
        };

        // Initialize routes
        $scope.routes = {
          createItem: 'api_v1_backend_widget_create_item',
          getItem:    'api_v1_backend_widget_get_item',
          getForm:    'api_v1_backend_widget_get_form',
          list:       'backend_widgets_list',
          redirect:   'backend_widget_show',
          saveItem:   'api_v1_backend_widget_save_item',
          updateItem: 'api_v1_backend_widget_update_item'
        };

        // Add default parameters
        $scope.addDefaultParameters = function(params) {
          if (!$scope.item.params || $scope.item.params.length === 0) {
            $scope.item.params = params;
            return;
          }

          var parameters = Object.assign($scope.buildObject(params), $scope.buildObject($scope.item.params));

          var result     = [];

          var properties = params.map(function(param) {
            return param.name;
          });

          properties.forEach(function(property) {
            var object = {};

            object.name  = property;
            object.value = parameters[property];

            result.push(object);
          });

          $scope.item.params = result;
        };

        // Initialize the controller
        $scope.init = function(index) {
          $scope.data.index = index;

          // Initialize the array of related contents
          if ($scope.item.params[$scope.data.index].value !== '') {
            var oql = 'pk_content in [' +
              $scope.item.params[$scope.data.index].value +
              ']';

            var route = {
              name: 'api_v1_backend_content_get_list',
              params: { oql: oql }
            };

            http.get(route)
              .then(function(response) {
                $scope.data.related = response.data.items;
              })
              .catch(function(err) {
                $scope.data.related = [];
                return err;
              });
          }

          // Watch for changes in the related contents
          $scope.$watch(function() {
            if (!$scope.data.related) {
              return '';
            }

            return $scope.data.related.reduce(function(previous, current) {
              if (!previous) {
                return previous + current.pk_content;
              }

              return previous + ',' + current.pk_content;
            }, '');
          }, function(nv, ov) {
            if (ov === nv) {
              return;
            }

            $scope.item.params[$scope.data.index].value = nv;
          });
        };

        // Remove item from related contents
        $scope.removeItem = function(index) {
          $scope.data.related.splice(index, 1);
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          var params = [];

          for (var key in $scope.item.params) {
            // eslint-disable-next-line no-new-wrappers
            var value = new Number($scope.item.params[key]);

            if ($scope.item.params[key] === '') {
              value = '';
            }

            value = value.toString() === 'NaN' ? $scope.item.params[key] : value.valueOf();

            params.push({ name: key, value: value });
          }

          $scope.item.params = params;

          if ($scope.displayMultiBody()) {
            $scope.language = $scope.data.extra.locale.selected || null;
            if (typeof $scope.item.body !== 'object') {
              var bodyValue = $scope.item.body;

              $scope.item.body = {};
              $scope.item.body[$scope.language] = bodyValue;
            }
          }
        };

        // Build an object from an array of parameters
        $scope.buildObject = function(params) {
          var object = {};

          // Asegúrate de que params sea un objeto y no un array
          if (typeof params === 'object' && !Array.isArray(params)) {
            Object.keys(params).forEach(function(key) {
              object[key] = params[key];
            });

            return object;
          }

          return {};
        };

        // Add an empty parameter to the list
        $scope.addParameter = function() {
          $scope.item.params.push({ name: '', value: '' });
        };

        // Get the form for widgets of uuid
        $scope.getForm = function(uuid) {
          if (!$scope.item.widget_type) {
            return;
          }

          $scope.flags = $scope.flags || {};
          $scope.flags.http = $scope.flags.http || {};
          $scope.flags.http.formLoading = true;

          $('.widget-form').empty();

          var route = {
            name: $scope.routes.getForm,
            params: { uuid: uuid }
          };

          http.get(route).then(function(response) {
            $scope.widgetForm = true;

            $('.widget-form').append($compile(response.data)($scope));

            $scope.disableFlags('http');
          }, function() {
            $scope.widgetForm = false;
            $scope.disableFlags('http');
          });
        };

        // Close the modal and refresh the page
        $scope.close = function() {
          location.reload();
          $uibModalInstance.dismiss('cancel');
        };

        // Watch for changes in the item class and get the form accordingly
        $scope.$watch('item.class', function(nv, ov) {
          if (ov) {
            $scope.item.params  = [];
            $scope.data.related = [];
          }

          if (!nv) {
            return;
          }

          $scope.getForm(nv);
        }, true);

        // Initialize the controller with widgetID
        if (widgetID) {
          $scope.getItem(widgetID);
        }
      }
    ]);
})();
