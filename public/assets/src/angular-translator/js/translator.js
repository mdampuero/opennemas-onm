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
          editText: '@'
        },
        template: function(elem, attrs) {
          if (attrs.link ) {
            return '<div class=\"translator btn-group  btn-group-xs\" ng-if=\"optionsSize > 4\">' +
                '<button type=\"button\" class=\"form-control btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">' +
                  '<i class=\"fa {{options[ngModel].icon}}\"></i>' +
                  '{{editText}}' +
                  '<i class=\"fa fa-angle-down\"></i>' +
                '</button>' +
                '<ul class=\"dropdown-menu\" role=\"menu\">' +
                  '<li ng-repeat=\"language in options\" ng-if=\"language.language != ngModel\">' +
                    '<a href=\"{{link + \'?locale=\' + language.language}}\" >' +
                      '<i class=\"fa {{language.icon}}\" ng-show=\"language.icon\"></i>' +
                      '{{language.name}}' +
                    '</a>' +
                  '</li>' +
                '</ul>' +
              '</div>' +
              '<div class="translator btn-group btn-group-xs" role="group" ng-if=\"optionsSize < 5\">' +
                '<a ng-repeat=\"option in options\" href="{{link + \'?locale=\' + option.language}}" class=\"btn {{option.btnClass}}\">' +
                  '<i class=\"fa {{option.icon}}\" ng-show=\"option.icon\"></i>{{option.name}}' +
                '</a>' +
              '</div>';
          }
          return '<div class=\"translator btn-group\">' +
            '<button type=\"button\" class=\"form-control btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">' +
              '<i class=\"fa {{options[ngModel].icon}}\"></i>' +
              '{{options[ngModel].name}}' +
              '<i class=\"fa fa-angle-down\"></i>' +
            '</button>' +
            '<ul class=\"dropdown-menu\" role=\"menu\">' +
              '<li ng-repeat=\"language in options\" ng-if=\"language.language != ngModel\">' +
                '<a href=\"#\" ng-click=\"changeSelected(language.language)\">' +
                  '<i class=\"fa {{language.icon}}\" ng-show=\"language.icon\"></i>' +
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
              option.icon = 'fa-pencil';
              return option;
            }

            if(mainTranslationField[lang] && '' !== mainTranslationField[lang]) {
              option.btnClass = 'btn-default';
              option.icon = 'fa-pencil';
              return option;
            }

            if(
                translatorToMap &&
                (!mainTranslationField[lang] || '' === mainTranslationField[lang]) &&
                translatorToMap.indexOf(lang) > -1
            ) {
              option.btnClass = 'btn-default';
              option.icon = 'fa-globe';
              return option;
            }

            option.btnClass = 'btn-default';
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
              translationOptions[lang] = $scope.getTranslatorOption(lang,
                languageData.all[lang],
                languageData.translators,
                translatorItem,
                languageData.default
              );
            });
            return translationOptions;
          };

          $scope.options = $scope.getTranslatorOptions();
          $scope.optionsSize = Object.keys($scope.getTranslatorOptions()).length;
        },
      };
    }
  ]);
