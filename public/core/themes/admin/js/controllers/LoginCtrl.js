angular.module('BackendApp.controllers')
  /**
   * @ngdoc controller
   * @name  LoginCtrl
   *
   * @requires $controller
   * @requires $scope
   *
   * @description
   *   Handles actions in login form.
   */
  .controller('LoginCtrl', [
    '$controller', '$scope',
    function($controller, $scope) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function changeLanguage
       * @memberOf LoginCtrl
       *
       * @description
       *   Reloads the page when language changes.
       */
      $scope.changeLanguage = function() {
        document.location.href = '?language=' + $scope.language;
      };
    }
  ]);
