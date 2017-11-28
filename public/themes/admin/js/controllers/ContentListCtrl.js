/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('ContentListCtrl', [
  '$http', '$location', '$uibModal', '$scope', '$timeout', '$window', 'http', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'localizer',
  function($http, $location, $uibModal, $scope, $timeout, $window, http, routing, messenger, webStorage, oqlEncoder, localizer) {
    'use strict';

    /**
     * The criteria to search.
     *
     * @type Object
     */
    $scope.criteria = {
      content_type_name: 'article',
      epp: 10,
      in_litter: 0,
      orderBy: { created:  'desc' },
      page: 1
    };

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
    $scope.selected = { all: false, contents: [] };

    /**
     * The search timeout function.
     *
     * @type function
     */
    $scope.tm = null;

    /**
     * Tree options for opinion frontpage.
     *
     * @type {Object}
     */
    $scope.treeOptions = {
      accept: function(sourceScope, targetScope) {
        return targetScope.$modelValue.indexOf(sourceScope.$modelValue) !== -1;
      },
    };

    /**
     * The available elements per page
     *
     * @type {Array}
     */
    $scope.views = [ 10, 25, 50, 100 ];

    $scope.deselectAll = function() {
      $scope.selected.all          = 0;
      $scope.selected.contents     = [];
      $scope.selected.lastSelected = null;
    };

    /**
     * Goes to content edit page.
     *
     * @param int    id    Content id.
     * @param string route Route name.
     */
    $scope.edit = function(id, route) {
      return routing.generate(route, { id: id });
    };

    /**
     * Initializes the content type for the current list.
     *
     * @param string content Content type.
     * @param object filters Object of allowed filters.
     * @param string sortBy  Field name to sort by.
     * @param string route   Route name.
     */
    $scope.init = function(contentName, route) {
      // Add content_type_name if it isn't a list of all content types
      if (contentName !== null && contentName !== 'content') {
        $scope.criteria.content_type_name = contentName;
      }

      // Route for list (required by $watch)
      $scope.route = route;

      $scope.list(route);
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
     * @param {String}  route The route name.
     * @param {Boolean} reset Whether to reset the list.
     */
    $scope.list = function(route, reset) {
      if (!reset && $scope.mode === 'grid') {
        $scope.loadingMore = 1;
      } else {
        $scope.loading  = 1;
        $scope.contents = [];
        $scope.selected = { all: false, contents: [] };
      }

      oqlEncoder.configure({
        placeholder: {
          title: 'title ~ "%[value]%"',
        }
      });

      var oql   = oqlEncoder.getOql($scope.criteria);
      var route = {
        name: $scope.route,
        params:  {
          contentType: $scope.criteria.content_type_name ?
            $scope.criteria.content_type_name : 'content' ,
          oql: oql
        }
      };

      $location.search('oql', oql);

      http.get(route).then(function(response) {
        $scope.total = parseInt(response.data.total);

        if (response.data.hasOwnProperty('extra')) {
          $scope.extra = response.data.extra;
        }

        if (reset || $scope.mode === 'grid') {
          $scope.contents = $scope.contents.concat(response.data.results);
        } else {
          $scope.contents = response.data.results;
        }

        $scope.contents = $scope.getContentsLocalizeTitle();
        $scope.map      = response.data.map;

        // Disable spinner
        $scope.loading     = 0;
        $scope.loadingMore = 0;
      }, function () {
        $scope.loading     = 0;
        $scope.loadingMore = 0;

        var params = {
          id: new Date().getTime(),
          message: 'Error while fetching data from backend',
          type: 'error'
        };

        messenger.post(params);
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

      $http.post(url, { positions: ids }).success(function(response) {
        messenger.post(response.messages);
      }).error(function() {});
    };

    $scope.saveOpinionsFrontpage = function() {
      var ids = { director: [], editorial: [], opinions: [] };

      for (var name in ids) {
        for (var i = 0; i < $scope[name].length; i++) {
          ids[name].push($scope[name][i].id);
        }
      }

      var url = routing.generate('backend_ws_opinions_save_frontpage');

      $http.post(url, { positions: ids }).success(function(response) {
        messenger.post(response.messages);
      }).error(function() {});
    };

    $scope.scroll = function() {
      if ($scope.total === $scope.contents.length) {
        return false;
      }

      $scope.criteria.page++;
    };

    /**
     * Saves the last selected item description.
     */
    $scope.saveDescription = function() {
      $scope.saving = true;

      var data = { description: $scope.selected.lastSelected.description };
      var url  = routing.generate(
        'backend_ws_picker_save_description',
        { id: $scope.selected.lastSelected.id }
      );

      $http.post(url, data).then(function(response) {
        $scope.saving = false;
        $scope.saved = true;

        if (response.status === 200) {
          $timeout(function() {
            $scope.saved = false;
          }, 2000);

          return true;
        }

        if (response.status !== 200) {
          $scope.saved = false;
          $scope.error = true;

          $timeout(function() {
            $scope.error = false;
          }, 2000);

          return false;
        }
      });
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
        $scope.selected.lastSelected = null;
      }
    };

    /**
     * Changes the list mode.
     *
     * @param {String} mode The new list mode.
     */
    $scope.setMode = function(mode) {
      if ($scope.mode === mode) {
        return;
      }

      $scope.mode = mode;

      if (mode !== 'grid') {
        return;
      }

      var maxHeight = $(window).height() - $('.header').height() -
        $('.actions-navbar').height();
      var maxWidth = $(window).width() - $('.sidebar').width();

      if ($('.content-wrapper').length > 0) {
        maxWidth -=parseInt($('.content-wrapper').css('padding-right'));
      }

      var height = $('.infinite-col').width() + 15;
      var width = $('.infinite-col').width() + 15;


      var rows = Math.ceil(maxHeight / height);
      var cols = Math.floor(maxWidth / width);

      if (rows === 0) {
        rows = 1;
      }

      if (cols === 0) {
        cols = 1;
      }

      if ($scope.criteria.epp !== rows * cols) {
        $scope.contents = [];
      }

      $scope.criteria.epp = rows * cols;
    };

    /**
     * Reloads the list on keypress.
     *
     * @param  Object event The even object.
     */
    $scope.searchByKeypress = function(event) {
      if (event.keyCode === 13) {
        if ($scope.criteria.page !== 1) {
          $scope.criteria.page = 1;
        } else {
          $scope.list($scope.route);
        }
      }
    };

    /**
     * Reloads the full page.
     */
    $scope.reloadPage = function() {
      $window.location.reload();
    };

    $scope.select = function(item) {
      $scope.selected.lastSelected = item;
    };

    /**
     * Sort by function
     *
     * @param  String field The field to sort.
     */
    $scope.sort = function(field) {
      if ($scope.sort_by === field) {
        if ($scope.sort_order === 'asc') {
          $scope.sort_order = 'desc';
        } else {
          $scope.sort_order = 'asc';
        }
      } else {
        $scope.sort_by    = field;
        $scope.sort_order = 'asc';
      }

      $scope.list($scope.route);
    };

    /**
     * Selects/deselects an item.
     *
     * @param {Integer} id The item id.
     */
    $scope.toggle = function(content) {
      if ($scope.selected.contents.indexOf(content.id) === -1) {
        if ($scope.selected.contents.length < 5) {
          $scope.selected.contents.push(content.id);
          $scope.selected.lastSelected = content;
        }
      } else {
        $scope.selected.lastSelected = null;

        $scope.selected.contents.splice(
            $scope.selected.contents.indexOf(content.id), 1);
      }
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
        messenger.post(response.messages);

        if (reload) {
          $scope.list($scope.route);
        }
      }).error(function(response) {
        contents[index][loading] = 0;
        messenger.post(response.messages);
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

      var modal = $uibModal.open({
        templateUrl: 'modal-update-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              checkPhone:  $scope.checkPhone,
              checkVat:    $scope.checkVat,
              extra:       $scope.extra,
              name:        name,
              saveBilling: $scope.saveBilling,
              selected:    $scope.selected,
              value:       value
            };
          },
          success: function() {
            return function() {
              // Load shared variable
              var selected = $scope.selected.contents;

              $scope.updateItemsStatus(loading, 1);

              var url = routing.generate(route,
                { contentType: $scope.criteria.content_type_name });

              return $http.post(url, { ids: selected, value: value });
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

        $scope.selected.contents = [];
        $scope.selected.all = false;
      });
    };

    /**
     * Permanently removes a keyword by using a confirmation dialog
     */
    $scope.deleteKeyword = function(content) {
      var modal = $uibModal.open({
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
        messenger.post(response.data.messages);

        if (response.success) {
          $scope.list($scope.route);
        }
      });
    };

    /**
     * Permanently removes a list of keywords by using a confirmation dialog
     */
    $scope.deleteSelected = function (route) {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $uibModal.open({
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
        messenger.post(response.data);

        if (response.success) {
          $scope.selected = { all: false, contents: [] };
          $scope.list($scope.route);
        }
      });
    };

    /**
     * Permanently removes a keyword by using a confirmation dialog
     */
    $scope.delete = function(content, route) {
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
              var url = routing.generate(route, { id: content.id });

              return $http.post(url);
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
     * Permanently removes a menu by using a confirmation dialog
     */
    $scope.deleteConfig = function(url) {
      var modal = $uibModal.open({
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
     * Permanently removes a list of keywords by using a confirmation dialog
     */
    $scope.deleteSelectedKeywords = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $uibModal.open({
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
        messenger.post(response.data.messages);

        $scope.selected.total = 0;
        $scope.selected.contents = [];

        if (response.success) {
          $scope.list($scope.route);
        }
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
        messenger.post(response.data);

        if (response.success) {
          $scope.list($scope.route, true);
        }
      });
    };

    /**
     * Sends a list of selected contents to trash by using a confirmation dialog
     */
    $scope.sendToTrashSelected = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $uibModal.open({
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
        messenger.post(response.data);

        if (response.success) {
          $scope.selected = { all: false, contents: [] };
          $scope.list($scope.route, true);
        }
      });
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
    };

    /**
     * Reloads the image list on media picker close event.
     */
    $scope.$on('MediaPicker.close', function() {
      if ($scope.criteria.content_type_name === 'photo') {
        $scope.list($scope.route, true);
      }
    });

    // Reloads the list when criteria changes
    $scope.$watch('criteria', function(nv, ov) {
      if (nv === ov) {
        return;
      }

      var changes = [];

      // Get which values change ignoring page
      for (var key in $scope.criteria) {
        if (key !== 'page' && !angular.equals(nv[key], ov[key])) {
          changes.push(key);
        }
      }

      // Reset the list if search changes
      var reset = changes.length > 0;

      // Change page when scrolling in grid mode
      if ($scope.tm) {
        $timeout.cancel($scope.tm);
      }

      if (ov.page === nv.page) {
        $scope.criteria.page = 1;
      }

      $scope.tm = $timeout(function() {
        $scope.list($scope.route, reset);
      }, 500);
    }, true);

    // Change page when scrolling in grid mode
    $(window).scroll(function() {
      if (!$scope.mode || $scope.mode === 'list' ||
          $scope.contents.length === $scope.total) {
        return;
      }

      if (!$scope.loadingMore && $(document).height() ===
          $(window).height() + $(window).scrollTop()) {
        $scope.criteria.page++;
        $scope.$apply();
      }
    });

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
  }
]);
