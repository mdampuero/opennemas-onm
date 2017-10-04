(function () {
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
    .directive('translator', [ '$window',
      function ($window) {
        return {
          restrict: 'E', // E = Element, A = Attribute, C = Class, M = Comment
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
              return '<div class=\"translator btn-group btn-group-xs\" ng-if=\"collapsed || size > 4\">' +
                '<button type=\"button\" class=\"form-control btn btn-default dropdown-toggle\" data-toggle=\"dropdown\">' +
                  '<i class=\"fa fa-pencil\"></i>' +
                  '{{text}}' +
                  '<i class=\"fa fa-angle-down\"></i>' +
                '</button>' +
                '<ul class=\"dropdown-menu\" role=\"menu\">' +
                  '<li ng-repeat=\"language in languages\" ng-if=\"language.value != ngModel\">' +
                    '<a href=\"{{link + \'?locale=\' + language.value}}\" >' +
                      '<i class=\"fa {{language.icon}}\" ng-show=\"language.icon\"></i>' +
                      '{{language.name}}' +
                    '</a>' +
                  '</li>' +
                '</ul>' +
              '</div>' +
              '<div class="translator btn-group" role="group" ng-if=\"!collapsed && size < 5\">' +
                '<a ng-repeat=\"language in languages\" href="{{link + \'?locale=\' + language.value}}" class=\"btn {{language.class}}\">' +
                  '<i class=\"fa {{language.icon}}\" ng-show=\"language.icon\"></i>{{language.name}}' +
                '</a>' +
              '</div>';
            }

            return '<div class=\"translator btn-group\">' +
              '<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\">' +
                '<i class=\"fa {{languages[ngModel].icon}}\" ng-show=\"languages[ngModel].icon\"></i>' +
                '{{languages[ngModel].name}}' +
                '<i class=\"fa fa-angle-down\"></i>' +
              '</button>' +
              '<ul class=\"dropdown-menu\" role=\"menu\">' +
                '<li ng-repeat=\"language in languages\" ng-if=\"language.value != ngModel\">' +
                  '<a href=\"#\" ng-click=\"changeSelected(language.value)\">' +
                    '<i class=\"fa {{language.icon}}\" ng-show=\"language.icon\"></i>' +
                    '{{language.name}}' +
                  '</a>' +
                '</li>' +
              '</ul>' +
            '</div>';
          },
          link: function($scope) {
            $scope.collapsed = $window.innerWidth < 992;
            $scope.languages = {};
            $scope.size      = Object.keys($scope.options.available).length;

            var getOption = function(name, value, main, translators, keys, item) {
              var option = {
                class: value === main ? 'btn-primary' : 'btn-default',
                icon:  item ? 'fa-plus' : '',
                value: value,
                name:  name
              };

              if (item) {
                if (translators && translators.indexOf(value) !== -1) {
                  option.icon = 'fa-globe';
                }

                if (keys) {
                  for (var i = 0; i < keys.length; i++) {
                    if (item[keys[i]] && angular.isObject(item[keys[i]]) &&
                      item[keys[i]][value]) {
                      option.icon = 'fa-pencil';
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
            $scope.changeSelected = function(language) {
              $scope.ngModel = language;
            };

            // Collapse directive when window width changes
            angular.element($window).bind('resize', function() {
              $scope.collapsed = $window.innerWidth < 992;

              $scope.$apply();
            });
          },
        };
      }
    ]);
})();
