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
      if (containers !== null) {
        $scope.newsletterContents = containers;
      } else {
        $scope.newsletterContents = [];
      }

      $scope.sortableOptions = {
          placeholder: 'newsletter-content-placeholder',
          connectWith: '.newsletter-container-contents-sortable'
      };
    };

    $scope.addContainer = function() {
      $scope.newsletterContents.push({
        'id': 0,
        'title': '',
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

    $scope.cleanContainers = function() {
      for (var i = $scope.newsletterContents.length - 1; i >= 0; i--) {
        $scope.newsletterContents[i].items = [];
      }
    };

    $scope.removeContent = function(container, content) {
      var position = container.items.indexOf(content);

      container.items.splice(position, 1);
    };

    /**
     * Removes unnecessary fields from items and updates the JSON string to send
     * to server.
     */
    $scope.$watch('newsletterContents', function() {
      for (var i = 0; i < $scope.newsletterContents.length; i++) {
        if ($scope.newsletterContents[i].items) {
          for (var j = 0; j < $scope.newsletterContents[i].items.length; j++) {
            var newElement = {
              id:                     $scope.newsletterContents[i].items.id,
              content_type:           $scope.newsletterContents[i].items[j].content_type,
              content_type_l10n_name: $scope.newsletterContents[i].items[j].content_type_l10n_name,
              title:                  $scope.newsletterContents[i].items[j].title,
              position:               j
            };

            $scope.newsletterContents[i].items[j] = newElement;
          }
        }
      }
      console.log($scope.newsletterContents);

      $scope.contents = angular.toJson($scope.newsletterContents);
    }, true);
  }
]);
