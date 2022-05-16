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
         *  The index of the container where contents selected in content picker
         *  should be inserted.
         *
         * @type {Integer}
         */
        $scope.containerTarget = null;

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
          name: '',
          title: '',
          contents: [],
          schedule: {
            days:  [],
            hours: [],
          },
          recipients: [],
          params: [],
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
        $scope.numberOfElements = _.range(1, 21);

        /**
         * @memberOf UserCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_newsletter_template_create',
          redirect:   'backend_newsletter_template_show',
          saveItem:   'api_v1_backend_newsletter_template_save',
          getItem:    'api_v1_backend_newsletter_template_show',
          updateItem: 'api_v1_backend_newsletter_template_update'
        };

        $scope.target = [];

        /**
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *  The list of options for angular-ui-tree directive.
         *
         * @type {Object}
         */
        $scope.treeOptions = {
          accept: function(source, target) {
            if (target.$element.attr('type') === 'content') {
              return source.$modelValue.content_type ||
                source.$modelValue.content_type === 'list';
            }

            return !source.$modelValue.content_type;
          }
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.item.params.append_title = $scope.item.params.append_title ?
            parseInt($scope.item.params.append_title) : 0;

          // Remove recipients that are not in data.extra
          $scope.item.recipients = $scope.item.recipients.filter(function(e) {
            for (var i = 0; i < $scope.data.extra.recipients.length; i++) {
              if (angular.equals(e, $scope.data.extra.recipients[i])) {
                return true;
              }
            }
            return false;
          });

          if ($scope.item.contents) {
            $scope.item.contents.map(function(item) {
              item.items.map(function(e) {
                if (e.e_type === 'list' &&
                  (e.criteria.category === '' ||
                    typeof e.criteria.category === 'undefined')) {
                  e.criteria.category = [];
                }

                if (e.e_type === 'list' &&
                  typeof e.criteria.category === 'string') {
                  e.criteria.category = [ parseInt(e.criteria.category) ];
                }

                // If the element is a list then convert its category criteria to numbers
                if (e.e_type === 'list') {
                  e.criteria.category = e.criteria.category.map(Number);
                }

                return e;
              });

              return item;
            });
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

          return items.filter(function(e) {
            return e.content_type !== 'list';
          }).map(function(e) {
            return e.id;
          });
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
         *   Adds a container.
         */
        $scope.addContainer = function() {
          $scope.item.contents.push({
            title: '',
            items: []
          });
        };

        /**
         * @function addSearch
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Adds a dummy dynamic content.
         *
         * @param {Object} container The container where to remove.
         * @param {Object} content The content to remove.
         */
        $scope.addSearch = function(index) {
          $scope.item.contents[index].items.push({
            content_type: 'list',
            criteria: {
              content_type: 'article',
              category: [],
              opinion_type: '',
              epp: 5,
              in_litter: 0,
              filter: '',
              orderBy: { starttime:  'desc' }
            }
          });
        };

        /**
         * @function emptyContainer
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Empties a container if index is provided or all containers if index is
         *   not provided.
         *
         * @param {Integer} index The index of the container to empty.
         */
        $scope.emptyContainer = function(index) {
          if (angular.isDefined(index)) {
            $scope.item.contents[index].items = [];
            return;
          }

          for (var i = 0; i < $scope.item.contents.length; i++) {
            $scope.item.contents[i].items = [];
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
            $scope.item.contents.splice(index, 1);
            return;
          }

          $scope.item.contents = [];
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

        // Add contents to the marked container when content-picker-target changes
        $scope.$watch('target', function(nv) {
          if ($scope.containerTarget === null || !nv || nv.length === 0) {
            return;
          }

          $scope.item.contents[$scope.containerTarget].items =
            $scope.item.contents[$scope.containerTarget].items
              .concat(nv.map(function(e) {
                return {
                  content_type: e.content_type_name,
                  content_type_l10n_name: e.content_type_l10n_name,
                  id: e.pk_content,
                  title: e.title
                };
              }));

          $scope.containerTarget = null;
          $scope.target          = [];
        }, true);
      }
    ]);
})();
