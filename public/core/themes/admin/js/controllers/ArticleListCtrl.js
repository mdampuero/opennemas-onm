(function() {
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
      '$controller', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'routing', '$window',
      function($controller, $scope, $uibModal, http, messenger, oqlEncoder, routing, $window) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

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
         * @memberOf ArticleListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_article_delete_item',
          deleteList: 'api_v1_backend_article_delete_list',
          getList:    'api_v1_backend_article_get_list',
          patchItem:  'api_v1_backend_article_patch_item',
          patchList:  'api_v1_backend_article_patch_list',
          public:     'frontend_article_show'
        };

        /**
         * @function getFrontendUrl
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          if (!$scope.categories) {
            return '';
          }

          var categories = $scope.categories.filter(function(e) {
            return e.id === item.categories[0];
          });

          if (categories.length === 0) {
            return '';
          }

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              category_slug: categories[0].name
            })
          );
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
              title: 'title ~ "%[value]%"'
            }
          });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
          $scope.localize($scope.data.extra.categories, 'categories');
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
            backdrop: 'static',
            keyboard: false,
            backdropClass: 'modal-backdrop-dark',
            controller:  'BackgroundTaskModalCtrl',
            openedClass: 'modal-relative-open',
            templateUrl: 'modal-translate-selected',
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
      }
    ]);
})();
