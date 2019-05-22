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
          thumbnail: null,
          title: '',
          type: 0,
          with_comments: 0,
          categories: [],
          related_contents: [],
          tags: [],
          external_link: '',
          agency: '',
          cover: null,
          cover_image: null,
          photos: [],
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
          create:   'api_v1_backend_poll_create',
          public:   'frontend_poll_show',
          redirect: 'backend_poll_show',
          save:     'api_v1_backend_poll_save',
          show:     'api_v1_backend_poll_show',
          update:   'api_v1_backend_poll_update'
        };

        /**
         * @function addAnswer
         * @memberOf PollCtrl
         *
         * @description
         *   Adds an empty answer to the answer list.
         */
        $scope.addAnswer = function() {
          $scope.item.items.push({ pk_item: '', votes: 0, item: '' });
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
         * @inheritdoc
         */
        $scope.parseItem = function(data) {
          if (data.item) {
            $scope.data.item = angular.extend($scope.item, data.item);
          }

          $scope.configure(data.extra);
          $scope.localize($scope.data.item, 'item', true, [ 'items' ]);

          $scope.item.items = [];
          for (var i = 0; i < $scope.data.item.items.length; i++) {
            $scope.item.items.push($scope.localizeOption(
              $scope.data.item.items[i], $scope.item.items.length));
          }
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
        };
      }
    ]);
})();
