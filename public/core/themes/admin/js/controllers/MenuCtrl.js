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
      '$controller', '$scope', 'localizer', 'linker', 'cleaner',
      function($controller, $scope, localizer, linker, cleaner) {
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

        $scope.defaultLink = {
          title: 'External Link',
          link_name: '/',
          pk_item: null,
          pk_menu: null,
          pk_father: 0,
          position: 0,
          submenu: [],
          uniqueID: null
        };

        // Unique ID for all elements
        $scope.uniqueID = 0;

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
            $scope.lang = $scope.languageData.default;
          }

          //  Create manual categories as a copy of automatic categories
          $scope.data.extra['blog-category'] = angular.copy($scope.data.extra.category);

          //  Parse extra data to use it on tpl
          $scope.menuData = $scope.transformExtraData($scope.data.extra);

          // Filter data in order to avoid duplicates
          $scope.menuData['blog-category'] = $scope.filterData(
            angular.copy($scope.menuData['blog-category']),
            angular.copy($scope.data.item.menu_items),
            'blog-category'
          );
          $scope.menuData.internal = $scope.filterData(
            angular.copy($scope.menuData.internal),
            angular.copy($scope.data.item.menu_items),
            'internal'
          );
          $scope.menuData.static = $scope.filterData(
            angular.copy($scope.menuData.static),
            angular.copy($scope.data.item.menu_items),
            'static'
          );
          $scope.menuData.category = $scope.filterData(
            angular.copy($scope.menuData.category),
            angular.copy($scope.data.item.menu_items),
            'category'
          );
          $scope.menuData.syncBlogCategory = $scope.filterData(
            angular.copy($scope.menuData.syncBlogCategory),
            angular.copy($scope.data.item.menu_items),
            'syncBlogCategory'
          );

          //  Parse item data (insert nested menu items as submenus)
          $scope.data.item.menu_items = $scope.transformItemData($scope.data.item.menu_items);

          $scope.item.menu_items = [];
          for (const menuItemIndex in $scope.data.item.menu_items) {
            //  Add unique id to elemnt
            $scope.data.item.menu_items[menuItemIndex].uniqueID = $scope.uniqueID;

            //  Localize Menu Items without submenues
            $scope.item.menu_items[menuItemIndex] = $scope.localizeItem(
              $scope.data.item.menu_items[menuItemIndex],
              $scope.data.item.menu_items[menuItemIndex].uniqueID, $scope, true, [ 'submenu' ]
            );
            //  Note that inscrement must go after localize due to linker index
            $scope.uniqueID++;

            // Same thing here with submenu items
            if ($scope.data.item.menu_items[menuItemIndex].submenu &&
              $scope.data.item.menu_items[menuItemIndex].submenu.length > 0) {
              $scope.item.menu_items[menuItemIndex].submenu = [];
              for (const submenuIndex in $scope.data.item.menu_items[menuItemIndex].submenu) {
                $scope.data.item.menu_items[menuItemIndex].submenu[submenuIndex].uniqueID = $scope.uniqueID;
                $scope.item.menu_items[menuItemIndex].submenu[submenuIndex] = $scope.localizeItem(
                  $scope.data.item.menu_items[menuItemIndex].submenu[submenuIndex],
                  $scope.data.item.menu_items[menuItemIndex].submenu[submenuIndex].uniqueID, $scope, true
                );
                $scope.uniqueID++;
              }
            }
          }

          // Add dragable items to original menu_items array in order to allow translations from this fields
          for (const categoryIndex in $scope.menuData.category) {
            $scope.data.item.menu_items.push($scope.menuData.category[categoryIndex]);
          }
          for (const categoryIndex in $scope.menuData['blog-category']) {
            $scope.data.item.menu_items.push($scope.menuData['blog-category'][categoryIndex]);
          }
          for (const categoryIndex in $scope.menuData.internal) {
            $scope.data.item.menu_items.push($scope.menuData.internal[categoryIndex]);
          }
          for (const categoryIndex in $scope.menuData.static) {
            $scope.data.item.menu_items.push($scope.menuData.static[categoryIndex]);
          }
          for (const categoryIndex in $scope.menuData.syncBlogCategory) {
            $scope.data.item.menu_items.push($scope.menuData.syncBlogCategory[categoryIndex]);
          }
          $scope.defaultLink.uniqueID = $scope.uniqueID;
          $scope.linkData = [ angular.copy($scope.defaultLink) ];
          $scope.data.item.menu_items.push($scope.linkData[0]);
          $scope.uniqueID++;
        };

        // Searchbar function
        $scope.visible = function(item, searchModel) {
          return !($scope[searchModel] && $scope[searchModel].length > 0 &&
            item.title.toLowerCase().indexOf($scope[searchModel]) === -1);
        };

        // Localize function
        $scope.localizeItem = function(item, linkerKey, scopeKey, clean, ignore = null) {
          var uniqueIndex = linkerKey;

          // Create localized element for title and link_name keys
          var lz = localizer.get($scope.config.locale)
            .localize(item, [ 'title', 'link_name' ], $scope.config.locale);

          $scope.config.linkers[uniqueIndex] = {};

          //  Ignore param to avoid collision with submenus
          if (ignore) {
            $scope.config.linkers[uniqueIndex] = linker.get([ 'title', 'link_name' ],
              $scope.config.locale.default, scopeKey, clean, ignore);
          } else {
            $scope.config.linkers[uniqueIndex] = linker.get([ 'title', 'link_name' ],
              $scope.config.locale.default, scopeKey, clean);
          }
          $scope.config.linkers[uniqueIndex]
            .setKey($scope.config.locale.selected);

          // Link original and localized items
          $scope.config.linkers[uniqueIndex].link(item, lz);

          $scope.config.linkers[uniqueIndex].update();
          return lz;
        };

        /**
         * @function localizeText
         * @memberOf ContentRestInnerCtrl
         *
         * @param {any} String or Object to localize.
         *
         * @return {String} Localized text.
         *
         * @description
         *   Localize and return text
         */
        $scope.localizeText = function(text) {
          if (typeof text === 'object') {
            return text[$scope.config.locale.selected];
          }

          return text;
        };

        $scope.getData = function() {
          //  Parse menu_items data, from nested submenus to plain array
          var parsedMenuItems = $scope.saveMenuItems(
            $scope.item.menu_items, $scope.item.pk_menu, $scope.data.item.menu_items
          );

          var resultData = {
            pk_menu: $scope.item.pk_menu,
            name: $scope.item.name,
            position: $scope.item.position,
            menu_items: parsedMenuItems
          };

          return cleaner.clean(resultData);
        };

        $scope.removeItem = function(index, parentIndex) {
          var removed = [];

          if (!isNaN(parentIndex)) {
            removed = $scope.item.menu_items[parentIndex].submenu.splice(index, 1);
          } else {
            removed = $scope.item.menu_items.splice(index, 1);
          }

          if ($scope.menuData[removed[0].type]) {
            if (removed[0].submenu && removed[0].submenu.length > 0) {
              for (const item in removed[0].submenu) {
                if ($scope.menuData[removed[0].submenu[item].type]) {
                  $scope.menuData[removed[0].submenu[item].type].push(removed[0].submenu[item]);
                }
              }
            }
            removed[0].submenu = [];
            $scope.menuData[removed[0].type].push(removed[0]);
          }
        };
        $scope.transformItemData = function(data) {
          var finalData = [];

          for (var iterator = 0; iterator < data.length; iterator++) {
            data[iterator].submenu = data[iterator].submenu ? data[iterator].submenu : [];
            if (data[iterator].pk_father > 0) {
              for (const iterator2 in finalData) {
                if (finalData[iterator2].pk_item === data[iterator].pk_father) {
                  finalData[iterator2].submenu.push(data[iterator]);
                }
              }
            } else {
              finalData.push(data[iterator]);
            }
          }
          return finalData;
        };
        $scope.transformExtraData = function(data) {
          var transformedData = {};

          if (!data) {
            return transformedData;
          }
          for (const dataElement in data) {
            switch (dataElement) {
              case 'internal':
              case 'static':
              case 'blog-category':
              case 'category':
                var element = [];

                for (const itemData in data[dataElement]) {
                  var item = {
                    pk_item: null,
                    pk_menu: null,
                    title: '',
                    link_name: '',
                    pk_father: 0,
                    position: 0,
                    submenu: [],
                    uniqueID:   $scope.uniqueID
                  };

                  $scope.uniqueID++;
                  if (!isNaN($scope.item.pk_menu)) {
                    item.pk_menu = $scope.item.pk_menu;
                  }
                  item.type = data[dataElement][itemData].type;
                  if (data[dataElement][itemData].title) {
                    item.title = data[dataElement][itemData].title;
                  }
                  item.link_name = data[dataElement][itemData].slug || data[dataElement][itemData].link ?
                    data[dataElement][itemData].slug ? data[dataElement][itemData].slug :
                      data[dataElement][itemData].link : data[dataElement][itemData].name;

                  element.push(item);
                }
                transformedData[dataElement] = element;
                break;
              case 'syncBlogCategory':
                var element = [];

                for (const itemData in data[dataElement]) {
                  for (const category in data[dataElement][itemData].categories) {
                    var item = {
                      pk_item: null,
                      pk_menu: null,
                      title: data[dataElement][itemData].categories[category],
                      link_name: data[dataElement][itemData].site_url,
                      pk_father: 0,
                      position: 0,
                      submenu: [],
                      uniqueID:   $scope.uniqueID
                    };

                    $scope.uniqueID++;

                    item.pk_menu = !isNaN($scope.item.pk_menu) ? $scope.item.pk_menu : null;

                    element.push(item);
                  }
                }
                transformedData[dataElement] = element;
                break;
              default:
                transformedData[dataElement] = angular.copy(data[dataElement]);
            }
          }
          return transformedData;
        };
        $scope.filterData = function(data, reference, type) {
          var finalData = [];

          for (const elementIndex in data) {
            var unique = true;

            for (const referenceIndex in reference) {
              if (reference[referenceIndex].type === type &&
                reference[referenceIndex].title === data[elementIndex].title) {
                unique = false;
              }
            }
            if (unique) {
              finalData.push(data[elementIndex]);
            }
          }
          return finalData;
        };
        $scope.saveMenuItems = function(data, pkMenu, reference) {
          var finalData = [];
          var itemCount = 1;

          //  Loop all localized menu items

          for (const dataIndex in data) {
            var item = {
              pk_item: itemCount,
              pk_menu: pkMenu,
              title: data[dataIndex].title,
              link_name: data[dataIndex].link_name,
              type: data[dataIndex].type,
              position: data[dataIndex].position,
              pk_father: 0,
              uniqueID: data[dataIndex].uniqueID,
            };

            /*
             * Loop original items, if same unique id between original and localized means multilanguage,
             * so take title and link_name from original item (array with languages instead of plain text)
             */

            for (const originalItemsIndex in reference) {
              if (reference[originalItemsIndex].uniqueID === item.uniqueID) {
                item.title = reference[originalItemsIndex].title;
                item.link_name = reference[originalItemsIndex].link_name;
              }
            }
            finalData.push(angular.copy(item));
            itemCount++;
            if (data[dataIndex].submenu && data[dataIndex].submenu.length > 0) {
              for (const submenuIndex in data[dataIndex].submenu) {
                var submenu = {
                  pk_item: itemCount,
                  pk_menu: pkMenu,
                  title: data[dataIndex].submenu[submenuIndex].title,
                  link_name: data[dataIndex].submenu[submenuIndex].link_name,
                  type: data[dataIndex].submenu[submenuIndex].type,
                  position: data[dataIndex].submenu[submenuIndex].position,
                  pk_father: item.pk_item,
                  uniqueID: data[dataIndex].submenu[submenuIndex].uniqueID,
                };

                // Same here for submenues (abreviate if to avoid item nested too deeply warning XD)
                for (const SubmenuIndex in reference) {
                  submenu.title = reference[SubmenuIndex].uniqueID === submenu.uniqueID ?
                    reference[SubmenuIndex].title : submenu.title;
                  submenu.link_name = reference[SubmenuIndex].uniqueID === submenu.uniqueID ?
                    reference[SubmenuIndex].link_name : submenu.link_name;
                }
                finalData.push(angular.copy(submenu));
                itemCount++;
              }
            }
          }
          return finalData;
        };

        // Updates the menu items input value when menu items change.
        $scope.$watch('item.menu_items', function(nv, ov) {
          if (nv === ov || !nv) {
            return;
          }

          for (const nvIndex in nv) {
            // When dragged item with submenues, update submenues->pk_father based on new father->pk_item
            if (nv[nvIndex].submenu.length > 0) {
              for (const submenuIndex in nv[nvIndex].submenu) {
                nv[nvIndex].submenu[submenuIndex].pk_father = nv[nvIndex].pk_item;
              }
            }

            // If not linker found means new item was dragged from rigth columns
            if (!$scope.config.linkers[nv[nvIndex].uniqueID]) {
              // Find the original element (added previously) by uniqueID
              const originalItem = $scope.data.item.menu_items.filter(function(item) {
                return item.uniqueID === nv[nvIndex].uniqueID;
              });
              // Link original and localized elements

              $scope.item.menu_items[nvIndex] = $scope.localizeItem(
                originalItem[0], nv[nvIndex].uniqueID, $scope, true, [ 'submenu' ]
              );
            }
            // Same for submenues
            if (nv[nvIndex].submenu && nv[nvIndex].submenu.length > 0) {
              for (const nvSubmenuIndex in nv[nvIndex].submenu) {
                if (!$scope.config.linkers[nv[nvIndex].submenu[nvSubmenuIndex].uniqueID]) {
                  const originalItem = $scope.data.item.menu_items.filter(function(item) {
                    return item.uniqueID === nv[nvIndex].submenu[nvSubmenuIndex].uniqueID;
                  });

                  $scope.item.menu_items[nvIndex].submenu[nvSubmenuIndex] = $scope.localizeItem(
                    originalItem[0], nv[nvIndex].submenu[nvSubmenuIndex].uniqueID, $scope, true, [ 'submenu' ]
                  );
                }
              }
            }
          }
        }, true);
        // Updates linkers when locale changes
        $scope.$watch('config.locale.selected', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.config.locale.multilanguage ||
              !$scope.config.locale.selected) {
            return;
          }
          for (var key in $scope.config.linkers) {
            $scope.config.linkers[key].setKey(nv);
            $scope.config.linkers[key].update();
          }
        }, true);
        $scope.$watch('linkData.length', function(nv, ov) {
          if (nv === 0 && ov > 0) {
            // Create new link dragable when container is empty
            $scope.defaultLink.uniqueID = $scope.uniqueID;
            $scope.uniqueID++;
            $scope.linkData = [ angular.copy($scope.defaultLink) ];
            $scope.data.item.menu_items.push($scope.linkData[0]);
          }
        });
      }
    ]);
})();

