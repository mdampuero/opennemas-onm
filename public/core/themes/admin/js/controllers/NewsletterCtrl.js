/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('NewsletterCtrl', [
  '$controller', '$http', '$uibModal', '$rootScope', '$sce', '$scope',
  function($controller, $http, $uibModal, $rootScope, $sce, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @memberOf NewsletterCtrl
     *
     * @description
     *  The index of the container where contents selected in content picker
     *  should be inserted.
     *
     * @type {Integer}
     */
    $scope.containerTarget = null;

    $scope.recipients = {
      all: false,
      items: [],
      selected: []
    };

    $scope.source = {
      items: [],
      selected: []
    };

    /**
     * @memberOf NewsletterCtrl
     *
     * @description
     *  The object to store content-picker selection.
     *
     * @type {Array}
     */
    $scope.target = [];

    /**
     * @memberOf NewsletterCtrl
     *
     * @description
     *  The list of options for angular-ui-tree directive.
     *
     * @type {Object}
     */
    $scope.treeOptions = {
      accept: function(source, target) {
        return source.$modelValue.pk_content &&
          target.$element.attr('type') === 'content' ||
          !source.$modelValue.pk_content &&
          target.$element.attr('type') !== 'content';
      }
    };

    /**
     * @function getItemIds
     * @memberOf NewsletterCtrl
     *
     * @description
     *   Returns the list of ids for items added to a container.
     *
     * @param {Array} items The list of items in a container.
     *
     * @return {Array} The list of ids.
     */
    $scope.getItemIds = function(items) {
      if (!items || !(items instanceof Array)) {
        return [];
      }

      return items.map(function(e) {
        return e.pk_content;
      });
    };

    /**
     * Initialize list of mail accounts
     */
    $scope.initPickRecipients = function(newsletter, extra) {
      $scope.extra              = extra;
      $scope.newsletter         = newsletter;
      $scope.source.items       = extra.recipients;
      $scope.newsletter_handler = extra.newsletter_handler;

      if ($scope.newsletter_handler === 'create_subscriptor') {
        $scope.newsletter_handler = 'lists';
      }

      if ($scope.newsletter_handler === 'submit') {
        $scope.newsletter_handler = 'external';
      }

      $scope.expanded = $scope.newsletter_handler;
    };

    /**
     * Add selected email to receivers list and remove them from available
     * receivers list.
     */
    $scope.addRecipients = function() {
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
    $scope.$watch('recipients.items', function(nv) {
      $scope.targetItems = angular.toJson(nv);
    }, true);

    /**
     * Parses and add more emails to newsletter receivers.
     */
    $scope.addMoreEmails = function() {
      $scope.moreEmailsError = false;

      var emails = $scope.moreEmails.split('\n');
      var pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      // Delete duplicate emails
      emails = emails.filter(function(item, pos) {
        return emails.indexOf(item) === pos;
      });

      // Get only emails to easy checking
      var currentEmails = [];

      for (var i = 0; i < $scope.recipients.items.length; i++) {
        currentEmails.push($scope.recipients.items[i].email);
      }

      // Save new valid emails (up to a maximum of 100)
      for (var i = 0; i < emails.length && $scope.recipients.items.length < 100; i++) {
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
      var emailsToSend = 0;

      $scope.recipients.items.forEach(function(el) {
        if (el.type === 'list') {
          emailsToSend += $scope.extra.users[el.id];
        } else {
          emailsToSend++;
        }
      });

      $uibModal.open({
        backdrop:    true,
        controller:  'YesNoModalCtrl',
        templateUrl: 'modal-confirm-send',
        resolve: {
          template: function() {
            return {
              emails_to_send: emailsToSend
            };
          },
          yes: function() {
            return function() {
              $('form').submit();
            };
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

        $http.post(url, data).then(function(response) {
          $scope.renderMessages(response.data.messages);
        });
      }
    };

    /*  ====================================================================== */
    $scope.stepOne = function(containers) {
      $scope.containers = [];

      if (containers !== null) {
        $scope.containers = containers;
      }
    };

    /**
     * @function addContainer
     * @memberOf NewsletterCtrl
     *
     * @description
     *   Adds a container.
     */
    $scope.addContainer = function() {
      $scope.containers.push({
        title: '',
        items: []
      });
    };

    /**
     * @function emptyContainer
     * @memberOf NewsletterCtrl
     *
     * @description
     *   Empties a container if index is provided or all containers if index is
     *   not provided.
     *
     * @param {Integer} index The index of the container to empty.
     */
    $scope.emptyContainer = function(index) {
      if (angular.isDefined(index)) {
        $scope.containers[index].items = [];
        return;
      }

      for (var i = 0; i < $scope.containers.length; i++) {
        $scope.containers[i].items = [];
      }
    };

    /**
     * @function markContainer
     * @memberOf NewsletterCtrl
     *
     * @description
     *   Marks a container as target after clicking on button to add contents.
     *
     * @param {Integer} index The index of the container in the list of
     *                        containers.
     */
    $scope.markContainer = function(index) {
      $scope.containerTarget = index;
    };

    /**
     * @function removeContainer
     * @memberOf NewsletterCtrl
     *
     * @description
     *   Removes a container.
     *
     * @param {Integer} index The index of the container to remove.
     */
    $scope.removeContainer = function(index) {
      if (angular.isDefined(index)) {
        $scope.containers.splice(index, 1);
        return;
      }

      $scope.containers = [];
    };

    /**
     * @function removeContent
     * @memberOf NewsletterCtrl
     *
     * @description
     *   Removes a content from a container.
     *
     * @param {Array}   container The container to remove contents from.
     * @param {Integer} index     The index of the content to remove.
     */
    $scope.removeContent = function(container, index) {
      container.items.splice(index, 1);
    };

    // Encodes containers as a JSON string when containers change
    $scope.$watch('containers', function() {
      if (!$scope.containers) {
        return;
      }

      $scope.contents = angular.toJson($scope.containers.map(function(e) {
        return {
          title: e.title,
          items: e.items.map(function(e) {
            return { id: e.pk_content, content_type: e.content_type_name };
          })
        };
      }));
    }, true);

    // Add contents to the marked container when content-picker-target changes
    $scope.$watch('target', function(nv) {
      if ($scope.containerTarget === null || !nv || nv.length === 0) {
        return;
      }

      $scope.containers[$scope.containerTarget].items =
        $scope.containers[$scope.containerTarget].items.concat(nv);

      $scope.containerTarget = null;
      $scope.target          = [];
    }, true);

    // Updates the trusted HTML to show in preview when HTML changes
    $scope.$watch('html', function() {
      $scope.trustedHtml = $sce.trustAsHtml($scope.html);
    });

    $scope.html = $scope.hiddenHtml;
  }
]);
