(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     *
     * @description
     *   Check billing information when saving user.
     */
    .controller('NewsletterTemplateCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'oqlDecoder',
      function($controller, $scope, oqlEncoder, oqlDecoder) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *  The newsletter object.
         *
         * @type {Object}
         */
        $scope.item = {
          type: 1,
          status: 0,
          title: '',
          contents: [],
          schedule: {
            days:  [],
            hours: [],
          },
          recipients: [],
        };

        /**
         * @function numberOfElements
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   The list of posible amount of contents.
         *
         * @type {Array}
         */
        $scope.numberOfElements = [];
        for (var i = 1; i < 21; i++) {
          $scope.numberOfElements.push(i);
        }

        /**
         * @memberOf UserCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_newsletter_template_create',
          redirect: 'backend_newsletter_template_show',
          save:     'api_v1_backend_newsletter_template_save',
          show:     'api_v1_backend_newsletter_template_show',
          update:   'api_v1_backend_newsletter_template_update'
        };

        /**
         * @function loadHours
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Returns the filtered list of hours given a search query.
         *
         * @param {String} $query The text to filter the hours.
         */
        $scope.loadHours = function($query) {
          return $scope.data.extra.hours.filter(function(el) {
            return el.indexOf($query) >= 0;
          });
        };

        /**
         * @function addContainer
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Adds a new container to the newsletter contents.
         */
        $scope.addContainer = function() {
          $scope.item.contents.push({
            title: '',
            items: []
          });
        };

        /**
         * @function removeContainer
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Removes a container from the list.
         *
         * @param {Object} container The container to remove.
         */
        $scope.removeContainer = function(container) {
          var position = $scope.item.contents.indexOf(container);

          $scope.item.contents.splice(position, 1);
        };

        /**
         * @function removeContent
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Removes a content from a container.
         *
         * @param {Object} container The container where to remove.
         * @param {Object} content The content to remove.
         */
        $scope.removeContent = function(container, content) {
          var position = container.items.indexOf(content);

          container.items.splice(position, 1);
        };

        /**
         * @function addDynamicContent
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Adds a dummy dynamic content.
         *
         * @param {Object} container The container where to remove.
         * @param {Object} content The content to remove.
         */
        $scope.addDynamicContent = function(container) {
          container.items.push({
            content_type: 'list',
            criteria: {
              content_type: '',
              category: '',
              epp: 5,
              in_litter: 0,
              orderBy: { starttime:  'desc' }
            }
          });
        };
      }
    ]);
})();
