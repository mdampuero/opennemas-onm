(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  EventCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     *
     * @description
     *   Check billing information when saving user.
     */
    .controller('EventCtrl', [
      '$controller', '$scope', '$timeout', 'messenger',
      function($controller, $scope, $timeout, messenger) {
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
          content_type_name: 'event',
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
          categories: [ null ],
          related_contents: [],
          tags: [],
          event_start_date: null,
          event_end_date: null,
          event_start_hour: null,
          event_end_hour: null,
          event_place: null,
          external_link: '',
        };

        /**
         * @memberOf EventCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_event_create',
          redirect: 'backend_event_show',
          save:     'api_v1_backend_event_save',
          show:     'api_v1_backend_event_show',
          update:   'api_v1_backend_event_update'
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

          var coverId = $scope.data.item.related_contents.filter(function(el) {
            return el.relationship === 'cover';
          }).shift();

          if (!coverId) {
            return;
          }

          $scope.cover = data.extra.related_contents[coverId.pk_content2];
        };

        // Update slug when title is updated
        $scope.$watch('cover', function(nv) {
          $scope.item.related_contents = [];

          if (!nv) {
            return;
          }

          $scope.item.related_contents.push({
            pk_content2: nv.pk_content,
            relationship: 'cover',
            position: 0
          });
        }, true);
      }
    ]);
})();
