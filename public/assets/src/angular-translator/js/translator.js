'use strict';

angular.module('onm.translator', [])

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
    function () {
      return {
        restrict: 'E', // E = Element, A = Attribute, C = Class, M = Comment
        scope: {
          translatorOptions: '=',
          translatorItem: '=',
          ngModel: '=',
          link: '@',
        },
        template: function(elem, attrs) {
          if (attrs.link) {
            return '<div class="btn-group btn-group-xs" role="group">' +
                '<a ng-repeat=\"option in options\" href="{{link + \'?locale=\' + option.language}}" class=\"btn {{option.btnClass}}\">' +
                  '<span class=\"fa {{option.icon}} m-l-10\"></span>{{option.name}}' +
                '</a>' +
              '</div>';
          }
          return '<div class=\"btn-group\">' +
            '<button type=\"button\" class=\"form-control btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">' +
              '<span class=\"fa {{options[ngModel].icon}}\"></span>' +
              '{{options[ngModel].name}}' +
              '<span class=\"caret\"></span>' +
            '</button>' +
            '<ul class=\"dropdown-menu\" role=\"menu\">' +
              '<li ng-repeat=\"language in options\" ng-if=\"language.language != ngModel\">' +
                '<a href=\"#\" ng-click=\"changeSelected(language.language)\">' +
                  '<span class=\"fa {{language.icon}}\" ></span>' +
                  '{{language.name}}' +
                '</a>' +
              '</li>' +
            '</ul>' +
          '</div>';
        },

        link: function($scope, element, $attrs) {
          var link = $scope.link || null;
          $scope.languages = {};

          $scope.changeSelected = function (language) {
            $scope.ngModel = language;
          };

          $scope.getTranslatorOption = function(lang, languageName, translatorToMap, mainTranslationField, main) {
            var option = {
              default: main === lang,
              language: lang,
              name: languageName
            }

            if(lang === main) {
              option.btnClass = 'btn-primary';
              option.icon = 'fa-exchange';
              return option;
            }

            if(mainTranslationField[lang] && '' !== mainTranslationField[lang]) {
              option.btnClass = '';
              option.icon = 'fa-pencil';
              return option;
            }

            if(
                translatorToMap &&
                (!mainTranslationField[lang] || '' === mainTranslationField[lang]) &&
                translatorToMap.indexOf(lang) > -1
            ) {
              option.btnClass = 'btn-transparent';
              option.icon = 'fa-globe';
              return option;
            }

            option.btnClass = 'btn-transparent';
            option.icon = '';
            return option;
          };

          /**
           * Gets the options for the translator object
           */
          $scope.getTranslatorOptions = function() {
            var languageData = $scope.translatorOptions;
            var translatorItem = $scope.translatorItem;
            var translationOptions = {};
            Object.keys(languageData.all).forEach(function(lang) {
              translationOptions[lang] = $scope.getTranslatorOption(lang, languageData.all[lang], languageData.translators, translatorItem, languageData.default);
            });

            return translationOptions;
          };

          $scope.options = $scope.getTranslatorOptions();
        },
      };
    }
  ]);
