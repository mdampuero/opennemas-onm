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
      '$controller', '$scope', 'oqlEncoder', 'oqlDecoder', 'messenger',
      function($controller, $scope, oqlEncoder, oqlDecoder, messenger) {
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
         * @function parseItem
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseItem = function(data) {
          if (data.item) {
            data.item.contents.map(function(item) {
              item.items.map(function(content) {
                if (content.content_type === 'list' &&
                  content.criteria.category == '') {
                  content.criteria.category = [];
                }
                if (content.content_type === 'list' &&
                  typeof content.criteria.category === 'undefined') {
                  content.criteria.category = [ ];
                }

                if (content.content_type === 'list' &&
                  typeof content.criteria.category === 'string') {
                  content.criteria.category = [ parseInt(content.criteria.category) ];
                }

                // If the element is a list then convert its category criteria to numbers
                if (content.content_type === 'list') {
                  content.criteria.category = content.criteria.category.map(Number);
                }

                return content;
              });

              return item;
            });

            $scope.item = angular.extend($scope.item, data.item);
          }
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
              category: [],
              epp: 5,
              in_litter: 0,
              filter: '',
              orderBy: { starttime:  'desc' }
            }
          });
        };

        /**
         * @function addDynamicContent
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Adds/removes a categoryId from the content category criteria.
         *
         * @param {Object} content The content to change category criteria from.
         * @param {Object} categoryId The categoryId to add/remove from the criteria.
         */
        $scope.toggleCategory = function(content, categoryId) {
          var position = content.criteria.category.indexOf(categoryId);

          if (position < 0) {
            content.criteria.category.push(categoryId);
          } else {
            content.criteria.category.splice(position, 1);
          }
        };

        /**
         * @function toggleAllCategories
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Adds or removes all categories from the content criteria.
         *
         * @param {Object} content The content to change category criteria from.
         */
        $scope.toggleAllCategories = function(content) {
          if (content.criteria.category.length !== $scope.data.extra.categories.length) {
            content.criteria.category = $scope.data.extra.categories.map(function(item) {
              return item.pk_content_category;
            });
          } else {
            content.criteria.category = [];
          }
        };

        /**
         * @function save
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   save the newslettertemplate.
         *
         */
        $scope.saveVal = function() {
          if (!($scope.item.contents instanceof Array) ||
            $scope.item.contents.length === 0
          ) {
            messenger.post(newsletterTemplateTranslations.contenidosRequerido);
            return null;
          }
          $scope.save();
          return null;
        };
      }
    ]);
})();
