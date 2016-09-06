(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NewsAgencyListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires $timeout
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires $http
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('NewsAgencyListCtrl', [
      '$controller', '$http', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger',
      function($controller, $http, $uibModal, $scope, $timeout, itemService, routing, messenger) {
        /**
         * The array of imported elements.
         *
         * @type {Array}
         */
        $scope.imported = [];

        /**
         * The current list mode.
         *
         * @type {String}
         */
        $scope.mode = 'list';

        // Initialize the super class and extend it.
        $.extend(this, $controller('OpinionListCtrl', { $scope: $scope }));

        /**
         * @function _import
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Sends a request to import a list of contents.
         *
         * @param {Object} contents The contents to import.
         */
        $scope._import = function(contents) {
          var modal = $uibModal.open({
            templateUrl: 'modal-import-selected',
            controller: 'NewsAgencyModalCtrl',
            resolve: {
              template: function() {
                return {
                  authors:    $scope.extra.authors,
                  categories: $scope.extra.categories,
                  contents:   contents
                };
              },
            }
          });

          modal.result.then(function(response) {
            if (response) {
              if (response.messages) {
                messenger.post(response.messages);
              }

              for (var i = 0; i < contents.length; i++) {
                $scope.imported.push(contents[i].urn);
              }
            }
          });
        };

        /**
         * @function import
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Opens a modal window to import one item.
         *
         * @param {Object} content The content to import.
         */
        $scope.import = function(content) {
          var contents = [];

          // Add related contents
          for (var i = 0; i < content.related.length && i < 2; i++) {
            contents.push($scope.extra.related[content.related[i]]);
          }

          // Add main content
          contents.push(content);

          $scope._import(contents);
        };

        /**
         * @function importSelected
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Opens a modal window to import the selected contents.
         */
        $scope.importSelected = function() {
          var contents = [];

          for (var i = 0; i < $scope.contents.length; i++) {
            var content = $scope.contents[i];

            if ($scope.selected.contents.indexOf(content.id) !== -1) {
              // Add related contents
              for (var j = 0; j < content.import.length; j++) {
                contents.push($scope.extra.related[content.import[j]]);
              }

              contents.push(content);
            }
          }

          $scope._import(contents);
        };

        /**
         * @function preview
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Displays a modal window with the content preview.
         *
         * @param {Object} content The content to preview.
         */
        $scope.preview = function(content) {
          var related = [];

          for (var i = 0; i < content.related.length; i++) {
            related.push($scope.extra.related[content.related[i]]);
          }

          $uibModal.open({
            templateUrl: 'modal-view-content',
            windowClass: 'modal-news-agency-preview',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  content:  content,
                  imported: $scope.imported.indexOf(content.urn) !== -1,
                  related:  related,
                  routing:  routing
                };
              },
              success: function() {
                return function(m) {
                  $scope.import(content);
                  m.close(1);
                };
              }
            }
          });
        };

        /**
         * @function
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Selects an item to show in sidebar.
         *
         * @param {Object} item Item to show in sidebar
         */
        $scope.select = function(item) {
          item.url = routing.generate('backend_ws_news_agency_show_image', {
            source: item.source, id: item.id
          });

          $scope.selected.lastSelected = item;
        };

        /**
         * @function selectAll
         * @memberOf NewsAgencyListCtrl
         *
         * @description
         *   Selects all items in list.
         */
        $scope.selectAll = function() {
          $scope.selected.contents = [];
          $scope.selected.lastSelected = null;

          if ($scope.selected.all) {
            $scope.selected.contents = [];

            for (var i = 0; i < $scope.contents.length; i++) {
              if ($scope.imported.indexOf($scope.contents[i].urn) === -1) {
                $scope.selected.contents.push($scope.contents[i].id);
              }
            }
          }
        };

        // Updates expanded status when contents change
        $scope.$watch('contents', function() {
          $scope.expanded = [];

          for (var i = 0; i < $scope.contents.length; i++) {
            $scope.expanded[i] = false;
          }
        });

        // Updates the list mode when criteria changes.
        $scope.$watch('criteria.type', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          $scope.loading = true;
          $scope.contents = [];

          if (nv === 'photo') {
            $scope.setMode('grid');
          } else {
            $scope.setMode('list');
          }
        });

        // Displays a message with last synchronization only for small devices
        $scope.$watch('extra', function(nv, ov) {
          if (!ov && nv && nv.last_sync) {
            $scope.xsOnly(null, function() {
              messenger.post(nv.last_sync);
            }, null);
          }

          if (ov !== nv && nv.imported) {
            $scope.imported = $scope.imported.concat(nv.imported);
          }
        }, true);

        // Updates selected related contents when selected contents change
        $scope.$watch('selected.contents', function(nv, ov) {
          // Check added
          var added = nv.filter(function(a) {
            return ov.indexOf(a) === -1;
          });

          var deleted = ov.filter(function(a) {
            return nv.indexOf(a) === -1;
          });

          for (var i = 0; i < $scope.contents.length; i++) {
            if (added.indexOf($scope.contents[i].id) !== -1 &&
                (!$scope.contents[i].import ||
                  $scope.contents[i].import.length < 2)) {
              $scope.contents[i].import =
                $scope.contents[i].related.slice(0, 2);
            }

            if (deleted.indexOf($scope.contents[i].id) !== -1) {
              $scope.contents[i].import = [];
            }
          }
        }, true);
    }]);
})();
