/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('NewsletterCtrl', [
  '$controller', '$http', '$uibModal', '$rootScope', '$sce', '$scope',
  function($controller, $http, $uibModal, $rootScope, $sce, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.source = {
      items: [],
      selected: []
    };

    $scope.recipients = {
      all: false,
      items: [],
      selected: []
    };

    $scope.expanded = 'external';

    /**
     * Initialize list of mail accounts
     */
    $scope.initPickRecipients = function(newsletter, extra) {
      $scope.newsletter = newsletter;
      $scope.source.items = extra.recipients;
      $scope.newsletter_handler = extra.newsletter_handler;

      if ($scope.newsletter_handler === 'submit' ||
        $scope.newsletter_handler === 'create_subscriptor'
      ) {
        $scope.newsletter_handler = 'lists';
      }

      $scope.expanded = $scope.newsletter_handler;
    };

    /**
     * Add selected email to receivers list and remove them from available
     * receivers list.
     */
    $scope.addRecipients = function(section) {
      // Had to use forEach in order to avoid to insert duplicates
      $scope.source.selected.forEach(function(el) {
        if ($scope.recipients.items.indexOf(el) < 0) {
          $scope.recipients.items.push(el);
        }
      });

      $scope.source.selected = [];
    };

    /**
     * Remove selected emails from receivers list and add them to available
     * receivers list.
     */
    $scope.removeRecipients = function() {
      for (var i = 0; i < $scope.recipients.selected.length; i++) {
        var index = $scope.recipients.items.indexOf($scope.recipients.selected[i]);

        $scope.recipients.items.splice(index, 1);
      }
    };

    /**
     * Updates scope when target item changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('recipients.items', function(nv, ov) {
      $scope.targetItems = angular.toJson(nv);
    }, true);

    /**
     * Parses and add more emails to newsletter receivers.
     */
    $scope.addMoreEmails = function() {
      $scope.moreEmailsError = false;

      var emails = $scope.moreEmails.split('\n');
      var pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      // Get only emails to easy checking
      var currentEmails = [];

      for (var i = 0; i < $scope.recipients.items.length; i++) {
        currentEmails.push($scope.recipients.items[i].email);
      }

      // Save new valid emails
      for (var i = 0; i < emails.length; i++) {
        if (pattern.test(emails[i]) &&
            currentEmails.indexOf(emails[i]) === -1
        ) {
          $scope.recipients.items.push({
            type: 'email',
            email: emails[i],
            name: emails[i],
          });
          $scope.moreEmails = $scope.moreEmails.replace(emails[i] + '\n', '');
        }

        if (!pattern.test(emails[i])) {
          $scope.moreEmailsError = true;
        }
      }
    };

    /**
     * Opens a modal to confirm newsletter sending.
     */
    $scope.send = function() {
      $uibModal.open({
        backdrop:    true,
        controller:  'YesNoModalCtrl',
        templateUrl: 'modal-confirm-send',
        resolve: {
          template: function() {
            return {};
          },
          yes: function() {
            $('form').submit();
          },
          no: function() {
            return function(modalWindow) {
              modalWindow.close();
            };
          }
        }
      });
    };

    $scope.toggleAllRecipients = function() {
      if ($scope.recipients.all) {
        $scope.recipients.selected = [];
      } else {
        $scope.recipients.selected = $scope.recipients.items;
      }
    };

    /*  ====================================================================== */
    $scope.saveHtml = function(url, save) {
      if (save) {
        var data = {
          title: $scope.subject,
          html: $scope.html
        };

        $http.post(url, data).success(function(response) {
          $scope.renderMessages(response.messages);
        });
      }
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
        id: 0,
        title: '',
        position: '',
        items: []
      });
    };

    $scope.moveContainerUp = function(container) {
      var from = $scope.newsletterContents.indexOf(container);
      var to = from - 1;

      $scope.newsletterContents.splice(to, 0, $scope.newsletterContents.splice(from, 1)[0]);
    };

    $scope.moveContainerDown = function(container) {
      var from = $scope.newsletterContents.indexOf(container);
      var to = from + 1;

      $scope.newsletterContents.splice(to, 0, $scope.newsletterContents.splice(from, 1)[0]);
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

    $scope.options = {
      accept: function(sourceNode, destNodes, destIndex) {
        var data = sourceNode.$modelValue;
        var destType = destNodes.$element.attr('type');

        return data.content_type == destType;
      }
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
              id:                     $scope.newsletterContents[i].items[j].id,
              content_type_name:      $scope.newsletterContents[i].items[j].content_type_name,
              content_type_l10n_name: $scope.newsletterContents[i].items[j].content_type_l10n_name,
              title:                  $scope.newsletterContents[i].items[j].title,
              content_type:           'content',
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

    $scope.html = $scope.hiddenHtml;
  }
]);
