(function() {
  'use strict';

  // Define the module and controller for the widget edit modal
  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ModalWidgetEditCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires $timeout
     * @requires fullUrl
     * @requires $compile
     * @requires http
     * @requires id
     *
     * @description
     *   Controller for the widget edit modal.
     */
    .controller('ModalWidgetEditCtrl', [
      '$controller', '$scope', 'id', '$timeout', '$uibModalInstance', '$window',
      'cleaner', 'http', 'messenger', 'routing', 'webStorage',
      function($controller, $scope, id, $timeout, $uibModalInstance, $window,
          cleaner, http, messenger, routing, webStorage) {
        // Extend the controller with WidgetCtrl
        $.extend(this, $controller('WidgetCtrl', { $scope: $scope }));

        $scope.id = id;
        $scope.getItem(id);

        /**
         * Builds the scope for the controller, transforming item params.
         */
        $scope.buildScope = function() {
          var params = [];

          // Iterate through item params and transform values
          for (var key in $scope.item.params) {
            // eslint-disable-next-line no-new-wrappers
            var value = new Number($scope.item.params[key]);

            if ($scope.item.params[key] === '') {
              value = '';
            }

            value = value.toString() === 'NaN' ? $scope.item.params[key] : value.valueOf();

            params.push({ name: key, value: value });
          }

          $scope.item.params = params;

          // Handle multi-language body if applicable
          if ($scope.displayMultiBody()) {
            $scope.language = $scope.data.extra.locale.selected || null;
            if (typeof $scope.item.body !== 'object') {
              var bodyValue = $scope.item.body;

              $scope.item.body = {};
              $scope.item.body[$scope.language] = bodyValue;
            }
          }
        };

        /**
         * Saves a new or updated item.
         */
        $scope.save = function() {
          $scope.flags.http.saving = true;

          var data  = $scope.getData();
          var route = { name: $scope.routes.saveItem };

          // Parse data before saving
          data = $scope.parseData(data);

          /**
           * Callback executed when the item is saved/updated successfully.
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

            if ($scope.dtm) {
              $timeout.cancel($scope.dtm);
            }
          };

          // Update item if it has an ID, otherwise create a new item
          if ($scope.itemHasId()) {
            route.name   = $scope.routes.updateItem;
            route.params = { id: $scope.getItemId() };

            // Set start time if updating a published item without start time
            if ($scope.item.content_status === 1 && !data.starttime) {
              $scope.item.starttime  = $window.moment().format('YYYY-MM-DD HH:mm:ss');
              $scope.item.urldatetime = $window.moment().format('YYYYMMDDHHmmss');
              data.starttime = $scope.item.starttime;
              data.urldatetime = $scope.item.urldatetime;
            }

            http.put(route, data).then(successCb, $scope.errorCb);
            location.reload();
            return;
          }

          http.post(route, data).then(successCb, $scope.errorCb);
        };

        // Close the modal without saving changes
        $scope.close = function() {
          $uibModalInstance.dismiss('cancel');
        };
      }
    ]);
})();
