/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('ContentListCtrl', [
  '$http', '$modal', '$scope', '$timeout', '$window', 'itemService', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'queryManager',
  function($http, $modal, $scope, $timeout, $window, itemService, routing, messenger, webStorage, oqlEncoder, queryManager) {
    'use strict';

    /**
     * The criteria to search.
     *
     * @type Object
     */
    $scope.criteria = {};

    /**
     * The list of elements.
     *
     * @type Object
     */
    $scope.contents = [];

    /**
     * The list of selected elements.
     *
     * @type array
     */
    $scope.selected = {
      all: false,
      contents: []
    };

    /**
     * The listing order.
     *
     * @type Object
     */
    $scope.orderBy = [{
      name: 'created',
      value: 'desc'
    }];

    /**
     * The current pagination status.
     *
     * @type Object
     */
    $scope.pagination = {
      epp: 10,
      page: 1,
      total: 0
    };

    /**
     * Tree options for opinion frontpage.
     *
     * @type {Object}
     */
    $scope.treeOptions = {
      accept: function(sourceNodeScope, destNodesScope, destIndex) {
        return destNodesScope.$modelValue.indexOf(sourceNodeScope.$modelValue) !== -1;
      },
    };

    /**
     * The available elements per page
     *
     * @type {Array}
     */
    $scope.views = [ 10, 25, 50, 100 ];

    /**
     * Default join operator for filters.
     *
     * @type string
     */
    $scope.union = 'AND';

    $scope.deselectAll = function() {
      $scope.selected.contents = [];
      $scope.selected.all = 0
    };

    /**
     * Goes to content edit page.
     *
     * @param int    id    Content id.
     * @param string route Route name.
     */
    $scope.edit = function(id, route) {
      return routing.generate(route, {
        id: id
      });
    };

    /**
     * Initializes the content type for the current list.
     *
     * @param string content Content type.
     * @param object filters Object of allowed filters.
     * @param string sortBy  Field name to sort by.
     * @param string route   Route name.
     */
    $scope.init = function(contentName, filters, sortBy, sortOrder, route, lang, epp) {
      // Filters used in GUI
      $scope.criteria = filters;

      console.log(epp, angular.isUndefined(epp));
      if (!angular.isUndefined(epp)) {
        $scope.pagination.epp = epp;
      }
      console.log($scope.pagination.epp);

      // Add content_type_name if it isn't a list of all content types
      if (contentName !== null && contentName !== 'content'
      ) {
        $scope.criteria.content_type_name = contentName;
      }

      // Set sortBy
      if (sortBy != null) {
        $scope.orderBy.name = sortBy;
      }

      // Set sortOrder
      if (sortOrder != null) {
        $scope.orderBy.value = sortOrder;
      }

      // Initialize filters from URL
      var queryParams = queryManager.getParams();

      for (var name in queryParams.criteria) {
        $scope.criteria[name] = queryParams.criteria[name];
      }

      // Set sortBy
      if (queryParams.order != null) {
        $scope.orderBy = queryParams.order;
      }

      if (queryParams.epp) {
        $scope.pagination.epp = queryParams.epp;
      }

      if (queryParams.page) {
        $scope.pagination.page = queryParams.page;
      }

      // Route for list (required by $watch)
      $scope.route = route;

      $scope.list(route);
    };

    /**
     * Checks if the listing is ordered by the given field name.
     *
     * @param string name The field name.
     *
     * @return mixed The order value, if the order exists. Otherwise,
     *               returns false.
     */
    $scope.isOrderedBy = function(name) {
      var i = 0;
      while (i < $scope.orderBy.length && $scope.orderBy[i].name !== name) {
        i++;
      }

      if (i < $scope.orderBy.length) {
        return $scope.orderBy[i].value;
      }

      return false;
    };

    /**
     * Checks if an instance is selected.
     *
     * @param string id The group id.
     */
    $scope.isSelected = function(id) {
      return $scope.selected.contents.indexOf(id) !== -1;
    };

    /**
     * Updates the array of contents.
     *
     * @param string route Route name.
     */
    $scope.list = function(route) {
      // Enable spinner
      $scope.loading = 1;

      var url = routing.generate(route, {
        contentType: $scope.criteria.content_type_name
      });

      var processedFilters = oqlEncoder.encode($scope.criteria);
      var filtersToEncode = angular.copy($scope.criteria);

      delete filtersToEncode.content_type_name;

      queryManager.setParams(filtersToEncode, $scope.orderBy,
        $scope.pagination.epp, $scope.pagination.page);

      var postData = {
        elements_per_page: $scope.pagination.epp,
        page: $scope.pagination.page,
        sort_by: $scope.orderBy.name,
        sort_order: $scope.orderBy.value,
        search: processedFilters
      };

      $scope.postData = postData;

      $http.post(url, postData).then(function(response) {
        $scope.pagination.total = parseInt(response.data.total);
        $scope.contents         = response.data.results;
        $scope.map              = response.data.map;

        if (response.data.hasOwnProperty('extra')) {
          $scope.extra = response.data.extra;
        }

        // Disable spinner
        $scope.loading = 0;
      });
    };

    /**
     * Saves the content positions in widget.
     *
     * @param string route Route name.
     */
    $scope.savePositions = function(route) {
      // Load shared variable
      var contents = $scope.contents;
      var ids = [];

      for (var i = 0; i < contents.length; i++) {
        ids.push(contents[i].id);
      }

      var url = routing.generate(
        route, {
          contentType: $scope.criteria.content_type_name
        }
      );

      $http.post(url, {
        positions: ids
      }).success(function(response) {
        if (response.content_status != null) {
          contents[index].content_status = response.content_status;
        }

        for (var i = 0; i < response.messages.length; i++) {
          var params = {
            id: new Date().getTime() + '_' + response.messages[i].id,
            message: response.messages[i].message,
            type: response.messages[i].type
          };

          messenger.post(params);
        }
      }).error(function(response) {});
    };

    $scope.saveOpinionsFrontpage = function(route) {
      var ids = { director: [], editorial: [], opinions: [] };

      for (var name in ids) {
        for (var i = 0; i < $scope[name].length; i++) {
          ids[name].push($scope[name][i].id);
        };
      }

      var url = routing.generate('backend_ws_opinions_save_frontpage');

      $http.post(url, { positions: ids }).success(function(response) {
        $scope.renderMessages(response.messages);
      }).error(function() {});
    };

    /**
     * Selects/unselects all instances.
     */
    $scope.selectAll = function() {
      if ($scope.selected.all) {
        $scope.selected.contents = $scope.contents.map(function(content) {
          return content.id;
        });
      } else {
        $scope.selected.contents = [];
      }
    };

    /**
     * Changes the page of the list.
     *
     * @param  int page Page number.
     */
    $scope.selectPage = function(page, route) {
      if (page !== $scope.pagination.page) {
        $scope.pagination.page = page;
        // $location.search('page', page);
        $scope.list(route);
      }
    };

    /**
     * Reloads the list on keypress.
     *
     * @param  Object event The even object.
     */
    $scope.searchByKeypress = function(event) {
      if (event.keyCode === 13) {
        if ($scope.pagination.page !== 1) {
          $scope.pagination.page = 1;
        } else {
          $scope.list($scope.route);
        }
      }
    };

    $scope.sort = function(field) {
      if ($scope.sort_by === field) {
        if ($scope.sort_order === 'asc') {
          $scope.sort_order = 'desc';
        } else {
          $scope.sort_order = 'asc';
        }
      } else {
        $scope.sort_by = field;
        $scope.sort_order === 'asc';
      }

      $scope.list($scope.route);
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
      var contents = $scope.contents;

      // Enable spinner
      contents[index][loading] = 1;

      var url = routing.generate(
        route, {
          contentType: $scope.criteria.content_type_name,
          id: id
        }
      );

      $http.post(url, { value: value }).success(function(response) {
        contents[index][loading] = 0;
        contents[index][name] = response[name];
        $scope.renderMessages(response.messages);

        if (reload) {
          $scope.list($scope.route);
        }
      }).error(function(response) {
        contents[index][loading] = 0;
        $scope.renderMessages(response.messages);
      });

      // Updated shared variable
      $scope.contents = contents;
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

      var modal = $modal.open({
        templateUrl: 'modal-update-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              name:     name,
              value:    value,
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              // Load shared variable
              var selected = $scope.selected.contents;

              updateItemsStatus(loading, 1);

              var url = routing.generate(route,
                { contentType: $scope.criteria.content_type_name });

              return $http.post(url, { ids: selected, value: value });
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            updateItemsStatus(loading, 0, name, value);
          }
        }
      });
    };

    /**
     * Permanently removes a keyword by using a confirmation dialog
     */
    $scope.deleteKeyword = function(content) {
      var modal = $modal.open({
        templateUrl: 'modal-remove-permanently',
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
              var url = routing.generate(
                'backend_ws_keyword_delete',
                { id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a list of keywords by using a confirmation dialog
     */
    $scope.deleteSelected = function (route) {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $modal.open({
        templateUrl: 'modal-delete-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate(route);

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a keyword by using a confirmation dialog
     */
    $scope.delete = function(content, route) {
      var modal = $modal.open({
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
              var url = routing.generate(route, { id: content.id });

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a menu by using a confirmation dialog
     */
    $scope.deleteConfig = function(url) {
      var modal = $modal.open({
        templateUrl: 'modal-remove-config',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {};
          },
          success: function() {
            return null;
          }
        }
      });

      modal.result.then(function() {
        $window.location.href = url;
      });
    };

    /**
     * Permanently removes a menu by using a confirmation dialog
     */
    $scope.removeMenu = function(content) {
      var modal = $modal.open({
        templateUrl: 'modal-remove-permanently',
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
              var url = routing.generate(
                'backend_ws_menu_delete',
                { id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a list of keywords by using a confirmation dialog
     */
    $scope.deleteSelectedKeywords = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $modal.open({
        templateUrl: 'modal-batch-remove-permanently',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate('backend_ws_keywords_batch_delete');

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a list of contents by using a confirmation dialog
     */
    $scope.removeSelectedMenus = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $modal.open({
        templateUrl: 'modal-batch-remove-permanently',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate('backend_ws_menus_batch_delete');

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a contents by using a confirmation dialog
     */
    $scope.removePermanently = function(content) {
      var modal = $modal.open({
        templateUrl: 'modal-remove-permanently',
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
              var url = routing.generate(
                'backend_ws_content_remove_permanently',
                { contentType: content.content_type_name, id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Permanently removes a list of contents by using a confirmation dialog
     */
    $scope.removePermanentlySelected = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $modal.open({
        templateUrl: 'modal-batch-remove-permanently',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate(
                'backend_ws_contents_batch_remove_permanently',
                { contentType: 'content' }
              );

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Takes out of trash a content by using a confirmation dialog
     */
    $scope.restoreFromTrash = function(content) {
      var modal = $modal.open({
        templateUrl: 'modal-restore-from-trash',
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
              var url = routing.generate(
                'backend_ws_content_restore_from_trash',
                { contentType: content.content_type_name, id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Takes out of trash a list of contents by using a confirmation dialog
     */
    $scope.restoreFromTrashSelected = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $modal.open({
        templateUrl: 'modal-batch-restore',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate(
                'backend_ws_contents_batch_restore_from_trash',
                { contentType: 'content' }
              );

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Sends a content to trash by using a confirmation dialog
     *
     * @param mixed content The content to send to trash.
     */
    $scope.sendToTrash = function(content) {
      var modal = $modal.open({
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
              var url = routing.generate(
                'backend_ws_content_send_to_trash',
                { contentType: content.content_type_name, id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
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

      var modal = $modal.open({
        templateUrl: 'modal-delete-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate(
                'backend_ws_contents_batch_send_to_trash',
                { contentType: $scope.criteria.content_type_name }
              );

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Returns a number of pages for the total amount of contents
     */
    $scope.getNumberOfPages = function() {
      return Math.ceil($scope.pagination.total / $scope.pagination.epp);
    };

    /**
     * Moves the list to the previous page
     */
    $scope.goToPrevPage = function() {
      $scope.pagination.page = $scope.pagination.page - 1;

      return $scope.pagination.page;
    };

    /**
     * Moves the list to the next page
     */
    $scope.goToNextPage = function() {
      $scope.pagination.page = $scope.pagination.page + 1;

      return $scope.pagination.page;
    };

    /**
     * Checks if the list is in the first page
     */
    $scope.isFirstPage = function() {
      return $scope.pagination.page - 1 < 1;
    };

    /**
     * Checks if the list is in the last pages
     */
    $scope.isLastPage = function() {
      return $scope.pagination.page === $scope.pagination.pages;
    };

    /**
     * Updates selected items current status.
     * @param  string  loading Name of the work-in-progress property.
     * @param  integer status  Current work-in-progress status.
     * @param  string  name    Name of the property to update.
     * @param  mixed   value   Value of the property to update.
     */
    function updateItemsStatus(loading, status, name, value) {
      // Load shared variables
      var contents = $scope.contents;
      var selected = $scope.selected.contents;

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
      $scope.selected.contents = selected;
    }

    /**
     * Updates the status property for selected contents.
     *
     * @param int loading Loading flag to use in template.
     * @param int status  Status value.
     */
    function updateStatus(loading, status) {
      // Load shared variable
      var contents = $scope.contents;
      var selected = $scope.selected.contents;

      for (var i = 0; i < selected.length; i++) {
        var j = 0;
        while (j < contents.length && contents[j].id !== selected[i]) {
          j++;
        }

        if (j < contents.length) {
          contents[j].status = status;
          contents[j].loading = loading;
        }
      }

      // Updated shared variable
      $scope.contents = contents;
      $scope.selected.contents = selected;
    }

    /**
     * Reloads the image list on media picker close event.
     */
    $scope.$on('MediaPicker.close', function() {
      if ($scope.criteria.content_type_name === 'photo') {
        $scope.list($scope.route);
      }
    });

    /**
     * Refresh the list of elements when some parameter changes.
     *
     * @param array newValues The new values
     * @param array oldValues The old values
     */
    $scope.$watch('[orderBy, pagination.epp, pagination.page]', function(newValues, oldValues) {
      if (newValues !== oldValues) {
        $scope.list($scope.route);
      }
    }, true);

    /**
     * Reloads the list when filters change.
     *
     * @param  object newValues New filters values.
     * @param  object oldValues Old filters values.
     */
    var searchTimeout;
    $scope.$watch('criteria', function(newValues, oldValues) {
      if (searchTimeout) {
        $timeout.cancel(searchTimeout);
      }

      if (newValues !== oldValues) {
        $scope.pagination.page = 1;

        searchTimeout = $timeout(function() {
          $scope.list($scope.route);
        }, 500);
      }
    }, true);
  }
]);
