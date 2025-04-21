(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AdvertisementListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires routing
     * @requires $location
     * @requires http
     * @requires messenger
     * @requires localizer
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('AdvertisementListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'routing', '$location', 'http', 'messenger', 'localizer', '$uibModal',
      function($controller, $scope, oqlEncoder, routing, $location, http, messenger, localizer, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search
         */
        $scope.criteria = {
          content_type_name: 'advertisement',
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
        };

        /**
         * The temporary criteria to search
         */
        $scope.tempCriteria = {
          content_type_name: 'advertisement',
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
        };

        /**
         * The list of elements
         */
        $scope.items = [];

        /**
         * @memberOf AdvertisementListCtrl
         * @description
         *  The list of routes for the controller.
         * @type {Object}
         */
        $scope.routes = {
          getList: 'backend_ws_advertisements_list',
          patchItem: 'backend_ws_content_set_content_status',
          patchList: 'backend_ws_contents_batch_set_content_status',
          deleteItem: 'backend_ws_content_send_to_trash',
          deleteList: 'backend_ws_contents_batch_send_to_trash',
        };

        /**
         * @memberOf AdvertisementListCtrl
         * @description
         *  Initialize the criteria for the filter
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"',
              fk_content_categories: 'fk_content_categories regexp' +
                '"^[value]($|,)|,[value],|(^|,)[value]$"',
              starttime: 'starttime >= "[value]"',
              endtime: 'endtime <= "[value]"',
            }
          });

          $scope.list();
        };

        /**
         * @function list
         * @memberOf AdvertisementListCtrl
         *
         * @description
         *  Get the list of advertisement
         */
        $scope.list = function() {
          if (!$scope.isModeSupported() || $scope.app.mode === 'list') {
            $scope.flags.http.loading = 1;
          } else {
            $scope.flags.http.loadingMore = 1;
          }

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data  = response.data;
            $scope.total = parseInt(response.data.total);

            if ($scope.mode === 'grid') {
              $scope.contents = $scope.contents.concat(response.data.items);
            } else {
              $scope.contents = response.data.items;
            }

            $scope.items = $scope.getContentsLocalizeTitle($scope.contents);
            $scope.map   = response.data.map;
            $scope.categories = response.data.categories;

            $scope.parseList(response.data);
            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.data  = {};
            $scope.items = [];
          });
        };

        /**
         * Updates an item.
         *
         * @param int    index   Index of the item to update in contents.
         * @param int    id      Id of the item to update.
         * @param string route   Route name.
         * @param string name    Name of the property to update.
         * @param mixed  value   New value.
         * @param string loading Name of the property used to show work-in-progress.
         */
        $scope.patchItem = function(index, id, route, name, value, loading, reload) {
          // Load shared variable
          var contents = $scope.contents;

          // Enable spinner
          contents[index][loading] = 1;

          var route = {
            name: $scope.routes.patchItem,
            params: {
              contentType: 'advertisement',
              id: id
            }
          };

          http.post(route, { value: value }).then(function(response) {
            contents[index][loading] = 0;
            contents[index][name] = response.data[name];
            messenger.post(response.data.messages);

            if (reload) {
              $scope.list($scope.route);
            }
          }, function(response) {
            contents[index][loading] = 0;
            messenger.post(response.data.messages);
          });

          // Updated shared variable
          $scope.contents = contents;
        };

        /**
         *  Localize all titles of the contents
         */
        $scope.getContentsLocalizeTitle = function() {
          if (!$scope.extra || !$scope.extra.options) {
            return $scope.contents;
          }

          var lz   = localizer.get($scope.extra.options);
          var keys = [ 'title' ];

          return lz.localize($scope.contents, keys, $scope.extra.options.default);
        };

        /**
         * Sends a content to trash by using a confirmation dialog
         *
         * @param mixed content The content to send to trash.
         */
        $scope.sendToTrash = function(content) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  content: content
                };
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.deleteItem,
                    params: {
                      contentType: 'advertisement',
                      id: content.id
                    }
                  };

                  return http.post(route);
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list($scope.route, true);
            }
          });
        };

        /**
         * Sends a list of selected contents to trash by using a confirmation dialog
         */
        $scope.sendToTrashSelected = function() {
          // Enable spinner
          $scope.deleting = 1;

          var modal = $uibModal.open({
            templateUrl: 'modal-delete-selected',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  selected: $scope.selected
                };
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.deleteList,
                    params: {
                      contentType: 'advertisement'
                    }
                  };

                  return http.post(route, { ids: $scope.selected.items });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.selected = { all: false, items: [] };
              $scope.list($scope.route, true);
            }
          }).catch(function(error) {
            $scope.deleting = 0;
            messenger.post({
              type: 'error',
              message: error.message || 'Failed to delete selected items'
            });
          });
        };

        /**
         *  @ngdoc method
         *  @name updateSelectedItems
         *  @methodOf AdvertisementListCtrl
         *
         *  @description
         *  Opens a confirmation modal to update a list of selected items.
         *  Sends a POST request with the selected item IDs and a value to apply.
         *  Displays success or error messages based on the response.
         *
         *  @param {string} name - The property name to update (e.g., 'status', 'category').
         *  @param {*} value - The new value to assign to the selected items.
         *
         *  @example
         *  $scope.updateSelectedItems('content_status', 1);
         */
        $scope.updateSelectedItems = function(name, value) {
          $scope.updating = 1;

          const selectedItems = angular.copy($scope.selected.items);

          if (!selectedItems.length) {
            $scope.updating = 0;
            return messenger.post({
              type: 'warning',
              message: 'No items selected.'
            });
          }

          var modal = $uibModal.open({
            templateUrl: 'modal-update-selected',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { name: name, value: value };
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.patchList,
                    params: { contentType: 'advertisement' }
                  };

                  return http.post(route, {
                    ids: selectedItems,
                    value: value
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            $scope.updating = 0;

            if (!response) {
              return;
            }

            messenger.post(response.data.messages || []);

            if (response.success) {
              $scope.selected = { all: false, items: [] };
              $scope.list($scope.route, true);
            }
          }).catch(function(error) {
            $scope.updating = 0;

            messenger.post({
              type: 'error',
              message: error.message || 'Failed to update selected items.'
            });
          });
        };

        /**
         * @fucntion applyFilter
         * @memberOf AdvertisementListCtrl
         *
         * @description
         *  Apply the filter to the list of advertisement
         */
        $scope.applyFilter = function() {
          $scope.criteria = angular.copy($scope.tempCriteria);
        };

        /**
         * @function cancelFilter
         * @memberOf AdvertisementListCtrl
         *
         * @description
         *   Cancel the filter and reset the criteria.
         */
        $scope.cancelFilter = function() {
          $scope.tempCriteria.starttime = null;
          $scope.tempCriteria.endtime = null;

          $scope.criteria = angular.copy($scope.tempCriteria);
        };

        /**
         * @function getCategoryTitle
         * @memberof AdvertisementListCtrl
         *
         * @description
         *  Get CategoryTitle by CategoryId
         */
        $scope.getCategoryTitle = function(categoryId) {
          var id = parseInt(Array.isArray(categoryId) ? categoryId[0] : categoryId, 10);

          var match = $scope.categories.find(function(cat) {
            return cat.id === id;
          });

          return match ? match.title : null;
        };

        /**
         * Initializes the adblock detection tool with specific configurations.
         * @returns {void}
         */
        var fuckAdBlock = new FuckAdBlock({
          debug: false,
          checkOnLoad: true,
          resetOnEnd: true
        });

        /**
         * Handles the event when an adblocker is detected.
         * Opens a modal dialog to inform the user about the detected adblocker.
         * @returns {void}
         */
        fuckAdBlock.onDetected(function() {
          /**
           * Opens a modal when an adblocker is detected.
           * @returns {Object} The modal instance.
           */
          $uibModal.open({
            templateUrl: 'modal-adblock',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return null;
              },
              success: function() {
                return null;
              }
            }
          });
        });
      }
    ]);
})();
