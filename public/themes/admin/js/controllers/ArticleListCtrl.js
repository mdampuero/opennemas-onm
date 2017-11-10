(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('ArticleListCtrl', [
      '$controller', '$location', '$scope', '$timeout','$uibModal', 'http', 'messenger', 'linker', 'localizer', 'oqlEncoder',
      function($controller, $location, $scope, $timeout, $uibModal, http, messenger, linker, localizer, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'article',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: 1
        };

        /**
         * @function groupCategories
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Groups categories in the ui-select.
         *
         * @param {Object} item The category to group.
         *
         * @return {String} The group name.
         */
        $scope.groupCategories = function(item) {
          var category = $scope.categories.filter(function(e) {
            return e.pk_content_category === item.fk_content_category;
          });

          if (category.length > 0 && category[0].pk_content_category) {
            return category[0].title;
          }

          return '';
        };

        /**
         * @function init
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Configures and initializes the list.
         */
        $scope.init = function() {
          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"',
              fk_user_group: '[key] regexp "^[value],|^[value]$|,[value],|,[value]$"'
            }
          });

          $scope.list();
        };

        /**
         * Updates the array of contents.
         */
        $scope.list = function() {
          $scope.loading  = 1;

          var oql = oqlEncoder.getOql($scope.criteria);

          $location.search('oql', oql);

          return http.get({
            name: 'api_v1_backend_articles_list',
            params: { oql: oql }
          }).then(function(response) {
            $scope.loading = 0;
            $scope.data    = response.data;

            // Configure the list
            if ($scope.config.multilanguage === null) {
              $scope.config.multilanguage = response.data.extra.multilanguage;
            }

            if (!$scope.config.linkers.il) {
              $scope.config.linkers.il =
                linker.get(response.data.extra.keys, $scope, false);
            }

            if (!$scope.config.linkers.cl) {
              $scope.config.linkers.cl = linker.get([ 'title' ], $scope, false);
            }

            if ($scope.config.locale === null) {
              $scope.config.locale = response.data.extra.locale;
            }

            // Load items
            $scope.items      = response.data.results;
            $scope.categories = response.data.extra.categories;

            if ($scope.config.multilanguage && $scope.config.locale) {
              var lz = localizer.get({ keys: $scope.data.extra.keys,
                available: $scope.data.extra.options.available });

              $scope.categories = lz.localize($scope.categories,
                [ 'title' ], $scope.config.locale);
              $scope.items = lz.localize($scope.items, $scope.data.extra.keys,
                $scope.config.locale);

              $scope.config.linkers.cl.link(
                $scope.data.extra.categories, $scope.categories);
              $scope.config.linkers.il.link(
                $scope.data.results, $scope.items);
            }

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          }, function() {
            $scope.loading = 0;

            messenger.post({
              id: new Date().getTime(),
              message: 'Error while fetching data from backend',
              type: 'error'
            });
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
        $scope.updateItem = function(index, id, route, name, value, loading, reload) {
          // Load shared variable
          var items = $scope.items;

          // Enable spinner
          items[index][loading] = 1;

          http.post({
            name: route,
            params: { contentType: 'article', id: id }
          }, { value: value }).success(function(response) {
            items[index][loading] = 0;
            items[index][name] = response[name];
            messenger.post(response.messages);

            if (reload) {
              $scope.list($scope.route);
            }
          }).error(function(response) {
            items[index][loading] = 0;
            messenger.post(response.messages);
          });

          // Updated shared variable
          $scope.items = items;
        };

        /**
         * Updates selected items current status.
         * @param  string  loading Name of the work-in-progress property.
         * @param  integer status  Current work-in-progress status.
         * @param  string  name    Name of the property to update.
         * @param  mixed   value   Value of the property to update.
         */
        $scope.updateItemsStatus = function(loading, status, name, value) {
          // Load shared variables
          var contents = $scope.items;
          var selected = $scope.selected.items;

          for (var i = 0; i < selected.length; i++) {
            var j = 0;

            while (j < contents.length && contents[j].id !== selected[i]) {
              j++;
            }

            if (j < contents.length) {
              contents[j][loading] = status;
              contents[j][name] = value;
            }
          }

          // Updated shared variable
          $scope.contents = contents;
          $scope.selected.items = selected;
        };

        /**
         * Updates selected items.
         *
         * @param string route   Route name.
         * @param string name    Name of the property to update.
         * @param mixed  value   New value.
         * @param string loading Name of the property used to show work-in-progress.
         */
        $scope.updateSelectedItems = function(route, name, value, loading) {
          // Enable spinner
          $scope.deleting = 1;
          var selected = {
            all: $scope.selected.contents,
            contents: $scope.selected.items
          };

          var modal = $uibModal.open({
            templateUrl: 'modal-update-selected',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name:        name,
                  selected:    selected,
                  value:       value
                };
              },
              success: function() {
                return function() {
                  // Load shared variable
                  var selected = $scope.selected.items;

                  $scope.updateItemsStatus(loading, 1);

                  return http.post({
                    name: route,
                    params: { contentType: $scope.criteria.content_type_name }
                  }, { ids: selected, value: value });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              messenger.post(response.data.messages);

              if (response.success) {
                $scope.updateItemsStatus(loading, 0, name, value);
              }
            }

            $scope.selected.items = [];
            $scope.selected.all = false;
          });
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
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  content: content
                };
              },
              success: function() {
                return function() {
                  return http.post({
                    name: 'backend_ws_content_send_to_trash',
                    params: { contentType: content.content_type_name, id: content.id }
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              messenger.post(response.data);

              if (response.success) {
                $scope.list($scope.route, true);
              }
            }
          });
        };

        /**
         * Sends a list of selected contents to trash by using a confirmation dialog
         */
        $scope.sendToTrashSelected = function () {
          // Enable spinner
          $scope.deleting = 1;
          var selected = {
            all: $scope.selected.contents,
            contents: $scope.selected.items
          };

          var modal = $uibModal.open({
            templateUrl: 'modal-delete-selected',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  selected: selected
                };
              },
              success: function() {
                return function() {
                  return http.post({
                    name: 'backend_ws_contents_batch_send_to_trash',
                    params: { contentType: $scope.criteria.content_type_name }
                  }, {ids: $scope.selected.items} );
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              messenger.post(response.data);

              if (response.success) {
                $scope.selected = { all: false, items: [] };
                $scope.list();
              }
            }
          });
        };
      }
    ]);
})();
