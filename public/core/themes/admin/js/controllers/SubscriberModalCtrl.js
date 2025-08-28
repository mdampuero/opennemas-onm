(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberModalCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires routing
     * @requires success
     * @requires template
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('SubscriberModalCtrl', [
      '$uibModalInstance', '$scope', 'template', 'success',
      function($uibModalInstance, $scope, template, success) {
        /**
         * MemberOf modalCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = template;

        /**
         * @function dismiss
         * @memberOf modalCtrl
         *
         * @description
         *   Close the modal without returning response.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
        };

        $scope.close = function(response) {
          $uibModalInstance.close(response);
        };

        /**
         * @function confirm
         * @memberOf modalCtrl
         *
         * @description
         *   Confirms and executes the confirmed action.
         */
        $scope.confirm = function() {
          if (!$scope.template.selectList || $scope.template.selectList.length === 0) {
            $scope.alert = { type: 'warning', message: 'Please select at least one list to import subscribers.' };
            return;
          }

          if (!$scope.template.file) {
            $scope.alert = { type: 'warning', message: 'Please select a CSV file.' };
            return;
          }

          var reader = new FileReader();

          reader.onload = function(e) {
            $scope.$apply(function() {
              var content = e.target.result;

              if (!$scope.validateCSVFile(content)) {
                $scope.loading = 0;
                return;
              }

              $scope.loading = 1;

              if (success && typeof success === 'function') {
                success($uibModalInstance, $scope.template);
              } else {
                $uibModalInstance.close(true);
              }
            });
          };

          reader.readAsText($scope.template.file);
        };

        /**
         * @function validateCSVFile
         * @memberOf modalCtrl
         *
         * @description
         *   Validates the uploaded CSV file.
         */
        $scope.validateCSVFile = function(content) {
          if (!content || content.trim().length === 0) {
            $scope.alert = { type: 'warning', message: 'The uploaded file is empty.' };
            return false;
          }

          try {
            JSON.parse(content);
            $scope.alert = { type: 'warning', message: 'The uploaded file appears to be JSON, not CSV.' };
            return false;
          } catch (e) {
            // Not JSON, which is good
          }

          var lines = content.split(/\r?\n/).filter(function(l) {
            return l.trim().length > 0;
          });

          if (lines.length < 2) {
            $scope.alert = { type: 'warning', message: 'The uploaded file does not appear to be a valid CSV.' };
            return false;
          }

          var headerCols = lines[0].split(',').length;

          for (var i = 1; i < lines.length; i++) {
            var cols = lines[i].split(',').length;

            if (cols !== headerCols) {
              $scope.alert = { type: 'warning', message: 'The uploaded file does not appear to be a valid CSV.' };
              return false;
            }
          }

          return true;
        };

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });
      }
    ]);
})();
