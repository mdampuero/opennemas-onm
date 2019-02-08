(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberCtrl
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
     *   Provides actions to edit, save and update subscribers.
     */
    .controller('RestInnerCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner, http, messenger, routing) {
        $.extend(this, $controller('BaseCtrl', { $scope: $scope }));

        /**
         * @memberOf RestInnerCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
        $scope.item = {};

        /**
         * @memberOf RestInnerCtrl
         *
         * @description
         *  Whether to refresh the item after a successful update.
         *
         * @type {Boolean}
         */
        $scope.refreshOnUpdate = false;

        /**
         * @function generateTagsFrom
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Returns a string to use when clicking on "Generate" button for
         *   tags component.
         *
         * @return {String} The string to generate tags from.
         */
        $scope.generateTagsFrom = function() {
          return $scope.item.title;
        };

        /**
         * @function getData
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Returns the data to send when saving/updating an item.
         */
        $scope.getData = function() {
          // Do not use angular.copy as it doesnt copy some keys in the object
          var eltoClean = angular.extend({}, $scope.data.item);

          return cleaner.clean(eltoClean);
        };

        /**
         * @function getItem
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Gets the subscriber to show.
         *
         * @param {Integer} id The subscriber id.
         */
        $scope.getItem = function(id) {
          $scope.flags.http.loading = true;

          var route = { name: $scope.routes.create };

          if (id) {
            route.name   = $scope.routes.show;
            route.params = { id: id };
          }

          http.get(route).then(function(response) {
            $scope.data = response.data;

            if (response.data.item) {
              $scope.item = angular.extend($scope.item, response.data.item);
            }

            $scope.parseItem($scope.data);
            $scope.disableFlags('http');
          }, function() {
            $scope.item = null;
            $scope.disableFlags('http');
          });
        };

        /**
         * @function getItemId
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @return {Integer} The item id.
         */
        $scope.getItemId = function() {
          return $scope.item.id;
        };

        /**
         * @function itemHasId
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Checks if the current item has an id.
         *
         * @return {Boolean} description
         */
        $scope.itemHasId = function() {
          return $scope.getItemId() && $scope.getItemId() !== null;
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
          return data;
        };

        /**
         * @function save
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Saves a new item.
         */
        $scope.save = function() {
          if ($scope.form.$invalid) {
            return;
          }

          $scope.form.$setPristine(true);
          $scope.flags.http.saving = true;

          var data  = $scope.getData();
          var route = { name: $scope.routes.save };

          /**
           * Callback executed when subscriber is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags('http');

            if ($scope.routes.redirect && response.status === 201) {
              var id = response.headers().location
                .substring(response.headers().location.lastIndexOf('/') + 1);

              $window.location.href =
                routing.generate($scope.routes.redirect, { id: id });
            }

            if (response.status === 200 && $scope.refreshOnUpdate) {
              $timeout(function() {
                $scope.getItem($scope.getItemId());
              }, 500);
            }

            messenger.post(response.data);
          };

          if ($scope.itemHasId()) {
            route.name   = $scope.routes.update;
            route.params = { id: $scope.getItemId() };
            http.put(route, data).then(successCb, $scope.errorCb);

            return;
          }

          http.post(route, data).then(successCb, $scope.errorCb);
        };

        /**
         * @function getContentScheduling
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Returns -1 if the the item scheduling state is DUED.
         *   Returns  0 if the the item scheduling state is IN TIME.
         *   Returns  1 if the the item scheduling state is PLANNED.
         */
        $scope.getContentScheduling = function(item) {
          var now = new Date();

          var starttime = item.starttime ? new Date(item.starttime) : null;
          var endtime   = item.endtime ? new Date(item.endtime) : null;

          if (endtime && endtime.getTime() < now.getTime()) {
            return -1;
          }

          if (starttime && starttime.getTime() > now.getTime()) {
            return 1;
          }

          return 0;
        };
      }
    ]);
})();
