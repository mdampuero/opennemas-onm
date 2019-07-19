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
     * @requires $scope
     */
    .controller('WidgetCtrl', [
      '$compile', '$controller', '$http', '$uibModal', '$sce', '$scope', 'routing',
      function($compile, $controller, $http, $uibModal, $sce, $scope, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

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
          created: new Date(),
          starttime: null,
          endtime: null,
          renderlet: 'html',
          title: '',
          params: [],
          categories: [],
          tags: [],
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
          redirect:   'backend_widget_show',
          saveItem:   'api_v1_backend_widget_save_item',
          updateItem: 'api_v1_backend_widget_update_item'
        };

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
          if ($scope.item.renderlet === 'html') {
            return;
          }

          $scope.flags.http.formLoading = true;

          $('.widget-form').empty();

          var url = routing.generate($scope.routes.getForm, { uuid: uuid });

          $http.get(url).then(function(response) {
            $scope.form = $sce.trustAsHtml(response.data);

            var e = $compile(response.data)($scope);

            $('.widget-form').append(e);

            $scope.disableFlags('http');
          }, function() {
            $scope.form = false;
            $scope.disableFlags('http');
          });
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

        // Gets the form for widget when widget type changes
        $scope.$watch('item.content', function(nv) {
          if (!nv) {
            return;
          }

          $scope.getForm(nv);
        }, true);
      }
    ]);
})();
