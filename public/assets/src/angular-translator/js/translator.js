(function() {
  'use strict';

  angular.module('onm.translator', [])

    .service('translator', [
      'http', '$uibModal',
      function(http, $uibModal) {
      /**
       * @memberOf translator
       *
       * @description
       *  The translator service.
       *
       * @type {Object}
       */
        var translator = {};

        /**
         * @function init
         * @memberOf Translator
         *
         * @description
         *  Initializes the Translator's scope to the actual scope.
         *
         * @param {Object} scope The actual scope.
         */
        translator.init = function(scope) {
          translator.scope = scope;

          // Shows a modal window to translate content automatically
          translator.scope.$watch('config.locale.selected', function(nv, ov) {
            if (!nv || nv === ov ||
              translator.isTranslated(translator.scope.data.item,
                translator.scope.data.extra.keys, nv)) {
              return;
            }

            // Filter for selected locale and translated in original language
            var translators = translator.scope.config.locale.translators.filter(function(e) {
              return e.to === nv && translator.isTranslated(translator.scope.data.item,
                translator.scope.data.extra.keys, e.from);
            });

            if (translators.length === 0) {
              return;
            }

            var config = {
              locales: translator.scope.config.locale.available,
              translators: translators
            };

            translator.translate(nv, config);
          }, true);
        };

        /**
         * @function isTranslated
         * @memberOf Translator
         *
         * @description
         *   Checks if the item is translated to the locale.
         *
         * @param {String} locale The locale to check.
         *
         * @return {Boolean} True if the item is translated. False otherwise.
         */
        translator.isTranslated = function(item, keys, locale) {
          return keys.some((key) => item[key] && item[key][locale]);
        };

        /**
         * @function translate
         * @memberOf Translator
         *
         * @description
         *   Shows a modal to translate a content automatically.
         *
         * @param {String} to     The locale to translate to.
         * @param {Object} config The locale-related configuration.
         *
         * @return {type} description
         */
        translator.translate = function(to, configParam) {
          var config = {
            translateFrom:  translator.scope.data.extra.locale,
            translateTo: to,
            locales: configParam.locales,
            translators: configParam.translators,
            translatorSelected: 0,
          };

          // Pick the default translator
          config.translators.forEach(function(el, index) {
            if (el.from === config.translateFrom &&
              el.to === config.translateTo &&
              el.default === true || el.default === 'true') {
              config.translatorSelected = index;
            }
          });

          // Raise a modal to indicate that background translation is being executed
          $uibModal.open({
            backdrop: 'static',
            keyboard: false,
            backdropClass: 'modal-backdrop-dark',
            controller:  'BackgroundTaskModalCtrl',
            openedClass: 'modal-relative-open',
            templateUrl: 'modal-translate',
            resolve: {
              template: function() {
                return {
                  config: config,
                  translating: false,
                };
              },
              callback: function() {
                return function(modal, template) {
                  var translatorItem = config.translators[config.translatorSelected];

                  // If no default translator dont call the server
                  if (!translatorItem) {
                    return;
                  }

                  template.translating = true;
                  template.translation_done = false;

                  var params = {
                    data: {},
                    from: translatorItem.from,
                    to: translatorItem.to,
                    translator: config.translatorSelected,
                  };

                  for (var i = 0; i < translator.scope.data.extra.keys.length; i++) {
                    var key = translator.scope.data.extra.keys[i];

                    if (translator.scope.data.item[key] &&
                        angular.isObject(translator.scope.data.item[key]) &&
                        translator.scope.data.item[key][params.from]) {
                      params.data[key] = translator.scope.data.item[key][params.from];
                    }
                  }

                  template.translating = true;
                  template.translation_done = false;

                  http.post('api_v1_backend_tools_translate_string', params)
                    .then(function(response) {
                      for (var i = 0; i < translator.scope.data.extra.keys.length; i++) {
                        var key = translator.scope.data.extra.keys[i];

                        translator.scope.item[key] = response.data[key];
                      }

                      template.translating = false;
                      template.translation_done = true;
                    }, function() {
                      modal.close({ response: true, error: true });
                    });
                };
              }
            }
          });
        };

        return translator;
      }
    ])

    /**
     * @ngdoc directive
     * @name  translator
     *
     * @requires $http
     * @requires routing
     *
     * @description
     *   Directive to create and display of the translator selector.
     */
    .directive('translator', [
      '$window',
      function($window) {
        return {
          // E = Element, A = Attribute, C = Class, M = Comment
          restrict: 'E',
          scope: {
            item:    '=',
            keys:    '=',
            link:    '@',
            ngModel: '=',
            options: '=',
            text:    '@'
          },
          template: function(elem, attrs) {
            if (attrs.link) {
              return '<div class="translator btn-group btn-group-sm" ng-if="collapsed || size > max">' +
                '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">' +
                  '<i class="fa fa-pencil m-r-5"></i>' +
                  '{{text}}' +
                  '<i class="fa fa-angle-down"></i>' +
                '</button>' +
                '<ul class="dropdown-menu no-padding" role="menu">' +
                  '<li ng-repeat="language in languages">' +
                    '<a href="{{link + \'?locale=\' + language.value}}">' +
                      '<i class="fa {{language.icon}} m-r-5" ng-show="language.icon"></i>' +
                      '{{language.name}}' +
                    '</a>' +
                  '</li>' +
                '</ul>' +
              '</div>' +
              '<div class="translator btn-group btn-group-sm" role="group" ng-if="!collapsed && size <= max">' +
                '<a class="btn btn-{{language.class}}"' +
                    ' href="{{link + \'?locale=\' + language.value}}" ng-repeat="language in languages">' +
                  '<i class="fa {{language.icon}} m-r-5" ng-show="language.icon"></i>{{language.name}}' +
                '</a>' +
              '</div>' +
              '<script type="text/ng-template" id="modal-translate">' +
                '<div class="modal-body">' +
                  '<button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">' +
                    '<i class="fa fa-times"></i>' +
                  '</button>' +
                  '<div ng-hide="template.config.translators.length > 0" class="text-center m-t-50 p-t-30">' +
                    '<i class="fa fa-4x fa-warning text-warning"></i>' +
                      '<h4>{t escape=off 1="[% template.config.locales[template.config.translateFrom] %]" 2="[% template.config.locales[template.config.translateTo] %]"}No available translators for "%1&rarr;%2"{/t}</h4>' +
                      '<p class="m-b-50">{t escape=off}Please go to the <a href="{url name=admin_system_settings}">Settings page</a> and configure your <br> translators for all the languages.{/t}</p>' +
                  '</div>' +
                  '<div ng-show="template.translating">' +
                    '<div class="spinner-wrapper" class="text-center m-t-50 p-t-30">' +
                      '<div class="loading-spinner"></div>' +
                      '<h4 class="text-center">{t 1="[% template.config.locales[template.config.translateTo] %]"}Translating content into "%1"{/t}</h4>' +
                    '</div>' +
                  '</div>' +
                  '<div ng-show="template.translation_done">' +
                    '<div class="text-center m-t-50 p-t-30">' +
                      '<i class="fa fa-4x fa-globe"></i>' +
                      '<h4>{t 1="[% template.config.locales[template.config.translateTo] %]"}Content translated properly into "%1".{/t}</h4>' +
                    '</div>' +
                    '<button class="btn btn-success btn-block m-t-50" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">' +
                      '<h4 class="text-uppercase text-white">' +
                        '<i class="fa fa-check"></i>' +
                        '<strong>{t}Ok{/t}</strong>' +
                      '</h4>' +
                    '</button>' +
                  '</div>' +
                '</div>' +
              '</script>';
            }

            return '<div class="translator btn-group">' +
              '<button class="btn btn-white dropdown-toggle" data-toggle="dropdown" type="button">' +
                '<i class="fa {{languages[ngModel].icon}} m-r-5" ng-show="languages[ngModel].icon"></i>' +
                '{{languages[ngModel].name}}' +
                '<i class="fa fa-angle-down"></i>' +
              '</button>' +
              '<ul class="dropdown-menu no-padding" role="menu">' +
                '<li ng-repeat="language in languages" ng-class="{ \'active\':  language.value === ngModel }">' +
                  '<a href="#" ng-click="changeCurrentLanguage($event, language.value)">' +
                    '<i class="fa {{language.icon}} m-r-5" ng-show="language.icon"></i>' +
                    '{{language.name}}' +
                  '</a>' +
                '</li>' +
              '</ul>' +
            '</div>' +
            '<script type="text/ng-template" id="modal-translate">' +
            '<div class="modal-body">' +
              '<button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">' +
                '<i class="fa fa-times"></i>' +
              '</button>' +
              '<div ng-hide="template.config.translators.length > 0" class="text-center m-t-50 p-t-30">' +
                '<i class="fa fa-4x fa-warning text-warning"></i>' +
                  '<h4>{t escape=off 1="[% template.config.locales[template.config.translateFrom] %]" 2="[% template.config.locales[template.config.translateTo] %]"}No available translators for "%1&rarr;%2"{/t}</h4>' +
                  '<p class="m-b-50">{t escape=off}Please go to the <a href="{url name=admin_system_settings}">Settings page</a> and configure your <br> translators for all the languages.{/t}</p>' +
              '</div>' +
              '<div ng-show="template.translating">' +
                '<div class="spinner-wrapper" class="text-center m-t-50 p-t-30">' +
                  '<div class="loading-spinner"></div>' +
                  '<h4 class="text-center">{t 1="[% template.config.locales[template.config.translateTo] %]"}Translating content into "%1"{/t}</h4>' +
                '</div>' +
              '</div>' +
              '<div ng-show="template.translation_done">' +
                '<div class="text-center m-t-50 p-t-30">' +
                  '<i class="fa fa-4x fa-globe"></i>' +
                  '<h4>{t 1="[% template.config.locales[template.config.translateTo] %]"}Content translated properly into "%1".{/t}</h4>' +
                '</div>' +
                '<button class="btn btn-success btn-block m-t-50" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">' +
                  '<h4 class="text-uppercase text-white">' +
                    '<i class="fa fa-check"></i>' +
                    '<strong>{t}Ok{/t}</strong>' +
                  '</h4>' +
                '</button>' +
              '</div>' +
            '</div>' +
          '</script>';
          },
          link: function($scope) {
            $scope.max           = 0;
            $scope.collapseWidth = 992;
            $scope.collapsed     = $window.innerWidth < $scope.collapseWidth;
            $scope.languages     = {};
            $scope.size          = Object.keys($scope.options.available).length;

            var getOption = function(name, value, main, translators, keys, item) {
              var option = {
                class: value === main ? 'info' : 'default',
                icon: item ? 'fa-plus' : '',
                name: name,
                translated: false,
                value: value,
              };

              if (item) {
                if (translators && translators.indexOf(value) !== -1) {
                  option.icon = 'fa-globe';
                }

                if (keys) {
                  for (var i = 0; i < keys.length; i++) {
                    if (!item[keys[i]]) {
                      continue;
                    }

                    if (angular.isString(item[keys[i]]) && value === main ||
                        angular.isObject(item[keys[i]]) && item[keys[i]][value]) {
                      option.icon       = 'fa-pencil';
                      option.translated = true;

                      return option;
                    }
                  }
                }
              }

              return option;
            };

            for (var lang in $scope.options.available) {
              $scope.languages[lang] = getOption(
                $scope.options.available[lang],
                lang,
                $scope.options.default,
                $scope.options.translators,
                $scope.keys,
                $scope.item
              );
            }

            /**
             * Change the current language.
             *
             * @param {String} The language value.
             */
            $scope.changeCurrentLanguage = function(e, language) {
              e.preventDefault();
              $scope.ngModel = language;
            };

            // Collapse directive when window width changes
            angular.element($window).bind('resize', function() {
              $scope.collapsed = $window.innerWidth < $scope.collapseWidth;

              $scope.$apply();
            });
          },
        };
      }
    ]);
})();
