/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('NewsletterCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /*  ====================================================================== */
    $scope.stepOne = function(containers) {
      $scope.newsletterContents = containers;

      $scope.sortableOptions = {
          placeholder: 'newsletter-content-placeholder',
          connectWith: '.newsletter-container-contents-sortable'
      };
    };

    $scope.addContainer = function() {
      $scope.newsletterContents.push({
        'pk_item': 0,
        'title': '',
        'link': '',
        'position': ''
      });
    };

    $scope.moveContainerUp = function(container) {
      var from = $scope.newsletterContents.indexOf(container);
      var to = from - 1;

      $scope.newsletterContents.splice(to,0,$scope.newsletterContents.splice(from,1)[0]);
    };

    $scope.moveContainerDown = function(container) {
      var from = $scope.newsletterContents.indexOf(container);
      var to = from + 1;

      $scope.newsletterContents.splice(to,0,$scope.newsletterContents.splice(from,1)[0]);
    };

    $scope.removeContainer = function(container) {
      var position = $scope.newsletterContents.indexOf(container);

      $scope.newsletterContents.splice(position, 1);
    };

    $scope.cleanContainers = function(container) {
      for (var i = $scope.newsletterContents.length - 1; i >= 0; i--) {
        $scope.newsletterContents[i].items = [];
      }
    };

    $scope.removeContent = function(container) {
    };
  }
]);
