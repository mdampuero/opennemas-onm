/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('OpinionCtrl', [
  '$controller', '$http', '$uibModal', '$rootScope', '$scope', 'routing',
  function($controller, $http, $uibModal, $rootScope, $scope, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf EventCtrl
     *
     * @description
     *  The cover object.
     *
     * @type {Object}
     */
    $scope.item = {
      body: '',
      content_type_name: 'opinion',
      fk_content_type: 5,
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
     * @memberOf EventCtrl
     *
     * @description
     *  Whether to refresh the item after a successful update.
     *
     * @type {Boolean}
     */
    $scope.refreshOnUpdate = true;

    /**
     * @memberOf EventCtrl
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
        $scope.data.item      = angular.extend($scope.item, data.item);
        $scope.data.item.tags = $scope.item.tags.map(function(id) {
          return data.extra.tags[id];
        });
      }

      $scope.configure(data.extra);
      $scope.localize($scope.data.item, 'item', true);

      var coverId = $scope.data.item.related_contents.filter(function(el) {
        return el.relationship === 'cover';
      }).shift();

      if (!coverId) {
        return;
      }

      $scope.cover = data.extra.related_contents[coverId.pk_content2];
    };

    // Update slug when title is updated
    $scope.$watch('item.title', function(nv, ov) {
      if (!nv) {
        return;
      }

      if (!$scope.item.slug || $scope.item.slug === '') {
        if ($scope.tm) {
          $timeout.cancel($scope.tm);
        }

        $scope.tm = $timeout(function() {
          $scope.getSlug(nv, function(response) {
            $scope.item.slug = response.data.slug;
          });
        }, 2500);
      }
    }, true);

    /**
     * Opens a modal with the preview of the article.
     *
     * @param {String} previewUrl    The URL to generate the preview.
     * @param {String} getPreviewUrl The URL to get the preview.
     */
    $scope.preview = function(previewUrl, getPreviewUrl) {
      $scope.loading = true;

      // Force ckeditor
      CKEDITOR.instances.body.updateElement();
      CKEDITOR.instances.summary.updateElement();

      var data = { contents: $('#formulario').serializeArray() };
      var url  = routing.generate(previewUrl);

      $http.post(url, data).success(function() {
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

        $scope.loading = false;
      }).error(function() {
        $scope.loading = false;
      });
    };

    /**
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo1', function(nv, ov) {
      $scope.img1 = null;

      if ($scope.photo1) {
        $scope.img1 = $scope.photo1.id;

        if (angular.isUndefined($scope.img1_footer) ||
          angular.isUndefined(ov) ||
          nv.id !== ov.id
        ) {
          $scope.img1_footer = $scope.photo1.description;
        }

        // Set inner image if empty
        if (angular.isUndefined($scope.photo2) && nv !== ov) {
          $scope.photo2 = $scope.photo1;
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
      $scope.img2 = null;

      if ($scope.photo2) {
        $scope.img2 = $scope.photo2.id;

        if (angular.isUndefined($scope.img2_footer) ||
          angular.isUndefined(ov) ||
          nv.id !== ov.id
        ) {
          $scope.img2_footer = $scope.photo2.description;
        }
      }
    }, true);
  }
]);
