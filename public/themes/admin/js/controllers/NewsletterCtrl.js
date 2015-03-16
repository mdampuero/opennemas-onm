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
        'position': '',
        'items': []
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

    /**
     * Removes unnecessary fields from items and updates the JSON string to send
     * to server.
     */
    $scope.$watch('newsletterContents', function() {
      for (var i = 0; i < $scope.newsletterContents.length; i++) {
        var contents = [];

        for (var j = 0; j < $scope.newsletterContents[i].items.length; j++) {
          $scope.newsletterContents[i].items[j] = {
            id:                $scope.newsletterContents[i].items.id,
            content_type_name: $scope.newsletterContents[i].items[j].content_type_name,
            title:             $scope.newsletterContents[i].items[i].title
          };
        }
      }

      $scope.contents = angular.toJson($scope.newsletterContents);
    }, true);
  }
]);
