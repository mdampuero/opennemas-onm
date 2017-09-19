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
  .directive('translator', ['$http', 'routing',
    function ($compile, $http, routing) {
      return {
        restrict:   'E', // E = Element, A = Attribute, C = Class, M = Comment
        require:    'ngModel',
        scope: {
          'languageData': '=',
          ngModel: '=',
          'link': '@',
        },
        template: function(elem, attrs) {
          if(attrs.link) {
            return '<a ng-repeat=\"language in languages\" href="{{language.link}}" class=\"{{language.btnClass}}\">' +
                '<span class=\"fa {{language.icon}} m-l-10\"></span>{{language.language}}' +
              '</a>';
          }
          return '<div class=\"btn-group\">' +
            '<button type=\"button\" class=\"form-control btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">' +
              '<span class=\"fa {{selected.icon}}\"></span>' +
              '{{selected.language}}' +
              '<span class=\"caret\"></span>' +
            '</button>' +
            '<ul class=\"dropdown-menu\" role=\"menu\">' +
              '<li ng-repeat=\"language in languages\" ng-if=\"language.language != locale\">' +
                '<a href=\"#\" data-ng-click=\"changeSelected(language.language)\">' +
                  '<span class=\"fa {{language.icon}}\" ></span>' +
                  '{{language.language}}' +
                '</a>' +
              '</li>' +
            '</ul>' +
          '</div>';
        },
        link: function($scope, element, $attrs, ctrl) {
          var data = $scope.languageData;
          var link = $scope.link || null;
          $scope.locale = data.locale || data.default;
          $scope.languages = {};
          if(data.all) {
            Object.keys(data.all).forEach(function(language) {
              var icon = (!link && language == data.default)?'fa-exchange':'';
              $scope.languages[language] = {'icon': icon, 'language': language};
              if(link) {
                $scope.languages[language].link = $attrs.link + '?locale=' + language;
                $scope.languages[language].btnClass = 'btn ';
              }
              if(language == $scope.locale) {
                $scope.selected = $scope.languages[language];
              }
            });
          }
          $scope.changeSelected = function (language) {
            $scope.locale = language;
            $scope.ngModel = language;
            $scope.selected = $scope.languages[language];
            ctrl.$setViewValue(language);
          }
        },
      };
    }
  ]);
