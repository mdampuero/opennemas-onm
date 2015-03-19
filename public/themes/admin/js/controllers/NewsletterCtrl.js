/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('NewsletterCtrl', [
  '$controller', '$http', '$modal', '$rootScope', '$sce', '$scope',
  function($controller, $http, $modal, $rootScope, $sce, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.source = {
      all: false,
      items: [],
      selected: []
    };

    $scope.target = {
      all: false,
      items: [],
      selected: []
    };

    /**
     * Add selected email to receivers list and remove them from available
     * receivers list.
     */
    $scope.addRecipients = function() {
      $scope.target.items =
        $scope.target.items.concat($scope.source.selected);

      for (var i = 0; i < $scope.source.selected.length; i++) {
        var index = $scope.source.items.indexOf($scope.source.selected[i]);
        console.log(index);
        console.log($scope.source.selected[i]);
        console.log($scope.source.items);
        $scope.source.items.splice(index, 1);
      }

      $scope.source.all = false;
      $scope.source.selected = [];
    };

    /**
     * Remove selected emails from receivers list and add them to available
     * receivers list.
     */
    $scope.removeRecipients = function() {
      $scope.source.items =
        $scope.source.items.concat($scope.target.selected);

      for (var i = 0; i < $scope.target.selected.length; i++) {
        var index = $scope.target.items.indexOf($scope.target.selected[i]);
        $scope.target.items.splice(index, 1);
      }

      $scope.target.all = false;
      $scope.target.selected = [];
    };

    /**
     * Selects/unselects all items of a list.
     *
     * @param {Array} source The list.
     */
    $scope.toggleAllRecipients = function(source) {
      console.log('toggle');
      source.selected = [];

      if (source.all) {
        source.selected = angular.copy(source.items);
      }
    };

    /**
     * Parses and add more emails to newsletter receivers.
     */
    $scope.addMoreEmails = function() {
      $scope.moreEmailsError = false;

      var emails = $scope.moreEmails.split('\n');
      var pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      // Get only emails to easy checking
      var currentEmails = [];
      for (var i = 0; i < $scope.target.items.length; i++) {
        currentEmails.push($scope.target.items[i].email);
      }

      // Save new valid emails
      for (var i = 0; i < emails.length; i++) {
        if (pattern.test(emails[i]) &&
            currentEmails.indexOf(emails[i]) === -1) {
          $scope.target.items.push({ email: emails[i] });
          $scope.moreEmails = $scope.moreEmails.replace(emails[i] + '\n', '');
        }

        if (!pattern.test(emails[i])) {
          $scope.moreEmailsError = true;
        }
      }
    };

    $scope.saveHtml = function(url) {
      var data = {
        title: $scope.subject,
        html: $scope.html
      };

      $http.post(url, data).success(function(response) {
        $scope.renderMessages(response.messages);
      });
    };

    /**
     * Opens a modal to confirm newsletter sending.
     */
    $scope.send = function() {
      var modal = $modal.open({
        templateUrl: 'modal-confirm-send',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return null;
          },
          success: function() {
            return function() {
              $('form').submit();
            };
          }
        }
      });
    };

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
      if (!$scope.newsletterContents) {
        return;
      }

      for (var i = 0; i < $scope.newsletterContents.length; i++) {
        if ($scope.newsletterContents[i].items) {
          for (var j = 0; j < $scope.newsletterContents[i].items.length; j++) {
            var newElement = {
              id:                     $scope.newsletterContents[i].items.id,
              content_type_name:      $scope.newsletterContents[i].items[j].content_type_name,
              content_type_l10n_name: $scope.newsletterContents[i].items[j].content_type_l10n_name,
              title:                  $scope.newsletterContents[i].items[j].title,
              position:               j
            };

            $scope.newsletterContents[i].items[j] = newElement;
          }
        }
      }

      $scope.contents = angular.toJson($scope.newsletterContents);
    }, true);

    /**
     * Updates the trusted HTML to show in preview when HTML changes.
     */
    $scope.$watch('html', function() {
      $scope.trustedHtml = $sce.trustAsHtml($scope.html);
    });

    $scope.html = $scope.hiddenHtml
  }
]);
