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
      '$controller', '$scope', 'oqlEncoder', 'oqlDecoder', 'messenger', 'cleaner', 'linker', 'localizer', '$timeout',
      function($controller, $scope, oqlEncoder, oqlDecoder, messenger, cleaner, linker, localizer, $timeout) {
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
          fk_content_type: 19,
          content_status: 0,
          description: '',
          favorite: 0,
          frontpage: 0,
          starttime: null,
          endtime: null,
          tag_ids: [],
          thumbnail: null,
          title: '',
          type: 0,
          with_comments: 0,
          categories: [],
          relations: [],

          event_startdate: null,
          event_enddate: null,
          event_starthour: null,
          event_endhour: null,
          event_place: null,
          external_link: '',
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
            $scope.item = angular.extend($scope.item, data.item);
          }

          $scope.category = $scope.item.categories.slice(1);

          // $scope.item.relations.each(function(index, el) {
          //   console.log(el);
          // });
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

        $scope.$watch('category', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          $scope.item.categories = [ Number(nv) ];
        });

        // Update slug when title is updated
        $scope.$watch('image', function(nv, ov) {
          if (!nv) {
            return;
          }

          $scope.item.relations = [];
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

        // Update metadata when title or category change
        $scope.$watch('[ item.title, item.category ]', function(nv, ov) {
          if ($scope.item.tag_ids && $scope.item.tag_ids.length > 0 ||
              !nv || nv === ov) {
            return;
          }

          if ($scope.mtm) {
            $timeout.cancel($scope.mtm);
          }

          $scope.mtm = $timeout(function() {
            $scope.loadAutoSuggestedTags();
          }, 2500);
        });
      }
    ]);
})();
