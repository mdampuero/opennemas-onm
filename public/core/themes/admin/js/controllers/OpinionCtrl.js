/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('OpinionCtrl', [
  '$controller', '$scope', '$uibModal', 'http', 'related', 'routing', 'cleaner',
  function($controller, $scope, $uibModal, http, related, routing, cleaner) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  Flag to enabled or disable drafts.
     *
     * @type {Boolean}
     */
    $scope.draftEnabled = true;

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The draft key.
     *
     * @type {String}
     */
    $scope.draftKey = 'opinion-draft';

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The timeout function for draft.
     *
     * @type {Function}
     */
    $scope.dtm = null;

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
     *  The related contents service
     *
     * @type {Object}
     */
    $scope.related = related;

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
      $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);

      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      $scope.checkDraft();
      related.init($scope);
      related.watch();
    };

    /**
     * Opens a modal with the preview of the article.
     */
    $scope.preview = function() {
      $scope.flags.http.generating_preview = true;

      // Force ckeditor
      CKEDITOR.instances.body.updateElement();
      CKEDITOR.instances.description.updateElement();

      var status = { starttime: null, endtime: null, content_status: 1 };
      var item   = Object.assign({}, $scope.data.item, status);

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
        $scope.flags.http.generating_preview = false;
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
  }
]);
