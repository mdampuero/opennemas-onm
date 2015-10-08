(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NewsAgencyModalCtrl
     *
     * @requires $http
     * @requires $modalInstance
     * @requires $scope
     * @requires routing
     * @requires template
     *
     * @description
     *   description
     */
    .controller('NewsAgencyModalCtrl', ['$controller', '$http', '$modalInstance', '$scope', '$window', 'routing', 'template',
      function ($controller, $http, $modalInstance, $scope, $window, routing, template) {

        // Initialize the super class and extend it.
        $.extend(this, $controller('modalCtrl', {
          $scope: $scope,
          $modalInstance: $modalInstance,
          template: template,
          success: null
        }));

        /**
         * @function confirm
         * @memberOf NewsAgencyModalCtrl
         *
         * @description
         *   Confirms and executes the confirmed action.
         *
         * @param {Boolean} edit Whether to edit after importing.
         */
        $scope.confirm = function(edit) {
          $scope.saving = true;

          var ids = [];
          for (var i = 0; i < $scope.template.contents.length; i++) {
            ids.push({
              id:     $scope.template.contents[i].id,
              source: $scope.template.contents[i].source,
            });
          }

          var url = routing.generate('backend_ws_news_agency_import');
          var data = {
            author:   $scope.author,
            category: $scope.category,
            ids:      ids,
            type:     $scope.type
          };

          if (edit) {
            data.edit = 1;
          }

          $http.post(url, data).then(function(response) {
            if (response.status === 201 && response.headers('location')) {
              $window.location.href = response.headers('location');
            } else {
              $modalInstance.close(response.data);
            }
          }, function() {
            $modalInstance.close(false);
          });
        };
      }
    ]);
})();
