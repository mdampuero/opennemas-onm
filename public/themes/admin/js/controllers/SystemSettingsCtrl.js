(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  SystemSettingsCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Handles actions for paywall settings configuration form.
     */
    .controller('SystemSettingsCtrl', ['$controller', '$http', '$rootScope', '$scope',
      function($controller, $http, $rootScope, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function init
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Initialize list of other ga account codes.
         *
         * @param Object others The list of other ga account codes.
         */
        $scope.init = function(others) {
          if (angular.isArray(others)) {
            $scope.others = others;
          } else {
            $scope.others = [];
          }
        };

        /**
         * @function addInput
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Add new input for ga tracking code.
         *
         * @param integer index The index of the domain to remove.
         */
        $scope.addGanalytics = function() {
          $scope.others.push({
            apiKey:'',
            baseDomain:''
          });
        };


        /**
         * @function removeInput
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Removes a ga tracking code input.
         *
         * @param integer index The index of the input to remove.
         */
        $scope.removeGanalytics = function(others, index) {
          $scope.others.splice(index, 1);
        };
      }
    ]);
})();
