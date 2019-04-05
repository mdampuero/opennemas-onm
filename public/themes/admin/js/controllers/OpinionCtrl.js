/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('OpinionCtrl', [
  '$controller', 'http', '$uibModal', '$scope', 'routing', 'cleaner',
  function($controller, http, $uibModal, $scope, routing, cleaner) {
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
      with_comments: 0,
      categories: [],
      related_contents: [],
      tags: [],
      external_link: '',
    };

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The photo1 object.
     *
     * @type {Object}
     */
    $scope.photo1 = null;

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The photo2 object.
     *
     * @type {Object}
     */
    $scope.photo2 = null;

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      create:   'api_v1_backend_opinion_create',
      redirect: 'backend_opinion_show',
      save:     'api_v1_backend_opinion_save',
      show:     'api_v1_backend_opinion_show',
      update:   'api_v1_backend_opinion_update'
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
        $scope.data.item = angular.extend($scope.item, data.item);
      }

      $scope.configure(data.extra);
      $scope.localize($scope.data.item, 'item', true);

      var img1 = data.extra.related_contents.filter(function(el) {
        return el.pk_photo === $scope.item.img1;
      }).shift();

      if (img1) {
        $scope.photo1 = img1;
      }

      var img2 = data.extra.related_contents.filter(function(el) {
        return el.pk_photo === $scope.item.img2;
      }).shift();

      if (img2) {
        $scope.photo2 = img2;
      }
    };

    /**
     * Opens a modal with the preview of the article.
     *
     * @param {String} previewUrl    The URL to generate the preview.
     * @param {String} getPreviewUrl The URL to get the preview.
     */
    $scope.preview = function(previewUrl, getPreviewUrl) {
      $scope.flags.http.generating_preview = true;

      // Force ckeditor
      CKEDITOR.instances.body.updateElement();
      CKEDITOR.instances.summary.updateElement();

      var data = {
        item: JSON.stringify(cleaner.clean($scope.item)),
        locale: $scope.config.locale.selected
      };

      http.put(previewUrl, data).success(function() {
        $uibModal.open({
          templateUrl: 'modal-preview',
          windowClass: 'modal-fullscreen',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
                src: routing.generate(getPreviewUrl)
              };
            },
            success: function() {
              return null;
            }
          }
        });

        $scope.flags.http.generating_preview = false;
      }).error(function() {
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
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo1', function(nv, ov) {
      if (angular.isObject(nv)) {
        $scope.item.img1 = nv.pk_photo;

        if (angular.isUndefined($scope.item.img1_footer) ||
          angular.isUndefined(ov) ||
          ov === null ||
          nv.pk_photo !== ov.pk_photo
        ) {
          $scope.item.img1_footer = $scope.photo1.description;
        }
        // Set inner image if empty
        if (!angular.isObject($scope.photo2) && nv !== ov) {
          $scope.photo2 = $scope.photo1;
        }
      } else {
        $scope.item.img1 = null;
        $scope.item.img1_footer = null;
      }
    }, true);

    /**
     * Updates scope when photo2 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo2', function(nv, ov) {
      if (angular.isObject(nv)) {
        $scope.item.img2 = nv.id;

        if (angular.isUndefined($scope.item.img2_footer) ||
          angular.isUndefined(ov) ||
          ov === null ||
          nv.pk_photo !== ov.pk_photo
        ) {
          $scope.item.img2_footer = $scope.photo2.description;
        }
      } else {
        $scope.item.img2 = null;
        $scope.item.img2_footer = null;
      }
    }, true);
  }
]);
