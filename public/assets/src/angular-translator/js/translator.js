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
         */
        var translator = {
          stringsToTranslate: []
        };

        /**
         * @function init
         * @memberOf translator
         *
         * @description
         *  Initializes the translator.
         */
        translator.init = function(scope) {
          translator.scope          = scope;
          translator.extractStrings = scope.extractStrings;
          translator.loadStrings    = scope.loadStrings;
          translator.config         = scope.config;

          // Define watcher to execute translation when locale changes
          translator.scope.$watch('config.locale.selected', function(nv, ov) {
            if (!nv) {
              return;
            }

            if (nv === ov) {
              return;
            }

            if (translator.valid(nv)) {
              // Raise a modal to indicate that background translation is being executed
              $uibModal.open({
                backdrop: 'static',
                keyboard: false,
                backdropClass: 'modal-backdrop-dark',
                controller:  'TranslatorCtrl',
                openedClass: 'modal-relative-open',
                templateUrl: 'modal-translate',
                resolve: {
                  template: function() {
                    return {
                      config: translator.config,
                      translating: false,
                      translator: translator
                    };
                  },
                  callback: function() {
                    return function(modal, template) {
                      template.translating = true;

                      translator.translate().then(function(response) {
                        translator.loadStrings(response.data, translator.scope, translator.locale);
                        template.translating      = false;
                        template.translation_done = true;
                      }, function() {
                        modal.close({ response: true, error: true });
                      });
                    };
                  }
                }
              });
            }
          }, true);
        };

        /**
         * @function hasLocale
         * @memberOf translator
         *
         * @description
         *  Check if an array of strings has the specific locale.
         *
         * @param {array}  strings An array of objects with the strings to check.
         * @param {String} locale  The locale to check.
         *
         * @return {Boolean} True if the string has the locale, false otherwise.
         */
        translator.hasLocale = function(strings, locale) {
          return strings.some((string) => string && string !== {} && string.hasOwnProperty(locale));
        };

        /**
         * @function translate
         * @memberOf translator
         *
         * @description
         *  Make the call to the translation service and return a promise.
         *
         * @return {Promise} The thenable object with the call to the server.
         */
        translator.translate = function() {
          var params = {
            data: this.stringsToTranslate.map((string) => string[this.selectedTranslator.from]),
            from: this.selectedTranslator.from,
            to: this.selectedTranslator.to,
            translator: 0
          };

          return http.post('api_v1_backend_tools_translate_string', params);
        };

        /**
         * @function valid
         * @memberOf translator
         *
         * @description
         *  Return true if the strings can be translated, false otherwise.
         *
         * @param {String} locale The selected locale.
         *
         * @return {Boolean} True if the strings can be translated to the locale, false otherwise.
         */
        translator.valid = function(locale) {
          if (!this.extractStrings) {
            return false;
          }

          this.locale             = locale;
          this.stringsToTranslate = this.extractStrings(this.scope);

          if (this.hasLocale(this.stringsToTranslate, locale)) {
            return false;
          }

          var translators = this.config.locale.translators.filter(function(translator) {
            return translator.to === locale && this.hasLocale(this.stringsToTranslate, translator.from);
          }.bind(this));

          if (translators.length === 0) {
            return false;
          }

          this.selectedTranslator = translators.shift();

          return true;
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
              '</div>';
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
            '</div>';
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
    ])

    .controller('TranslatorCtrl', [
      '$uibModalInstance', '$scope', 'template', 'callback',
      function($uibModalInstance, $scope, template, callback) {
        /**
         * @memberOf TranslatorCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = template;

        callback($uibModalInstance, template);

        /**
         * @function dismiss
         * @memberOf TranslatorCtrl
         *
         * @description
         *   Close the modal without executing any action.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
        };

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });
      }
    ]);
})();
