(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  EventCtrl
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
    .controller('EventCtrl', [
      '$controller', '$scope', '$timeout',
      function($controller, $scope, $timeout) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

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
          starttime: null,
          endtime: null,
          thumbnail: null,
          title: '',
          type: 0,
          with_comments: 0,
          categories: [],
          related_contents: [],
          tags: [],
          event_startdate: null,
          event_enddate: null,
          event_starthour: null,
          event_endhour: null,
          event_place: null,
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
            $scope.item      = angular.extend($scope.item, data.item);
            $scope.item.tags = $scope.item.tags.map(function(id) {
              return data.extra.tags[id];
            });
          }

          var coverId = $scope.item.related_contents.filter(function(el) {
            return el.relationship === 'cover';
          }).shift();

          if (!coverId) {
            return;
          }

          $scope.cover = data.extra.related_contents[coverId.pk_content2];
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
         * @function getItemId
         * @memberOf EventCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @return {Integer} The item id.
         */
        $scope.getItemId = function() {
          return $scope.item.pk_content;
        };

        // Update slug when title is updated
        $scope.$watch('cover', function(nv, ov) {
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
      }
    ]);
})();
