(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsAgencyResourceListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('NewsAgencyResourceListCtrl', [
      '$controller', '$scope', '$uibModal', '$window', 'http', 'messenger', 'oqlEncoder',
      function($controller, $scope, $uibModal, $window, http, messenger, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          type: 'text',
          orderBy: { created_time: 'desc' },
          epp: 10,
          page: 1
        };

        /**
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getContent: 'api_v1_backend_news_agency_resource_get_content',
          getList:    'api_v1_backend_news_agency_resource_get_list',
          importItem: 'api_v1_backend_news_agency_resource_import_item',
          importList: 'api_v1_backend_news_agency_resource_import_list'
        };

        /**
         * @inheritdoc
         */
        $scope.countSelectedItems = function() {
          return _.uniq(_.concat($scope.selected.items,
            $scope.selected.related)).length;
        };

        /**
         * @inheritdoc
         */
        $scope.isModeSupported = function() {
          return $scope.criteria.type === 'photo';
        };

        /**
         * @function hasTexts
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Checks if the item is of type text or if there is any item of type
         *   text in the list of items.
         *
         * @param {Object} template The template object.
         *
         * @return {Boolean} True if there an item of type text in the list.
         *                   False otherwise.
         */
        $scope.hasTexts = function(items) {
          return items && items.filter(function(e) {
            return e.type === 'text';
          }).length > 0;
        };

        /**
         * @function importItem
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Opens a modal window to import one item.
         *
         * @param {Object} content The content to import.
         */
        $scope.importItem = function(item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-import',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  hasTexts:          $scope.hasTexts,
                  id:                item.id,
                  isEditable:        $scope.isEditable,
                  items:             [ item ],
                  related:           $scope.data.extra.related,
                  content_type_name: item.type === 'photo' ? 'photo' : 'article',
                  onmai_prompts:     $scope.onmai_prompts,
                  onmai_extras:      $scope.onmai_extras,
                };
              },
              success: function() {
                return function(modal, template) {
                  return $scope.saveItem(template.id, template);
                };
              }
            }
          });

          modal.result.then(function(response) {
            $scope.disableFlags('http');

            if (response.status === 201 && response.headers('location')) {
              $window.location.href = response.headers('location');
              return;
            }

            if (response.success) {
              $scope.list();
            }

            if (response.data) {
              messenger.post(response.data);
            }
          });
        };

        /**
         * @function importList
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Opens a modal window to import the selected items.
         */
        $scope.importList = function() {
          var ids     = $scope.selected.related.concat($scope.selected.items);
          var related = [];
          var items   = $scope.items.filter(function(e) {
            return $scope.selected.items.indexOf(e.id) !== -1;
          });

          for (var i = 0; i < $scope.selected.related.length; i++) {
            related[$scope.selected.related[i]] =
              $scope.data.extra.related[$scope.selected.related[i]];
          }

          var type = items.filter(function(e) {
            return e.type === 'photo';
          }).length === items.length ? 'photo' : 'article';

          var modal = $uibModal.open({
            templateUrl: 'modal-import',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  hasTexts:          $scope.hasTexts,
                  ids:               ids,
                  isEditable:        $scope.isEditable,
                  items:             items,
                  related:           related,
                  content_type_name: type
                };
              },
              success: function() {
                return function(modal, template) {
                  return $scope.saveList(template.ids, template);
                };
              }
            }
          });

          modal.result.then(function(response) {
            $scope.disableFlags('http');

            if (response.data) {
              messenger.post(response.data);
            }

            if (response.success) {
              $scope.list();
            }
          });
        };

        /**
         * @function init
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          $scope.selected.related   = [];
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({ placeholder: {
            title: '[key] ~ "[value]"',
          } });

          $scope.setMode('list');

          if ($scope.app.mode === 'grid') {
            $scope.criteria.epp = $scope.getEppInGrid();
          }

          $scope.list();

          $scope.getOnmAIPrompts();
        };

        /**
         * @function isEditable
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Checks if you can edit the content after importing.
         *
         * @return {Boolean} True if content could be editable after importing.
         *                   Otherwise, returns false.
         */
        $scope.isEditable = function(template) {
          return angular.isDefined(template.id) &&
            template.items.length === 1 &&
            template.items[0].type === 'text';
        };

        /**
         * @function isImported
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Checks if the item is already imported.
         *
         * @param {Object} item The item to check
         *
         * @return {Boolean} True if the item is already imported. False
         *                   otherwise.
         */
        $scope.isImported = function(item) {
          return $scope.data.extra.imported &&
            $scope.data.extra.imported.indexOf(item.urn) !== -1;
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function(item) {
          return !$scope.isImported(item);
        };

        /**
         * @function preview
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Displays a modal window with the content preview.
         *
         * @param {Object} content The content to preview.
         */
        $scope.preview = function(item) {
          $uibModal.open({
            templateUrl: 'modal-preview',
            windowClass: 'modal-news-agency-preview',
            controller:  'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  getImage: $scope.getImage,
                  item:     item,
                  timezone: $scope.data.extra.timezone,
                  related:  $scope.data.extra.related,
                  routes:   $scope.routes,
                  routing:  $scope.routing
                };
              },
              success: function() {
                return function(m) {
                  $scope.import(item);
                  m.close(1);
                };
              }
            }
          });
        };

        /**
         * @function saveItem
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Saves a new item based on template parameter.
         *
         * @param {String} id       The resource id.
         * @param {String} template The template with information to use while
         *                          saving.
         *
         * @return {Object} The promise object.
         */
        $scope.saveItem = function(id, template) {
          $scope.flags.http.saving = true;

          var route = {
            name: $scope.routes.importItem,
            params: { id: id }
          };

          var data = {
            fk_author: template.fk_author ? template.fk_author : null,
            fk_content_category: template.fk_content_category ?
              template.fk_content_category : null,
            content_status: template.content_status,
            content_type_name: template.content_type_name,
            prompt: template.promptSelected ? template.promptSelected.prompt : null,
            tone: template.toneSelected ? template.toneSelected.name : null,
            language: template.languageSelected ? template.languageSelected.code : null,
          };

          return http.put(route, data);
        };

        /**
         * @function saveItem
         * @memberOf NewsAgencyResourceListCtrl
         *
         * @description
         *   Saves a new item based on template parameter.
         *
         * @param {String} ids      The list of resource ids.
         * @param {String} template The template with information to use while
         *                          saving.
         *
         * @return {Object} The promise object.
         */
        $scope.saveList = function(ids, template) {
          $scope.flags.http.saving = true;

          var data = {
            ids: ids,
            fk_author: template.author ? template.author : null,
            fk_content_category: template.fk_content_category ?
              template.fk_content_category : null,
            content_status: template.content_status,
            content_type_name: template.content_type_name,
            prompt: template.promptSelected ? template.promptSelected.prompt : null,
            tone: template.toneSelected ? template.toneSelected.name : null
          };

          return http.post($scope.routes.importList, data);
        };

        /**
         * @inheritdoc
         */
        $scope.sort = function(name) {
          if (!$scope.criteria.orderBy) {
            $scope.criteria.orderBy = {};
          }

          var direction = !$scope.criteria.orderBy[name] ||
              $scope.criteria.orderBy[name] === 'desc' ? 'asc' : 'desc';

          $scope.criteria.orderBy       = {};
          $scope.criteria.orderBy[name] = direction;
          $scope.criteria.page          = 1;
        };

        // Update epp when mode changes
        $scope.$watch(function() {
          return $scope.app.mode;
        }, function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (nv === 'grid') {
            var epp = $scope.getEppInGrid();

            if (epp !== $scope.criteria.epp) {
              $scope.criteria.epp = epp;
              return;
            }
          }

          $scope.criteria.epp = 10;
        }, true);

        // Updates the list mode when criteria changes.
        $scope.$watch('criteria.type', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          if (nv === 'photo') {
            $scope.setMode('grid');
            $scope.flags.http.loading = true;
          } else {
            $scope.setMode('list');
            $scope.flags.http.loading = true;
          }
        });

        // Updates expanded status when items change
        $scope.$watch('items', function(nv) {
          if (!nv) {
            return;
          }

          $scope.expanded = [];

          for (var i = 0; i < $scope.items.length; i++) {
            $scope.expanded[i] = false;
          }
        });

        // Updates selected related items when selected items change
        $scope.$watch('selected.items', function(nv, ov) {
          if (!nv || !$scope.items) {
            return;
          }

          var added = nv.filter(function(a) {
            return ov.indexOf(a) === -1;
          });

          var deleted = ov.filter(function(a) {
            return nv.indexOf(a) === -1;
          });

          var relatedToAdd    = [];
          var relatedToDelete = [];

          for (var i = 0; i < $scope.items.length; i++) {
            if (added.indexOf($scope.items[i].id) !== -1) {
              relatedToAdd = relatedToAdd.concat($scope.items[i].related);
            }

            if (deleted.indexOf($scope.items[i].id) !== -1) {
              relatedToDelete = relatedToDelete.concat($scope.items[i].related);
            }
          }

          $scope.selected.related = _.concat(_.difference(
            $scope.selected.related, relatedToDelete), relatedToAdd);
        }, true);

        $scope.getOnmAIPrompts = function() {
          $scope.waiting = true;
          var oqlQuery   = oqlEncoder.getOql({
            epp: 1000,
            mode: 'Agency',
            orderBy: { name: 'asc' },
            page: 1,
          });

          http.get({ name: 'api_v1_backend_onmai_prompt_get_list', params: { oql: oqlQuery } })
            .then(function(response) {
              $scope.onmai_prompts = response.data.items;
              $scope.onmai_extras = response.data.extra;
            }).finally(function() {
              $scope.waiting = false;
            });
        };
      }
    ]);
})();
