(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.gravatar
   *
   * @description
   *   The `onm.gravatar` module provides a directive to request user avatars
   *   to gravatar service.
   */
  angular.module('onm.gravatar', [])
    /**
     * @ngdoc service
     * @name  Gravatar
     *
     * @description
     *   Service to generate the gravatar URL from an email and a set of
     *   parameters.
     */
    .service('Gravatar', [
      function() {
        /**
         * @memberOf Gravatar
         *
         * @description
         *   Available values for gravatar.
         *
         * @type {Object}
         */
        this.available = {
          defaultImage: [
            404,         // HTTP 404 (File not found)
            'mm',        // A simple, cartoon-style silhouetted outline of a person
            'identicon', // A geometric pattern based on an email hash
            'monsterid', // A generated 'monster' with different colors, faces, etc
            'wavatar',   // Generated faces with differing features and backgrounds
            'retro',     // Awesome generated, 8-bit arcade-style pixelated faces
            'blank'      // Transparent PNG image
          ],
          rating: [ 'g', 'pg' ],
        };

        /**
         * @memberOf Gravatar
         *
         * @description
         *   Default values for gravatar.
         *
         * @type {Object}
         */
        this.defaults = {
          baseUrl: '//www.gravatar.com/avatar/',
          defaultImage: 'mm',
          rating: 'g',
          size: 32
        };

        /**
         * @function getDefaults
         * @memberOf Gravatar
         *
         * @description
         *   Returns the default gravatar values.
         *
         * @return {Object} Default gravatar values.
         */
        this.getDefaults = function() {
          return this.defaults;
        };

        /**
         * @function getUrl
         * @memberOf Gravatar
         *
         * @description
         *   Returns the gravatar URL given an email.
         *
         * @param {String} email The email.
         * @param {Object} size  The gravatar parameters.
         *
         * @return {String} The gravatar URL.
         */
        this.getUrl = function (email, params) {
          params = this.validate(params);

          return params.baseUrl + hex_md5(email.trim().toLowerCase()) +
            '?s=' + params.size +
            '&d=' + params.defaultImage +
            '&r=' + params.rating;
        };

        /**
         * @function validate
         * @memberOf Gravatar
         *
         * @description
         *   Validates the given parameters.
         *
         * @param {Object} params The parameters to validate.
         *
         * @return {Object} The validated parameters.
         */
        this.validate = function(params) {
          if (!params) {
            return angular.extend({}, this.defaults);
          }

          for (var key in this.defaults) {
            if (!params[key] || (this.available.hasOwnProperty(key) &&
                this.available[key].indexOf(params[key]) === -1)) {
              params[key] = this.defaults[key];
            }
          }

          return params;
        };

        return this;
      }
    ])

    /**
     * @ngdoc directive
     * @name  gravatar
     *
     * @requires $compile
     * @requires Gravatar
     *
     * @description
     *   Directive to request user's avatar to gravatar.
     *
     *  ###### Attributes:
     *  - **`default-image`**: The image to show if gravatar does not exists. (Optional)
     *  - **`ng-model`**: The string with email to use as gravatar. (Required)
     *  - **`rating`**: The gravatar rating parameter. (Optional)
     *  - **`size`**: The gravatar image size. (Optional)
     *
     * @example
     * <!-- Loads a 42x42 gravatar -->
     * <gravatar class="img-thumbnail" ng-model="user.email" size="42"></gravatar>
     */
    .directive('gravatar', ['$compile', 'Gravatar',
      function ($compile, Gravatar) {
        return {
            restrict: 'E',
            scope: {
              ngModel: '='
            },
            link: function ($scope, $element, $attrs) {
              $scope.$watch('ngModel', function(nv) {
                var html = '<img[attrs] src="[src]" />';
                var defaults = Gravatar.getDefaults();

                var attrs = '';
                var gravatarAttrs = {};
                for (var key in $attrs) {
                  if (key !== 'ngModel' && /(class|(ng([A-Z][a-x]*)+))/.test(key)) {
                    // Get class & angular attributes (ng-class, ng-show, ...)
                    var newKey = key.replace(/([A-Z]{1})/, '-$1'.toLowerCase());
                    attrs += ' ' + newKey + '="' + $attrs[key] + '"';
                  } else if (defaults[key]){
                    // Get gravatar attributes (size, defaultImage and rating)
                    gravatarAttrs[key] = $attrs[key];
                  }
                }

                var src = Gravatar.getUrl(nv, gravatarAttrs);

                html = html.replace(/\[attrs\]/g, attrs);
                html = html.replace(/\[src\]/g, src);

                var e = $compile(html)($scope);
                $element.replaceWith(e);
              });
            }
        };
      }
    ]);
})();
