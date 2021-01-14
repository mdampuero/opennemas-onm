/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('OpinionCtrl', [
  '$controller', '$scope', '$uibModal', 'http', 'linker', 'localizer', 'routing', 'cleaner',
  function($controller, $scope, $uibModal, http, linker, localizer, routing, cleaner) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The cover object.
     *
     * @type {Object}
     */
    $scope.item = {
      body: '',
      content_type_name: 'opinion',
      fk_content_type: 4,
      content_status: 0,
      description: '',
      favorite: 0,
      frontpage: 0,
      created: new Date(),
      starttime: null,
      endtime: null,
      thumbnail: null,
      title: '',
      type: 0,
      with_comment: 0,
      categories: [],
      related_contents: [],
      tags: [],
      external_link: '',
    };

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The map for related contents.
     *
     * @type {Object}
     */
    $scope.relatedMap = {
      featured_frontpage: {
        name:        'featuredFrontpage',
        replicateOn: 'featured_inner',
        simple:      true
      },
      featured_inner: {
        name:   'featuredInner',
        simple: true
      },
    };

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      createItem:  'api_v1_backend_opinion_create_item',
      getItem:     'api_v1_backend_opinion_get_item',
      getPreview:  'api_v1_backend_opinion_get_preview',
      list:        'backend_opinions_list',
      redirect:    'backend_opinion_show',
      saveItem:    'api_v1_backend_opinion_save_item',
      savePreview: 'api_v1_backend_opinion_save_preview',
      updateItem:  'api_v1_backend_opinion_update_item'
    };


    /**
     * @function buildRelated
     * @memberOf OpinionCtrl
     *
     * @description
     *   Initializes the scope with the list of related contents and
     *   defines watchers to update the model on changes.
     */
    $scope.buildRelated = function() {
      $scope.item.related_contents = [];

      for (var i = 0; i < $scope.data.item.related_contents.length; i++) {
        var related = $scope.data.item.related_contents[i];

        $scope.item.related_contents.push($scope.localizeRelated(related, i));

        var simple = $scope.relatedMap[related.type].simple;
        var name   = $scope.relatedMap[related.type].name;
        var item   = $scope.data.extra.related_contents[related.target_id];

        if (!simple) {
          if (!$scope.name) {
            $scope[name] = [];
          }

          $scope[name].push(item);
          continue;
        }

        $scope[name] = item;
      }

      // Updates related contents after insertion via content picker
      $scope.$watch('[ featuredFrontpage, featuredInner ]', function(nv, ov) {
        for (var i = 0; i < nv.length; i++) {
          var type = Object.keys($scope.relatedMap)[i];

          if (angular.equals(nv[i], ov[i])) {
            continue;
          }

          var caption     = null;
          var removedItem = null;

          if ($scope.relatedMap[type].simple) {
            if (ov[i]) {
              // Try to keep caption from old item
              removedItem = $scope.data.item.related_contents.filter(function(e) {
                return e.type === type;
              }).shift();

              caption     = removedItem ? removedItem.caption : null;
              removedItem = removedItem ?
                $scope.data.extra.related_contents[removedItem.target_id] :
                null;
            }

            // Remove from unlocalized
            $scope.data.item.related_contents =
              $scope.data.item.related_contents.filter(function(e) {
                return e.type !== type;
              });

            // Remove from localized
            $scope.item.related_contents =
              $scope.item.related_contents.filter(function(e) {
                return e.type !== type;
              });
          }

          if (!nv[i]) {
            continue;
          }

          var items = $scope.relatedMap[type].simple ? [ nv[i] ] : nv[i];

          for (var j = 0; j < items.length; j++) {
            // Add content to map of contents
            if (!$scope.data.extra.related_contents[items[j].pk_content]) {
              $scope.data.extra.related_contents[items[j].pk_content] = items[j];
            }

            /**
             * Override caption when adding new item or when caption matches
             * the removed item description
             */
            if (!removedItem || removedItem.description === caption) {
              caption = items[j].description;
            }

            var related = {
              caption:           caption,
              content_type_name: items[j].content_type_name,
              position:          j,
              target_id:         items[j].pk_content,
              type:              type
            };

            $scope.data.item.related_contents.push(related);
            $scope.item.related_contents.push($scope.localizeRelated(related, j));
          }

          // Copy current item to another item
          if ($scope.relatedMap[type].replicateOn) {
            var replicated = $scope.relatedMap[$scope.relatedMap[type].replicateOn];

            if (!$scope[replicated.name] ||
                angular.equals($scope[replicated.name], ov[i])) {
              $scope[replicated.name] = angular.copy(nv[i]);
            }
          }
        }
      }, true);
    };

    /**
     * @inheritdoc
     */
    $scope.buildScope = function() {
      $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);

      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      $scope.buildRelated();
    };

    /**
     * Opens a modal with the preview of the article.
     */
    $scope.preview = function() {
      $scope.flags.http.generating_preview = true;

      // Force ckeditor
      CKEDITOR.instances.body.updateElement();
      CKEDITOR.instances.summary.updateElement();

      var status = { starttime: null, endtime: null, content_status: 1 };
      var item   = Object.assign(Object.assign({}, $scope.item), status);

      var data = {
        item: JSON.stringify(cleaner.clean(item)),
        locale: $scope.config.locale.selected
      };

      http.put($scope.routes.savePreview, data).then(function() {
        $uibModal.open({
          templateUrl: 'modal-preview',
          windowClass: 'modal-fullscreen',
          controller: 'ModalCtrl',
          resolve: {
            template: function() {
              return {
                src: routing.generate($scope.routes.getPreview)
              };
            },
            success: function() {
              return null;
            }
          }
        });

        $scope.flags.http.generating_preview = false;
      }, function() {
        $scope.flats.http.generating_preview = false;
      });
    };

    /**
     * Returns the frontend url for the content given its object.
     *
     * @param {String} item  The object item to generate the url from.
     *
     * @return {String} The frontend URL.
     */
    $scope.getFrontendUrl = function(item) {
      var date = item.created;

      var formattedDate = moment(date).format('YYYYMMDDHHmmss');

      return $scope.getL10nUrl(
        routing.generate('frontend_opinion_show', {
          id: item.pk_content,
          created: formattedDate,
          opinion_title: item.slug
        })
      );
    };

    /**
     * @function getRelated
     * @memberOf OpinionCtrl
     *
     * @description
     *   Returns the related content based on the type.
     *
     * @param {String} type The related type.
     *
     * @return {Object} The related content.
     */
    $scope.getRelated = function(type) {
      for (var i = 0; i < $scope.item.related_contents.length; i++) {
        if ($scope.item.related_contents[i].type === type) {
          return $scope.item.related_contents[i];
        }
      }

      return null;
    };

    /**
     * @function localizeRelated
     * @memberOf OpinionCtrl
     *
     * @description
     *   Localizes a related content.
     *
     * @param {Object} original The content to localize.
     *
     * @return {Object} The localized content.
     */
    $scope.localizeRelated = function(original, index) {
      var localized = localizer.get($scope.config.locale).localize(original,
        [ 'caption' ], $scope.config.locale);

      // Initialize linker
      delete $scope.config.linkers[index];
      $scope.config.linkers[index] = linker.get([ 'caption' ],
        $scope.config.locale.default, $scope, true);

      // Link original and localized items
      $scope.config.linkers[index].setKey($scope.config.locale.selected);
      $scope.config.linkers[index].link(original, localized);

      return localized;
    };
  }
]);
