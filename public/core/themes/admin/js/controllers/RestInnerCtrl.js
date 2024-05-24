(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Provides generic actions to edit, save and update items.
     */
    .controller('RestInnerCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window',
      'cleaner', 'http', 'messenger', 'routing', 'webStorage',
      function($controller, $scope, $timeout, $uibModal, $window,
          cleaner, http, messenger, routing, webStorage) {
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
         * @memberOf RestInnerCtrl
         *
         * @description
         *  Expand fields on form load
         *
         */
        $scope.expandFields = function() {
          if ($scope.formSettings && $scope.formSettings.name && $scope.app.fields[$scope.formSettings.name] &&
            $scope.app.fields[$scope.formSettings.name].expanded) {
            $scope.expanded = Object.assign({}, $scope.app.fields[$scope.formSettings.name].expanded);
          }
        };

        /**
         * @memberOf RestInnerCtrl
         *
         * @description
         *  Call modal and set expanded fields by default in form
         *
         * @type {Object}
         */
        $scope.expansibleSettings = function() {
          var modal = $uibModal.open({
            backdrop:    'static',
            templateUrl: 'modal-expansible-fields',
            controller:  'ModalCtrl',
            resolve: {
              template: function() {
                var fields = $scope.app.fields && $scope.app.fields[$scope.formSettings.name] &&
                  $scope.app.fields[$scope.formSettings.name].expanded ?
                  $scope.app.fields[$scope.formSettings.name].expanded : {};

                return {
                  formSettings: $scope.formSettings,
                  defaultExpanded: Object.assign({}, fields)
                };
              },
              success: function() {
                return function(modal, template) {
                  $scope.app.fields[$scope.formSettings.name] = {};
                  $scope.app.fields[$scope.formSettings.name].expanded = Object.assign({}, template.defaultExpanded);
                  return true;
                };
              }
            }
          });

          modal.result.then(function(response) {
            var fields = $scope.app.fields && $scope.app.fields[$scope.formSettings.name] &&
              $scope.app.fields[$scope.formSettings.name].expanded ?
              $scope.app.fields[$scope.formSettings.name].expanded : {};

            $scope.expanded =  Object.assign({}, fields);
          });
        };

        /**
         * @function buildScope
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Updates the scope after assigning the information from the
         *   response to the scope.
         */
        $scope.buildScope = function() {
          return true;
        };

        /**
         * @function generate
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Forces automatic field generation.
         */
        $scope.generate = function() {
          $scope.flags.generate = { slug: true, tags: true };
        };

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
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Returns the data to send when saving/updating an item.
         */
        $scope.getData = function() {
          var data = angular.extend({}, $scope.item);

          return cleaner.clean(data);
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

          var route = { name: $scope.routes.createItem };

          if (id) {
            route.name   = $scope.routes.getItem;
            route.params = { id: id };
          }

          http.get(route).then(function(response) {
            $scope.data = response.data;

            if (!response.data.item) {
              $scope.data.item = {};
            }

            if (response.data.extra && response.data.extra.formSettings) {
              $scope.formSettings = response.data.extra.formSettings;
            }

            $scope.data.item = angular.extend($scope.item, $scope.data.item);
            $scope.item      = angular.extend({}, response.data.item);
            $scope.configure($scope.data.extra);
            $scope.buildScope();
            $scope.disableFlags('http');
            $scope.incomplete = false;
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
         * @function unsetItemId
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Unsets the item id.
         *
         * @return {Integer} The item id.
         */
        $scope.unsetItemId = function(data) {
          delete data.id;
          return data;
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
         * @function parseData
         * @memberOf RestInnerCtrl
         *
         * @description
         *   description
         *
         * @param {Object} data The data to parse.
         *
         * @return {Object} Parses data before submit.
         */
        $scope.parseData = function(data) {
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
            messenger.post($window.strings.forms.not_valid, 'error');
            $scope.disableFlags('http');

            return false;
          }

          $scope.form.$setPristine(true);
          $scope.flags.http.saving = true;

          var data  = $scope.getData();
          var route = { name: $scope.routes.saveItem };

          // Parses data before save
          data = $scope.parseData(data);

          console.log(data);

          /**
           * Callback executed when subscriber is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags('http');

            if ($scope.routes.redirect && response.status === 201) {
              $scope.flags.http.saving = true;

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

            if ($scope.draftKey !== null) {
              $scope.draftSaved = null;
              webStorage.session.remove($scope.draftKey);
            }
            messenger.post(response.data);
          };

          if ($scope.itemHasId()) {
            route.name   = $scope.routes.updateItem;
            route.params = { id: $scope.getItemId() };

            // When updating created published item, if it does not have start time it will refresh the form
            if ($scope.item.content_status === 1 && !data.starttime) {
              $scope.item.starttime  = $window.moment().format('YYYY-MM-DD HH:mm:ss');
              $scope.item.urldatetime = $window.moment().format('YYYYMMDDHHmmss');
              data.starttime = $scope.item.starttime;
              data.urldatetime = $scope.item.urldatetime;
            }

            http.put(route, data).then(successCb, $scope.errorCb);
            return;
          }

          http.post(route, data).then(successCb, $scope.errorCb);
        };

        /**
         * @function copy
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Copy a new item.
         */
        $scope.copy = function() {
          if ($scope.form.$invalid) {
            messenger.post($window.strings.forms.not_valid, 'error');
            $scope.disableFlags('http');

            return false;
          }

          $scope.form.$setPristine(true);
          $scope.flags.http.saving = true;

          var data  = $scope.getData();

          var route = { name: $scope.routes.saveItem };

          // Parses data before save
          data = $scope.parseData(data);

          data = $scope.parseCopyData(data);

          /**
           * Callback executed when subscriber is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags('http');

            if ($scope.routes.redirect && response.status === 201) {
              $scope.flags.http.saving = true;

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

            if ($scope.draftKey !== null) {
              $scope.draftSaved = null;
              webStorage.session.remove($scope.draftKey);
            }

            messenger.post(response.data);
          };

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
