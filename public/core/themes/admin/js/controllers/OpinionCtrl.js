/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('OpinionCtrl', [
  '$controller', '$scope', '$uibModal', 'http', 'routing', 'cleaner',
  function($controller, $scope, $uibModal, http, routing, cleaner) {
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
     * @inheritdoc
     */
    $scope.buildScope = function() {
      $scope.localize($scope.data.item, 'item', true);

      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      var img1 = $scope.data.extra.related_contents.filter(function(e) {
        return parseInt(e.pk_content) === parseInt($scope.item.img1);
      }).shift();

      if (img1) {
        $scope.photo1 = img1;
      }

      var img2 = $scope.data.extra.related_contents.filter(function(e) {
        return parseInt(e.pk_content) === parseInt($scope.item.img2);
      }).shift();

      if (img2) {
        $scope.photo2 = img2;
      }
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
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo1', function(nv, ov) {
      if (angular.equals(nv, ov)) {
        return;
      }

      if (!nv) {
        $scope.item.img1        = null;
        $scope.item.img1_footer = null;
        return;
      }

      if (!$scope.item.id ||
          parseInt($scope.item.img1) !== parseInt(nv.pk_content)) {
        $scope.item.img1        = nv.pk_content;
        $scope.item.img1_footer = nv.description;

        if (angular.equals(ov, $scope.photo2)) {
          $scope.photo2 = nv;
        }
      }
    }, true);

    /**
     * Updates scope when photo2 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo2', function(nv, ov) {
      if (angular.equals(nv, ov)) {
        return;
      }

      if (!nv) {
        $scope.item.img2        = null;
        $scope.item.img2_footer = null;
        return;
      }

      if (!$scope.item.id ||
          parseInt($scope.item.img2) !== parseInt(nv.pk_content)) {
        $scope.item.img2        = nv.pk_content;
        $scope.item.img2_footer = nv.description;
      }
    }, true);
  }
]);
