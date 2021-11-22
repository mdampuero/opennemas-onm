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
     * @requires $compile
     * @requires $controller
     * @requires $scope
     * @requires http
     */
    .controller('WidgetCtrl', [
      '$compile', '$controller', '$scope', 'cleaner', 'http',
      function($compile, $controller, $scope, cleaner, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf WidgetCtrl
         *
         * @description
         *  The HTML string with the form for the widget
         *
         * @type {String}
         */
        $scope.widgetForm = false;

        /**
         * @inheritdoc
         */
        $scope.incomplete = true;

        /**
         * @memberOf AlbumCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
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
          type: null,
          class: null,
          title: '',
          params: [],
          categories: [],
          external_link: '',
        };

        /**
         * @memberOf AlbumCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_widget_create_item',
          getItem:    'api_v1_backend_widget_get_item',
          getForm:    'api_v1_backend_widget_get_form',
          list:       'backend_widgets_list',
          redirect:   'backend_widget_show',
          saveItem:   'api_v1_backend_widget_save_item',
          updateItem: 'api_v1_backend_widget_update_item'
        };

        /**
         * @function addDefaultParameters
         * @memberOf WidgetCtrl
         *
         * @description
         *   Adds default parameters to item and re-use old parameters if names
         *   match for the same index in the array of parameters.
         *
         * @param {Array} params The list of default parameters.
         */
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

        /**
         * @function buildObject
         * @memberOf WidgetCtrl
         *
         * @description
         *  Generates an object basing on the array of parameters.
         *
         * @param {Array} params The array of parameters.
         *
         * @return {Object} The object with the parameters as properties.
         */
        $scope.buildObject = function(params) {
          var object = {};

          params.forEach(function(param) {
            object[param.name] = param.value;
          });

          return object;
        };

        /**
         * @function addParameter
         * @memberOf WidgetCtrl
         *
         * @description
         *   Adds an empty parameter to the parameters list.
         */
        $scope.addParameter = function() {
          $scope.item.params.push({ name: '', value: '' });
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
        };

        /**
         * @inheritdoc
         */
        $scope.getData = function() {
          var data = angular.extend({}, $scope.item);

          return cleaner.clean(data);
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
          if ($scope.item.renderlet === 'html') {
            return;
          }

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

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return false;
        };

        /**
         * @inheritdoc
         */
        $scope.parseData = function(data) {
          var params = {};

          // Convert array of parameters to object
          for (var i = 0; i < data.params.length; i++) {
            params[data.params[i].name] = data.params[i].value;
          }

          data.params = params;

          return data;
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
          $scope.item.params.splice(index, 1);
        };

        /**
         * @function resetContent
         * @memberOf WidgetCtrl
         *
         * @description
         *   Cleans widget content and widget form.
         */
        $scope.resetContent = function() {
          $('.widget-form').empty();
          $scope.item.class = null;
          $scope.widgetForm   = null;
        };

        // Gets the form for widget when widget type changes
        $scope.$watch('item.class', function(nv) {
          if (!nv) {
            return;
          }

          if ($scope.item.type) {
            $scope.item.body = $scope.item.class;
          }

          $scope.getForm(nv);
        }, true);
      }
    ]);
})();
