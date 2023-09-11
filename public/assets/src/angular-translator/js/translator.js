(function() {
  'use strict';

  angular.module('onm.translator', [])

    .service('translator', [
      'http',
      function(http) {
        /**
         * @memberOf translator
         *
         * @description
         *  The translator service.
         */
        var translator = {};

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
          return strings.some(function(string) {
            return string && string.hasOwnProperty(locale) && string[locale];
          });
        };

        /**
         * @function translate
         * @memberOf translator
         *
         * @description
         *  Make the call to the translation service and return a promise.
         *
         * @param {Object} selectedTranslator The translator to perform the translation.
         *
         * @return {Promise} The thenable object with the call to the server.
         */
        translator.translate = function(selectedTranslator) {
          var strings = translator.extractStrings(translator.scope).map(function(string) {
            return string[selectedTranslator.from] ? string[selectedTranslator.from] : null;
          });

          var params = {
            data: strings,
            from: selectedTranslator.from,
            to: selectedTranslator.to,
            translator: 0
          };

          return http.post('api_v1_backend_tools_translate_string', params);
        };

        /**
         * @function getTranslatorItem
         * @memberOf translator
         *
         * @description
         *  Returns a translator object or null if no exists.
         *
         * @param {String} from The locale to translate from.
         * @param {String} to   The locale to translato to.
         *
         * @return {null|Object} The translator object or null.
         */
        translator.getTranslatorItem = function(from, to) {
          var strings = translator.extractStrings(translator.scope);

          var translators = translator.config.locale.translators.filter(function(item) {
            return item.to === to &&
              (
                translator.hasLocale(strings, item.from) ||
                from === translator.config.locale.default
              );
          });

          if (translators.length > 0) {
            return translators.shift();
          }

          return null;
        };

        /**
         * @function isTranslatable
         * @memberOf translator
         *
         * @description
         *  Return true if the strings can be translated, false otherwise.
         *
         * @param {String} locale The selected locale.
         *
         * @return {Boolean} True if the strings can be translated to the locale, false otherwise.
         */
        translator.isTranslatable = function(from, to) {
          if (!translator.extractStrings) {
            return false;
          }

          var strings = translator.extractStrings(translator.scope);

          if (translator.hasLocale(strings, to)) {
            return false;
          }

          var selectedTranslator = translator.getTranslatorItem(from, to);

          if (!selectedTranslator) {
            return false;
          }

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
            item:     '=',
            keys:     '=',
            link:     '@',
            ngModel:  '=',
            options:  '=',
            text:     '@',
            language: '='
          },
          template: function(elem, attrs) {
            if (attrs.link && attrs.language) {
              return '<a class="btn btn-white btn-small" href="{{link}}?locale={{language}}" uib-tooltip="{{text}}" tooltip-placement="top"> <i class="fa fa-pencil"></i></a>';
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

            var isTranslated = function(value, main, item) {
              return angular.isString(item) && value === main || angular.isObject(item) && item[value];
            };

            var getOptionForArray = function(keys, value, main, item, option) {
              for (var i = 0; i < keys.length; i++) {
                if (!item[keys[i]]) {
                  continue;
                }

                if (isTranslated(value, main, item[keys[i]])) {
                  return Object.assign({}, option, { icon: 'fa-pencil', translated: true });
                }
              }

              return option;
            };

            var getOptionForObject = function(keys, value, main, item, option) {
              for (var key in keys) {
                if (!item[key]) {
                  continue;
                }

                if (!Array.isArray(item[key]) && isTranslated(value, main, item[key][keys[key]])) {
                  return Object.assign({}, option, { icon: 'fa-pencil', translated: true });
                }

                for (var i = 0; i < item[key].length; i++) {
                  if (isTranslated(value, main, item[key][i][keys[key]])) {
                    return Object.assign({}, option, { icon: 'fa-pencil', translated: true });
                  }
                }
              }

              return option;
            };

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

                if (!keys) {
                  return option;
                }

                return Array.isArray(keys) ?
                  getOptionForArray(keys, value, main, item, option) :
                  getOptionForObject(keys, value, main, item, option);
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
      '$uibModalInstance', '$scope', 'template', 'callback', 'translator',
      function($uibModalInstance, $scope, template, callback, translator) {
        /**
         * @memberOf TranslatorCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = template;

        callback(template);

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

        /**
         * @function confirm
         * @memberOf TranslatorCtrl
         *
         * @description
         *  Confirm and executes the translation.
         */
        $scope.confirm = function() {
          template.confirm = false;
          template.translating = true;

          translator.translate(template.selectedTranslator).then(function(response) {
            translator.loadStrings(response.data, translator.scope, template.selectedTranslator.to);
            template.translating      = false;
            template.translation_done = true;
          }, function() {
            $uibModalInstance.close({ response: true, error: true });
          });
        };

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });
      }
    ]);
})();
