(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  MenuCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $rootScope
     * @requires $scope
     * @requires routing
     *
     * @description
     *   Handle actions for article inner.
     */
    .controller('MenuCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf MenuCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
        $scope.item = {
          pk_menu:    null,
          name:       '',
          menu_items: [],
          position:   ''
        };

        /**
         * @memberOf MenuCtrl
         *
         * @description
         *  The default link to show in the sidebar.
         *
         * @type {Object}
         */
        $scope.defaultLink = {
          pk_item: null,
          pk_menu: null,
          title: 'External Link',
          type: 'external',
          link_name: '/',
          pk_father: 0,
          position: 0,
        };

        /**
         * @memberOf MenuCtrl
         *
         * @description
         *  The replacements for the name of the property link_name on the dragable items.
         *
         * @type {Object}
         */
        $scope.replacements = {
          internal: {
            link_name: 'link',
          },
          static: {
            link_name: 'slug',
          },
          'blog-category': {
            link_name: 'name'
          },
          category: {
            link_name: 'name'
          }
        };

        /**
         * @memberOf MenuCtrl
         *
         * @description
         *  The object to save the queries.
         *
         * @type {Object}
         */
        $scope.search = {};

        /**
         * @memberOf MenuCtrl
         *
         * @description
         *  The options for ui-tree directive.
         *
         * @type {Object}
         */
        $scope.treeOptions = {
          // Generate a unique pk_item for the new element dropped
          dropped: function(e) {
            if (e.source.cloneModel) {
              e.source.cloneModel.pk_item = ++$scope.last;
            }
          }
        };

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return $scope.config && $scope.config.locale &&
        $scope.config.locale.multilanguage;
        };

        /**
         * @memberOf CommentCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getItem:    'api_v1_backend_menu_get_item',
          list:       'backend_menus_list',
          updateItem: 'api_v1_backend_menu_update_item',
          createItem: 'api_v1_backend_menu_create_item',
          saveItem:   'api_v1_backend_menu_save_item',
          redirect:   'backend_menu_show'
        };

        /**
         * @inheritdoc
         */
        $scope.getItemId = function() {
          return $scope.item.pk_menu;
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
          if ($scope.data.extra.locale) {
            $scope.languageData = angular.copy($scope.data.extra.locale);
            $scope.lang         = $scope.languageData.default;
          }

          $scope.data.extra['blog-category'] = angular.copy($scope.data.extra.category);

          $scope.menuData  = $scope.transformExtraData($scope.data.extra);
          $scope.parents   = $scope.filterParents();
          $scope.childs    = $scope.filterChilds($scope.parents);
          $scope.dragables = $scope.filterDragables($scope.menuData);
          $scope.linkData  = [ Object.assign({}, $scope.defaultLink) ];
          $scope.last      = $scope.getLastIndex($scope.data.item.menu_items);
        };

        /**
         * @param {Object} dragables An object with the arrays of dragable items.
         *
         * @returns The dragable items that are not filtered and are not in the menu.
         */
        $scope.filterDragables = function(dragables) {
          var object = {};

          Object.keys(dragables).forEach(function(type) {
            object[type] = $scope.filterItems($scope.menuData[type]);
          });

          return object;
        };

        /**
         * @param {Object} dragables The menu items of the right block.
         *
         * @returns The items that match with the search string.
         */
        $scope.filterItems = function(dragables) {
          return dragables.filter(function(dragable) {
            var valid = !$scope.search[dragable.type] ||
              dragable.title.toLowerCase().indexOf($scope.search[dragable.type].toLowerCase()) !== -1;

            return valid && !$scope.isAlreadyInMenu(dragable);
          });
        };

        /**
         * Filters the array of menu items to show only the parents.
         *
         * @returns All the menu items that are parents.
         */
        $scope.filterParents = function() {
          return $scope.item.menu_items.filter(function(item) {
            return !item.pk_father;
          });
        };

        /**
         * Filters the array of menu items to show only the childs of the given parent.
         *
         * @param {array} parents The array of parents.
         */
        $scope.filterChilds = function(parents) {
          var childs = {};

          parents.forEach(function(parent) {
            childs[parent.pk_item] = [];
          });

          $scope.item.menu_items.forEach(function(item) {
            if (item.pk_father) {
              childs[item.pk_father].push(item);
            }
          });

          return childs;
        };

        /**
         * @return The data prepared to be saved.
         */
        $scope.getData = function() {
          var menuItems = [];
          var map       = {};

          $scope.parents = $scope.parents.map(function(parent, index) {
            map[index + 1]      = parent.pk_item;
            parent.pk_menu  = $scope.item.pk_menu;
            parent.position = index + 1;
            parent.pk_item  = index + 1;
            menuItems.push(parent);
            return parent;
          });

          var childs = {};

          for (var parent in $scope.parents) {
            childs[parent] = $scope.childs[map[parent]];
          }

          $scope.childs = childs;

          for (var parent in $scope.childs) {
            for (var child in $scope.childs[parent]) {
              var item = $scope.childs[parent][child];

              item.pk_item   = menuItems.length + 1;
              item.pk_father = parent;
              item.pk_menu   = $scope.item.pk_menu;
              item.position  = child;
              menuItems.push(item);
            }
          }

          $scope.item.menu_items = menuItems;
          $scope.data.item       = Object.assign({}, $scope.item);

          return $scope.data.item;
        };

        /**
         * Removes an item from the array of menu items.
         *
         * @param {Object} item The item to remove from the array of menu items.
         */
        $scope.removeItem = function(item) {
          for (var id in $scope.childs) {
            $scope.childs[id] = $scope.childs[id].filter(function(child) {
              return child.pk_item !== item.pk_item;
            });
          }
          delete $scope.childs[item.pk_item];
          $scope.parents = $scope.parents.filter(function(parent) {
            return parent.pk_item !== item.pk_item;
          });
        };

        /**
         * Adapt the extra data to the format of a menu item.
         *
         * @param {array} data The array of the extra data.
         * @returns The extra data unified with menu item.
         */
        $scope.transformExtraData = function(data) {
          var object = {};

          Object.keys($scope.replacements).forEach(function(key) {
            object[key] = [];

            data[key].forEach(function(item) {
              object[key].push({
                pk_item: null,
                pk_menu: null,
                title: item.title,
                type: key,
                link_name: item[$scope.replacements[key].link_name],
                pk_father: 0,
                position: 0,
              });
            });
          });

          object.syncBlogCategory = [];

          Object.keys(data.syncBlogCategory).forEach(function(site) {
            data.syncBlogCategory[site].categories.forEach(function(category) {
              object.syncBlogCategory.push(
                {
                  pk_item: null,
                  pk_menu: null,
                  title: category,
                  type: 'syncBlogCategory',
                  link_name: category,
                  pk_father: 0,
                  position: 0,
                }
              );
            });
          });

          return object;
        };

        /**
         * Calculates the last pk_item of the menu items.
         *
         * @param {array} menuItems The array with the items of the menu.
         * @returns The last pk_item of the given array.
         */
        $scope.getLastIndex = function(menuItems) {
          var last = 1;

          if (!menuItems || menuItems.length === 0) {
            return last;
          }

          menuItems.forEach(function(menuItem) {
            if (menuItem.pk_item && menuItem.pk_item > last) {
              last = menuItem.pk_item;
            }
          });

          return last;
        };

        /**
         * @param {Object} dragable A menu item object.
         *
         * @returns true if the menu item is already in the menu, false otherwise.
         */
        $scope.isAlreadyInMenu = function(dragable) {
          if (dragable.type === 'external') {
            return false;
          }

          for (var parent in $scope.parents) {
            var item = $scope.parents[parent];

            if ($scope.isEqual(item, dragable)) {
              return true;
            }

            for (var child in $scope.childs[item.pk_item]) {
              if ($scope.isEqual($scope.childs[item.pk_item][child], dragable)) {
                return true;
              }
            }
          }

          return false;
        };

        /**
         * @param {Object} original The original object.
         * @param {Object} copy     The item to check if is a copy of the original
         *
         * @returns true if the objects are equal, false otherwise.
         */
        $scope.isEqual = function(original, copy) {
          return original.type === copy.type && original.link_name === copy.link_name;
        };

        /**
         * Watcher to generate the array of childs for the new parents.
         */
        $scope.$watch(function() {
          if (!$scope.parents) {
            return null;
          }

          return $scope.parents.map(function(parent) {
            return parent.pk_item;
          }).join(',');
        }, function(nv, ov) {
          if (!nv && !ov) {
            return;
          }

          var oldKeys = ov ? ov.split(',') : [];

          var newKey = nv.split(',').filter(function(key) {
            return !oldKeys.includes(key);
          });

          $scope.dragables = $scope.filterDragables($scope.menuData);

          if (!newKey || newKey.length === 0) {
            return;
          }

          var key = newKey.shift();

          if ($scope.childs[key]) {
            return;
          }

          $scope.childs[key] = [];
        });

        /**
         * Watcher to refresh the dragable items when something changes in search or childs.
         */
        $scope.$watch(function() {
          return JSON.stringify($scope.childs) + JSON.stringify($scope.search);
        }, function(nv, ov) {
          if (nv === ov) {
            return;
          }

          $scope.dragables = $scope.filterDragables($scope.menuData);
        });
      }
    ]);
})();

