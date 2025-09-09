(function() {
  'use strict';
  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name ModalTimeFilterCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires routing
     * @requires success
     * @requires template
     *
     * @description
     * Controller for Time Filter Modal.
     */
    .controller('ModalTimeFilterCtrl', [
      '$uibModalInstance', '$scope', '$q', 'routing', 'success', 'template',
      function($uibModalInstance, $scope, $q, routing, success, template) {
        /**
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * The routing service.
         *
         * @type {Object}
         */
        $scope.routing = routing;

        /**
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * The information provided by the controller which open the modal
         * window.
         *
         * @type {Object}
         */
        $scope.template = template;

        /**
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Initialize temporal criteria for the modal
         *
         * @type {Object}
         */
        $scope.tempCriteria = {
          starttime: null,
          endtime: null
        };

        /**
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Error message for date validation
         *
         * @type {String}
         */
        $scope.dateError = null;

        /**
         * @function validateDates
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Validates that start date is before end date and at least one date is selected
         *
         * @returns {Boolean} True if validation passes
         */
        $scope.validateDates = function() {
          $scope.dateError = null;

          // Check if both dates are selected and start >= end
          if ($scope.tempCriteria.starttime && $scope.tempCriteria.endtime) {
            var start = new Date($scope.tempCriteria.starttime);
            var end = new Date($scope.tempCriteria.endtime);

            if (start >= end) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function clearFilters
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Clears all date filters
         */
        $scope.clearFilters = function() {
          $scope.tempCriteria.starttime = null;
          $scope.tempCriteria.endtime = null;
          $scope.dateError = null;
        };

        /**
         * @function close
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Closes the modal window returning the provided response to the
         * controller which opened the modal window.
         *
         * @param {Object} response The response to return to the main
         * controller.
         */
        $scope.close = function(response) {
          $uibModalInstance.close(response);
        };

        /**
         * @function confirm
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Validates dates and executes the success callback, then closes
         * the window returning the response with the applied criteria.
         */
        $scope.confirm = function() {
          // Validate dates first
          if (!$scope.validateDates()) {
            return;
          }

          // Check if at least one date is selected
          if (!$scope.tempCriteria.starttime && !$scope.tempCriteria.endtime) {
            return;
          }

          $scope.loading = 1;

          // Create the response with the filter criteria
          var filterResponse = {
            confirmed: true,
            criteria: angular.copy($scope.tempCriteria)
          };

          // If success callback exists, execute it
          if (success && typeof success === 'function') {
            $q.when(success($uibModalInstance, $scope.template, $scope.tempCriteria))
              .then(function() {
                $scope.resolve(filterResponse, true);
              }, function(response) {
                $scope.resolve(response, false);
              });
          } else {
            // No success callback, just close with filter data
            $uibModalInstance.close(filterResponse);
          }
        };

        /**
         * @function resolve
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Resolves the modal with the response data
         *
         * @param {Object} response The response data
         * @param {Boolean} success Whether the operation was successful
         */
        $scope.resolve = function(response, success) {
          $scope.loading = 0;

          if (!response || Object.keys(response).length === 0) {
            $uibModalInstance.close(success);
            return;
          }

          $uibModalInstance.close({
            data: response.data || response,
            headers: response.headers,
            status: response.status,
            success: success,
            criteria: response.criteria || $scope.tempCriteria
          });
        };

        /**
         * @function dismiss
         * @memberOf ModalTimeFilterCtrl
         *
         * @description
         * Closes the modal window without returning any response.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss('cancel');
        };

        // Watch for date changes to validate in real time
        $scope.$watch('tempCriteria.starttime', function(newVal, oldVal) {
          if (newVal !== oldVal) {
            $scope.validateDates();
          }
        });

        $scope.$watch('tempCriteria.endtime', function(newVal, oldVal) {
          if (newVal !== oldVal) {
            $scope.validateDates();
          }
        });

        // Changes step on client saved
        $scope.$on('client-saved', function(event, args) {
          $scope.client = args;

          if ($scope.template) {
            $scope.template.step = 2;
          }
        });

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
          $scope.tempCriteria = null;
        });
      }
    ]);
})();
