(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PollCtrl
     *
     * @description
     *   Handles actions for poll inner
     *
     * @requires $controller
     * @requires $scope
     * @requires linker
     * @requires localizer
     * @requires messenger
     * @requires routing
     */
    .controller('PollCtrl', [
      '$controller', '$scope', 'linker', 'localizer', 'messenger', 'routing',
      function($controller, $scope, linker, localizer, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf PollCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'poll',
          fk_content_type: 11,
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
          tags: [],
          agency: '',
          items: [],
        };

        /**
         * @memberOf PollCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_poll_create_item',
          getItem:    'api_v1_backend_poll_get_item',
          public:     'frontend_poll_show',
          redirect:   'backend_poll_show',
          saveItem:   'api_v1_backend_poll_save_item',
          updateItem: 'api_v1_backend_poll_update_item'
        };

        /**
         * @function addAnswer
         * @memberOf PollCtrl
         *
         * @description
         *   Adds an empty answer to the answer list.
         */
        $scope.addAnswer = function() {
          var item = { pk_item: '', votes: 0, item: '' };

          $scope.data.item.items.push(item);

          // Localize and add new item to localized item
          $scope.item.items.push($scope.localizeOption(item,
            $scope.data.item.items.length));
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'items' ]);

          // Check if item is new (created) or existing for use default value or not
          if ($scope.data.item.title.length === 0) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          $scope.item.items = [];

          for (var i = 0; i < $scope.data.item.items.length; i++) {
            $scope.item.items.push($scope.localizeOption(
              $scope.data.item.items[i], $scope.item.items.length));
          }
        };

        /**
         * @function getFrontendUrl
         * @memberOf AlbumCtrl
         *
         * @description
         * Returns the frontend url for the content given its object
         *
         * @param  {String} item  The object item to generate the url from.
         * @return {String}
         */
        $scope.getFrontendUrl = function(item) {
          var date = item.date;

          var formattedDate = window.moment(date).format('YYYYMMDDHHmmss');

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: formattedDate,
              slug: item.slug,
              category_name: item.category_name
            })
          );
        };

        /**
         * @function isClosed
         * @memberOf PollCtrl
         *
         * @description
         *   Checks if the current poll is already closed.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the poll is closed. False otherwise.
         */
        $scope.isClosed = function(item) {
          return item && item.params && item.params.closetime &&
            new Date(item.params.closetime) < new Date();
        };

        /**
         * @function localizeOption
         * @memberOf PollCtrl
         *
         * @description
         *   Localizes an option in the array of options.
         *
         * @param {Object}  original The option to localize.
         * @param {Integer} index    The index in the array of options to use as
         *                           linker name.
         */
        $scope.localizeOption = function(original, index) {
          var localized = localizer.get($scope.config.locale).localize(original,
            [ 'item' ], $scope.config.locale);

          // Initialize linker
          delete $scope.config.linkers[index];
          $scope.config.linkers[index] = linker.get([ 'item' ],
            $scope.config.locale.default, $scope, true);

          // Link original and localized items
          $scope.config.linkers[index].setKey($scope.config.locale.selected);
          $scope.config.linkers[index].link(original, localized);

          return localized;
        };

        /**
         * @function removeAnswer
         * @memberOf PollCtrl
         *
         * @description
         *   Removes one answer from the answer list given its index.
         *
         * @param {Integer} index The index of the answer to remove.
         */
        $scope.removeAnswer = function(index) {
          $scope.item.items.splice(index, 1);
          $scope.data.item.items.splice(index, 1);
        };
      }
    ]);
})();
