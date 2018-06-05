(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CommentsConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     *   Provides actions to edit, save and update articles.
     */
    .controller('CommentsConfigCtrl', [
      '$controller', '$scope', '$uibModal',
      function($controller, $scope, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        $scope.init = function(config, extra) {
          $scope.configs = config;
          $scope.extra = extra;
        };

        $scope.changeHandler = function(name, icon, url) {
          $uibModal.open({
            backdrop:    true,
            backdropClass: 'modal-backdrop-dark',
            controller:  'YesNoModalCtrl',
            openedClass: 'modal-relative-open',
            templateUrl: 'modal-comment-change',
            resolve: {
              template: function() {
                return {
                  handler: name,
                  iconName: icon,
                };
              },
              yes: function() {
                return function(modalWindow) {
                  window.location = url;
                };
              },
              no: function() {
                return function(modalWindow) {
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
        };
      }
    ]);
})();
