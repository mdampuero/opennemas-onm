(function() {
  'use strict';

  angular.module('BackendApp')

    /**
     * @ngdoc controller
     * @name  ListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     *
     * @description
     *   Generic controller for lists.
     */
    .controller('ListCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger', '$timeout',
      function($controller, $scope, $uibModal, http, messenger, $timeout) {
        $.extend(this, $controller('BaseCtrl', { $scope: $scope }));

        /**
         * @memberOf ListCtrl
         *
         * @description
         *   The list of selected elements.
         *
         * @type {Array}
         */
        $scope.selected = { all: false, items: [] };

        /**
         * @memberOf ListCtrl
         *
         * @description
         *  Variable for timeout actions.
         *
         * @type {type}
         */
        $scope.tm = null;

        /**
         * The available elements per page
         *
         * @type {Array}
         */
        $scope.views = [ 10, 25, 50, 100 ];

        /**
         * @function closeColumns
         * @memberOf ClientListCtrl
         *
         * @description
         *   Hides the dropdown to toggle table columns.
         */
        $scope.closeColumns = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.config.columns.collapsed = true;
          }, 500);
        };

        /**
         * @function list
         * @memberOf ListCtrl
         *
         * @description
         *   Just a dummy actions that forces the developer
         *   to overwrite this method on child classes.
         */
        $scope.list = function() {
          throw Error('Method not implemented');
        };

        /**
         * @function openColumns
         * @memberOf ClientListCtrl
         *
         * @description
         *   Shows the dropdown to toggle table columns.
         */
        $scope.openColumns = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.config.columns.collapsed = false;
          }, 500);
        };

        /**
         * @function isColumnEnabled
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if a columns is enabled.
         *
         * @param {String} name The columns name.
         */
        $scope.isColumnEnabled = function(name) {
          return $scope.config.columns.selected.indexOf(name) !== -1;
        };

        /**
         * @function isOrderedBy
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if the list is ordered by the given field name.
         *
         * @param {String} name The field name.
         *
         * @return {mixed} The order value, if the order exists. Otherwise,
         *                 returns false.
         */
        $scope.isOrderedBy = function(name) {
          if ($scope.criteria && $scope.criteria.orderBy &&
              $scope.criteria.orderBy[name]) {
            return $scope.criteria.orderBy[name];
          }

          return false;
        };

        /**
         * @function isSelected
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if an item is selected.
         *
         * @param {String} id The item id.
         */
        $scope.isSelected = function(id) {
          return $scope.selected.items.indexOf(id) !== -1;
        };

        /**
         * @function deselectAll
         * @memberOf ListCtrl
         *
         * @description
         *   Deselects all elements.
         */
        $scope.deselectAll = function() {
          $scope.selected = { all: false, items: [] };
        };

        /**
         * @function searchByKeypress
         * @memberOf ListCtrl
         *
         * @description
         *   Reloads the list on keypress.
         *
         * @param {Object} event The event object.
         */
        $scope.searchByKeypress = function(event) {
          if (event.keyCode === 13) {
            if ($scope.criteria.page !== 1) {
              $scope.criteria.page = 1;
              return;
            }

            $scope.list();
          }
        };

        /**
         * @function sort
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Changes the sort order.
         *
         * @param string name Field name.
         */
        $scope.sort = function(name) {
          if (!$scope.criteria.orderBy) {
            $scope.criteria.orderBy = {};
          }

          if ($scope.criteria.orderBy[name] === 'asc') {
            $scope.criteria.orderBy[name] = 'desc';
            return;
          }

          if ($scope.criteria.orderBy[name] === 'desc') {
            delete $scope.criteria.orderBy[name];
            return;
          }

          $scope.criteria.orderBy[name] = 'asc';
          $scope.criteria.page          = 1;
        };

        /**
         * @function toggleAll
         * @memberOf ListCtrl
         *
         * @description
         *   Toggles all items selection.
         */
        $scope.toggleAll = function() {
          if ($scope.selected.all) {
            $scope.selected.items = $scope.items.map(function(item) {
              return item.id;
            });
          } else {
            $scope.selected.items = [];
          }
        };

        /**
         * @function toggleColumns
         * @memberOf ListCtrl
         *
         * @description
         *   Toggles column filters container.
         */
        $scope.toggleColumns = function() {
          $scope.config.columns.collapsed = !$scope.config.columns.collapsed;

          if (!$scope.config.columns.collapsed) {
            $scope.scrollTop();
          }
        };

        /**
         * Translates contents
         *
         * @param mixed content The content to send to trash.
         */
        $scope.selectedItemsAreTranslatedTo = function(translateToParam) {
          var anyTranslated = false;

          $scope.selected.items.forEach(function(selectedId) {
            $scope.data.results.forEach(function(el) {
              if (el.id === selectedId) {
                if (el.title[translateToParam] && el.title[translateToParam].length > 0) {
                  anyTranslated = anyTranslated || true;
                }
              }
            });
          });

          return anyTranslated;
        };

        /**
         * Translates contents
         *
         * @param mixed content The content to send to trash.
         */
        $scope.translateSelected = function(translateToParam) {
          var config = {
            translateFrom:  $scope.data.extra.locale,
            translateTo: translateToParam,
            locales: $scope.data.extra.options.available,
            translators: $scope.data.extra.translators,
            translatorSelected: 0,
          };

          config.translators.forEach(function(el, index) {
            if (el.from === config.translateFrom &&
              el.to === config.translateTo &&
              el.default === true || el.default === 'true') {
              config.translatorSelected = index;
            }
          });

          config.translators = config.translators.filter(function(el) {
            return el.from === config.translateFrom && el.to === config.translateTo;
          });

          var topScope = $scope;

          // Raise a modal indicating that we are translating in background
          $uibModal.open({
            backdrop:    true,
            backdropClass: 'modal-backdrop-transparent',
            controller:  'BackgroundTaskModalCtrl',
            openedClass: 'modal-relative-open',
            templateUrl: 'modal-translate-selected',
            keyboard: false,
            resolve: {
              template: function() {
                return {
                  selected: $scope.selected.items,
                  config: config,
                  translating: false,
                };
              },
              callback: function() {
                return function(modal, template) {
                  var translateParams = {
                    ids: $scope.selected.items,
                    from: config.translateFrom,
                    to: config.translateTo,
                    translator: config.translatorSelected,
                  };

                  if (template.config.translators.length < 1) {
                    topScope.selected = { all: false, items: [] };
                    return;
                  }

                  template.translating = true;
                  template.translation_done = false;

                  http.post({
                    name: 'api_v1_backend_tools_translate_contents', params: { }
                  }, translateParams)
                    .then(function(response) {
                      var message = {
                        id: new Date().getTime(),
                        message: 'Unable to translate contents. Please check your configuration.',
                        type: 'error'
                      };

                      if (response) {
                        if (response.data) {
                          topScope.selected = { all: false, items: [] };
                          message = response.data.message;

                          template.translating = false;
                          template.translation_done = true;

                          $scope.list();
                        }
                      }
                    }, function(response) {
                      var message = {
                        id: new Date().getTime(),
                        message: 'Unable to translate contents. Please check your configuration.',
                        type: 'error'
                      };

                      modal.close({ response: true, success: true });
                      messenger.post(message);
                    });
                };
              }
            }
          });
        };

        // Marks variables to delete for garbage collector
        $scope.$on('$destroy', function() {
          $scope.criteria = null;
          $scope.config   = null;
          $scope.items    = null;
          $scope.selected = null;
        });

        // Updates linkers when locale changes
        $scope.$watch('config.locale', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.config.multilanguage || !$scope.config.locale) {
            return;
          }

          for (var key in $scope.config.linkers) {
            $scope.config.linkers[key].setKey(nv);
            $scope.config.linkers[key].update();
          }
        });

        // Reloads the list when filters change.
        $scope.$watch('criteria', function(nv, ov) {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          if (nv === ov) {
            return;
          }

          // Reset page when criteria changes
          if (nv.page === ov.page) {
            nv.page = 1;
          }

          $scope.tm = $timeout(function() {
            $scope.list();
          }, 500);
        }, true);
      }
    ]);
})();
