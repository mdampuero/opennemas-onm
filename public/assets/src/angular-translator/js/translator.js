(function() {
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
                  '<li ng-repeat="language in languages" ng-if="language.value != ngModel">' +
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
                '<li ng-repeat="language in languages" ng-if="language.value != ngModel">' +
                  '<a href="#" ng-click="changeSelected($event, language.value)">' +
                    '<i class="fa {{language.icon}} m-r-5" ng-show="language.icon"></i>' +
                    '{{language.name}}' +
                  '</a>' +
                '</li>' +
              '</ul>' +
            '</div>';
          },
          link: function($scope, element) {
            $scope.max       = 4;
            $scope.collapsed = $window.innerWidth < 992;
            $scope.languages = {};
            $scope.size      = Object.keys($scope.options.available).length;

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
                      return option;
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
            $scope.changeSelected = function(e, language) {
              e.preventDefault();
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
