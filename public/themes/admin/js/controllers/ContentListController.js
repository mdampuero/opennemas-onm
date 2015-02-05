/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('ContentListController', [
  '$http', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'queryManager',
  function($http, $modal, $scope, $timeout, itemService, routing, messenger, webStorage, oqlEncoder, queryManager) {

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
      epp: 25,
      page: 1,
      total: 0
    }

    /**
     * Default join operator for filters.
     *
     * @type string
     */
    $scope.union = 'AND';

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
    }

    /**
     * Initializes the content type for the current list.
     *
     * @param string content Content type.
     * @param object filters Object of allowed filters.
     * @param string sortBy  Field name to sort by.
     * @param string route   Route name.
     */
    $scope.init = function(content_name, filters, sortBy, sortOrder, route, lang, epp) {
      // Filters used in GUI
      $scope.criteria = filters;

      if (epp) {
        $scope.pagination.epp = epp;
      }

      // Add content_type_name if it isn't a list of all content types
      if (content_name != null && content_name != 'content') {
        $scope.criteria.content_type_name = content_name;
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
    }

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
      while (i < $scope.orderBy.length && $scope.orderBy[i].name != name) {
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
      return $scope.selected.contents.indexOf(id) != -1;
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

      var processed_filters = oqlEncoder.encode($scope.criteria);
      var filtersToEncode = angular.copy($scope.criteria);

      delete filtersToEncode.content_type_name;

      queryManager.setParams(filtersToEncode, $scope.orderBy,
        $scope.pagination.epp, $scope.pagination.page);

      var postData = {
        elements_per_page: $scope.pagination.epp,
        page: $scope.pagination.page,
        sort_by: $scope.orderBy.name,
        sort_order: $scope.orderBy.value,
        search: processed_filters
      }
      $scope.postData = postData;

      $http.post(url, postData).then(function(response) {
        $scope.pagination.total = parseInt(response.data.total);
        $scope.contents         = response.data.results;
        $scope.map              = response.data.map;

        if (response.data.hasOwnProperty('extra')) {
          $scope.extra = response.data.extra
        };

        // Disable spinner
        $scope.loading = 0;
      })
    }

    /**
     * Opens new modal window when clicking delete button.
     *
     * @param  string template Template id.
     * @param  string route    Route name.
     * @param  int    index    Index of the selected content.
     */
    $scope.open = function(template, route, index) {
      $modal.open({
        templateUrl: template,
        controller: 'ContentModalCtrl',
        resolve: {
          index: function() {
            return index;
          },
          route: function() {
            return route;
          }
        }
      });
    }

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
      };

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
        };
      }).error(function(response) {});
    }

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
      if (page != $scope.pagination.page) {
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
      if (event.keyCode == 13) {
        if ($scope.pagination.page != 1) {
          $scope.pagination.page = 1;
        } else {
          $scope.list($scope.route);
        }
      }
    };

    $scope.sort = function(field) {
      if ($scope.sort_by == field) {
        if ($scope.sort_order == 'asc') {
          $scope.sort_order = 'desc';
        } else {
          $scope.sort_order = 'asc';
        }
      } else {
        $scope.sort_by = field;
        $scope.sort_order == 'asc';
      }

      $scope.list($scope.route);
    }

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
    $scope.updateItem = function(index, id, route, name, value, loading) {
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

      $http.post(url, {
        value: value
      }).success(function(response) {
        if (response[name] != null) {
          contents[index][name] = response[name];
        }

        for (var i = 0; i < response.messages.length; i++) {
          var params = {
            id: new Date().getTime() + '_' + response.messages[i].id,
            message: response.messages[i].message,
            type: response.messages[i].type
          };

          messenger.post(params);
        };

        // Disable spinner
        contents[index][loading] = 0;
      }).error(function(response) {
        // Disable spinner
        contents[index][loading] = 0;
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
      // Load shared variable
      var contents = $scope.contents;
      var selected = $scope.selected;

      updateItemsStatus(loading, 1);

      var url = routing.generate(
        route, {
          contentType: $scope.criteria.content_type_name
        }
      );
      $http.post(url, {
        ids: selected,
        value: value
      })
        .success(function(response) {
          updateItemsStatus(loading, 0, name, value);

          for (var i = 0; i < response.messages.length; i++) {
            var params = {
              id: new Date().getTime() + '_' + response.messages[i].id,
              message: response.messages[i].message,
              type: response.messages[i].type
            };

            messenger.post(params);
          };
        }).error(function(response) {

        });
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
      var selected = $scope.selected;

      for (var i = 0; i < selected.length; i++) {
        var j = 0;

        while (j < contents.length && contents[j].id != selected[i]) {
          j++;
        }

        if (j < contents.length) {
          contents[j][loading] = status
          contents[j][name] = value;
        }
      };

      // Updated shared variable
      sharedVars.set('contents', contents);
      sharedVars.set('selected', selected);
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
      var selected = $scope.selected;

      for (var i = 0; i < selected.length; i++) {
        var j = 0;
        while (j < contents.length && contents[j].id != selected[i]) {
          j++;
        }

        if (j < contents.length) {
          contents[j].status = status;
          contents[j].loading = loading
        }
      };

      // Updated shared variable
      $scope.contents = contents;
      $scope.selected = selected;
    }

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

    /**
     * Load the value of elements per page variable in scope when it changes.
     *
     * @param  Event  event Event object.
     * @param  Object vars  Shared variables object.
     */
    $scope.$watch('$scope.pagination.epp', function(newValues, oldValues) {
      if (searchTimeout) {
        $timeout.cancel(searchTimeout);
      }

      if (newValues !== oldValues) {
        // $location.search('elements_per_page', newValues);

        $scope.pagination.page = 1;
        // $location.search('page', null);

        searchTimeout = $timeout(function() {
          $scope.list($scope.route);
        }, 500);
      }
    }, true);
  }
]);
