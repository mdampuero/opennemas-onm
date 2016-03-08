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
        /**
         * @memberOf NewsAgencyModalCtrl
         *
         * @description
         *   The amount of photos to import.
         *
         * @type {Integer}
         */
        $scope.photos = 0;

        /**
         * @memberOf NewsAgencyModalCtrl
         *
         * @description
         *   The amount of texts to import.
         *
         * @type {Integer}
         */
        $scope.texts = 0;

        // Initialize the super class and extend it.
        $.extend(this, $controller('modalCtrl', {
          $scope: $scope,
          $modalInstance: $modalInstance,
          template: template,
          success: null
        }));

        /**
         * @function init
         * @memberOf NewsAgencyModalCtrl
         *
         * @description
         *   Initializes the type on init.
         */
        $scope.init = function() {
          $scope.check();

          $scope.template.type = 'article';

          if ($scope.photos === $scope.template.contents.length) {
            $scope.template.type = 'photo';
          }
        };

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
            author:   $scope.template.author,
            category: $scope.template.category,
            ids:      ids,
            type:     $scope.template.type
          };

          if (edit) {
            data.edit = 1;
          } else {
            $scope.loading = true;
          }

          $http.post(url, data).then(function(response) {
            $scope.loading = false;
            if (response.status === 201 && response.headers('location')) {
              $window.location.href = response.headers('location');
            } else {
              if (!edit) {
                $scope.imported = true;
                template.messages = response.data.messages;
              } else {
                $modalInstance.close(response.data);
              }
            }
          }, function() {
            $scope.loading = false;
            $modalInstance.close(false);
          });
        };

        /**
         * @function isEditable
         * @memberOf NewsAgencyModalCtrl
         *
         * @description
         *   Checks if you can edit the content after importing.
         *
         * @return {Boolean} True if content could be editable after importing.
         *                   Otherwise, returns false.
         */
        $scope.isEditable = function() {
          var texts = $scope.template.contents.filter(function(a) {
            return a.type === 'text';
          });

          return texts.length === 1;
        };

        /**
         * @function check
         * @memberOf NewsAgencyModalCtrl
         *
         * @description
         *   Checks the types of the contents to import.
         */
        $scope.check = function() {
          for (var i = 0; i < $scope.template.contents.length;  i++) {
            if ($scope.template.contents[i].type === 'photo') {
              $scope.photos++;
            }

            if ($scope.template.contents[i].type === 'text') {
              $scope.texts++;
            }
          }
        };
      }
    ]);
})();
