(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  InnerCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     *
     * @description
     *   This is the base controller for forms when no full SPA edition
     *   implemented.
     */
    .controller('InnerCtrl', [
      '$controller', '$scope', 'http', 'messenger',
      function($controller, $scope, http, messenger) {
        $.extend(this, $controller('BaseCtrl', { $scope: $scope }));

        /**
         * @function generate
         * @memberOf InnerCtrl
         *
         * @description
         *   Forces automatic field generation.
         */
        $scope.generate = function() {
          $scope.flags.generate = { slug: true, tags: true };
        };

        /**
         * @function generateTagsFrom
         * @memberOf InnerCtrl
         *
         * @description
         *   Returns a string to use when clicking on "Generate" button for
         *   tags component.
         *
         * @return {String} The string to generate tags from.
         */
        $scope.generateTagsFrom = function() {
          return $scope.title;
        };

        /**
         * @function submit
         * @memberOf InnerCtrl
         *
         * @description
         *   Saves tags and, then, submits the form.
         */
        $scope.submit = function(e) {
          e.preventDefault();

          if ($scope.form.$invalid) {
            $('[name=form]')[0].reportValidity();

            messenger.post(window.strings.forms.not_valid, 'error');

            return false;
          }

          if (!$('[name=form]')[0].checkValidity()) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          $scope.$broadcast('onmTagsInput.save', {
            onSuccess: function(ids) {
              $('[name=tags]').val(JSON.stringify(ids));
              $('[name=form]').submit();
            }
          });
        };

        // Initialize the scope with the input/select values.
        $('input, select, textarea').each(function(index, element) {
          var name = $(element).attr('name');
          var value = $(element).val();

          if ($(element).attr('type') === 'number') {
            value = parseInt(value);
          }

          $scope[name] = value;
        });
      }
    ]);
})();
