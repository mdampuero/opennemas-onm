(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  LetterCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires linker
     * @requires localizer
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Provides actions to edit, save and update articles.
     */
    .controller('LetterCtrl', [
      '$controller', '$scope', '$timeout', '$window', 'routing',
      function($controller, $scope, $timeout, $window,
          routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.draftEnabled = true;

        /**
         * @inheritdoc
         */
        $scope.draftKey = 'letter-draft';

        /**
         * @inheritdoc
         */
        $scope.dtm = null;

        /**
         * @inheritdoc
         */
        $scope.incomplete = true;

        /**
         * @memberOf LetterCtrl
         *
         * @description
         *  The letter object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'letter',
          fk_content_type: 17,
          content_status: 0,
          description: '',
          frontpage: 0,
          created: null,
          starttime: null,
          endtime: null,
          title: '',
          with_comment: 0,
          related_contents: [],
          tags: [],
          url: '',
          author: '',
          email: '',
        };

        /**
         * @memberOf LetterCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem:  'api_v1_backend_letter_create_item',
          getItem:     'api_v1_backend_letter_get_item',
          list:        'backend_letters_list',
          saveItem:    'api_v1_backend_letter_save_item',
          updateItem:  'api_v1_backend_letter_update_item',
          public:      'frontend_letter_show',
          redirect:    'backend_letter_show',
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ ]);

          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'letter-' + $scope.data.item.pk_content + '-draft';
          }

          $scope.checkDraft();
        };

        /**
         *
         * @function getFrontendUrl
         * @memberOf LetterCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              author: item.author
            })
          );
        };

        // Update title_int when title changes
        $scope.$watch('item.title', function(nv, ov) {
          if (!nv && !ov) {
            return;
          }

          if (!$scope.item.pk_content) {
            if ($scope.tm) {
              $timeout.cancel($scope.tm);
            }

            if (!nv) {
              $scope.item.slug = '';
            }
          }
        }, true);

        /**
         * @inheritdoc
         */
        $scope.validate = function() {
          if ($scope.form && $scope.form.$invalid) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          if (!$('[name=form]')[0].checkValidity()) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          return true;
        };
      }
    ]);
})();
