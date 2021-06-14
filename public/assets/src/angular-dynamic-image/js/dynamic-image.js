(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.dynamicImage
   *
   * @requires swfobject
   * @requires onm.routing
   *
   * @description
   *   The `onm.dynamicImage` module provides a service and a directive to load
   *   images dynamically.
   */
  angular.module('onm.dynamicImage', [ 'swfobject', 'onm.routing' ])

    /**
     * @ngdoc provider
     * @name  DynamicImage
     *
     * @requires routing
     * @requires $http
     *
     * @description
     *   Service to load images from objects.
     */
    .service('DynamicImage', [
      'routing', 'http',
      function(routing, $http) {
        /**
         * Template for the dynamic image.
         *
         * @type {String}
         */
        var dynamicImageTpl = '<div class="dynamic-image-wrapper"[autoscale]>' +
          '<div [attributes][autoscale]>' +
            '<div class="dynamic-image-thumbnail[autoscaleClass]" ng-style="{ \'background-image\': \'url(\' + bg + \')\' }"></div>' +
          '</div>' +
          '[dimensions]' +
        '</div>';

        /**
         * Template for the dynamic image.
         *
         * @type {String}
         */
        var dynamicSwfTpl = '<div class="dynamic-image-wrapper[autoscaleClass]">' +
          '<swf-object [attributes] swf-params="{wmode: \'opaque\'}" swf-url="[% src %]" swf-width="[% width %]" swf-height="[% height %]"></swf-object>' +
          '<div class="swf-overlay"></div>' +
          '[dimensions]' +
        '</div>';

        /**
         * Allowed attributes with this directive.
         *
         * @memberof DynamicImage
         * @name     allowedAttributes
         * @type     {Array}
         */
        this.allowedAttributes = [ 'class', 'height', 'width' ];

        /**
         * Path to the default image.
         *
         * @memeberof DynamicImage
         * @name      brokenImage
         * @type      {String}
         */
        this.brokenImage = '/core/themes/admin/images/img-not-found.jpg';

        /**
         * Property with the path to the image.
         *
         * @memberof DynamicImage
         * @name     property
         * @type     {String}
         */
        this.property = 'path';

        /**
         * The image folder name.
         *
         * @memberof DynamicImage
         * @name     property
         * @type     {String}
         */
        this.imageFolder = '';

        /**
         * The route object to get a content.
         *
         * @memberof DynamicImage
         * @name     property
         * @type     {Object}
         */
        this.route = {
          name: 'api_v1_backend_content_get_item'
        };

        /**
         * @function generateUrl
         * @memberof DynamicImage
         *
         * @description
         *   Generates an URL for a given image.
         *
         * @param {Object}  image         The image to generate URL from.
         * @param {String}  transform     The transform parameters.
         * @param {String}  instanceMedia The instance media folder path.
         * @param {String}  property      The object property name.
         * @param {Boolean} raw           Whether to use image without prefix.
         * @param {Boolean} onlyImage     Whether to generate only for images.
         *
         * @return {String} The generated URL.
         */
        this.generateUrl = function(image, transform, instanceMedia, property, raw, onlyImage) {
          var prefix = '';

          if (!image) {
            return this.brokenImage;
          }

          if (typeof image === 'object') {
            if (property) {
              image = image[property];
            } else {
              image = image[this.property];
            }
          }

          if (!image) {
            return this.brokenImage;
          }

          if (!/^http/.test(image) && !raw) {
            prefix = instanceMedia + this.imageFolder;
          }

          if (onlyImage && /.*\.(swf)$/.test(image)) {
            return this.brokenImage;
          }

          if (!transform || /.*\.swf/.test(image)) {
            return prefix + image;
          }

          if (/^http/.test(image)) {
            return image;
          }

          return decodeURIComponent(routing.generate('asset_image', {
            path:   prefix + image,
            params: transform,
          }));
        };

        /**
         * @function getDefaultSize
         * @memberOf DynamicImage
         *
         * @description
         *   Returns the default width and height for the element.
         *
         * @param {Object} element The element.
         *
         * @return {Object} The width and height for element.
         */
        this.getDefaultSize = function(element) {
          return {
            height: element.parent().width(),
            width: element.parent().width()
          };
        };

        /**
         * @function getFeaturedFrontpage
         * @memberof DynamicImage
         *
         * @description
         *  Returns the related content id.
         *
         * @param {Object} item The item to get the related content id from.
         *
         * @return {int} The related content id.
         */
        this.getFeaturedFrontpage = function(item) {
          if (!Array.isArray(item.related_contents)) {
            return null;
          }

          var related = item.related_contents.filter(function(related) {
            return related.type === 'featured_frontpage';
          }).shift();

          if (!related) {
            return null;
          }

          return related.target_id;
        };

        /**
         * @function getItem
         * @memberof DynamicImage
         *
         * @description
         *  Returns the item to generate the url.
         *
         * @param {number|Object|string} The value to get the item from.
         *
         * @return {number|Object|Promise|string}
         *  The item or a promise wich will return the item.
         */
        this.getItem = function(item) {
          if (!item) {
            return null;
          }

          if (typeof item === 'string' ||
                item instanceof String ||
                item.content_type_name === 'photo') {
            return item;
          }

          if (item.content_type_name === 'video' &&
                item.hasOwnProperty('information') &&
                item.information.thumbnail) {
            return item.information.thumbnail;
          }

          if (Number.isFinite(item)) {
            this.route.params = { id: item };
            return $http.get(this.route).then(function(response) {
              return this.getItem(response.data.item);
            }.bind(this), function() {
              return null;
            });
          }

          var related = this.getFeaturedFrontpage(item);

          if (!related) {
            return null;
          }

          this.route.params = { id: related };

          return $http.get(this.route).then(function(response) {
            return this.getItem(response.data.item);
          }.bind(this), function() {
            return null;
          });
        };

        /**
         * @function getSettings
         * @memberof DynamicImage
         *
         * @description
         *   Returns the height and width basing on the available space.
         *
         * @param {Integer} height    The image original height.
         * @param {Integer} width     The image original width.
         * @param {Integer} maxHeight The available height.
         * @param {Integer} maxWidth  The available width.
         *
         * @return {Object} The height and width for the available space.
         */
        this.getSettings = function(height, width, maxHeight, maxWidth) {
          var w = maxWidth;
          var h = height * maxWidth / width;

          if (h > 250) {
            w = 250 * width / height;
            h = 250;
          }

          return { height: h, width: w };
        };

        /**
         * @function isFlash
         * @memberof DynamicImage
         *
         * @description
         *   Checks if the image is a flash object.
         *
         * @param {Object} image    The image to check.
         * @param {String} property The object property name.
         *
         * @return {boolean} True if the given image is a flash object. Otherwise,
         *                   returns false
         */
        this.isFlash = function(image, property) {
          if (!image) {
            return false;
          }

          if (typeof image === 'object') {
            if (property) {
              image = image[property];
            } else {
              image = image[this.property];
            }
          }

          return /.*\.swf/.test(image);
        };

        /**
         * @function render
         * @memberof DynamicImage
         *
         * @description
         *   Returns the HTML for the current image.
         *
         * @param Object options The image options.
         * @param Object model   The image object.
         *
         * @return String The HTML code for the current image.
         */
        this.render = function(options, model) {
          var html = dynamicImageTpl;

          if (this.isFlash(model) && options.onlyImage !== 'true') {
            html = dynamicSwfTpl;
          }

          var attributes = [];

          for (var i = 0; i < this.allowedAttributes.length; i++) {
            var name = this.allowedAttributes[i];
            var value = '';

            if (name === 'class') {
              value += 'dynamic-image-thumbnail-wrapper ';
            }

            if (options[name]) {
              attributes.push(name + '="' + value + options[name] + '"');
            }
          }

          var autoscale = '';
          var autoscaleClass = '';

          if (options.ngModel && options.autoscale && options.autoscale === 'true') {
            autoscale      = 'ng-style="{ \'height\': + settings.height, \'width\': + settings.width }"';
            autoscaleClass = ' autoscale';
          }

          if (options.ngModel && options.reescale && options.reescale === 'true') {
            autoscale      = 'ng-style="{ \'height\': height, \'width\': width }"';
            autoscaleClass = ' autoscale';
          }

          var dimensions = '';

          if (options.ngModel && options.dimensions && options.dimensions === 'true') {
            dimensions = '<div class="dynamic-image-dimensions-overlay" ng-if="!loading">' +
              '<span class="dynamic-image-dimensions-label">' +
                '[% ngModel.width %]x[% ngModel.height %]' +
              '</span>' +
            '</div>';
          }

          html = html.replace(/\[attributes\]/g, attributes.join(' '));
          html = html.replace(/\[dimensions\]/g, dimensions);
          html = html.replace(/\[autoscale\]/g, autoscale);
          html = html.replace(/\[autoscaleClass\]/g, autoscaleClass);

          return html;
        };

        /**
         * @function setProperty
         * @memberof DynamicImage
         *
         * @description
         *   Sets the name of the object property with the image path.
         *
         * @param String property The object property name.
         */
        this.setProperty = function(property) {
          this.property = property;
        };

        /**
         * @function $get
         * @memberof DynamicImage
         *
         * @description
         *   Returns the current service.
         *
         * @return object The current service.
         */
        this.$get = function() {
          return this;
        };
      }
    ])

    /**
     * @ngdoc directive
     * @name  dynamicImage
     *
     * @requires $compile
     * @requires DynamicImage
     *
     * @description
     *   Directive to load images dynamically from a given source.
     *
     *  ###### Attributes:
     *  - **`autoescale`**: Creates an overlay with the image dimensions. (Optional)
     *  - **`instance`**: The name of the instance where photo is from.
     *    - ***Required*** for internal resources.
     *    - ***Optional*** (ignored) for external resources.
     *  - **`ng-model`**: The object or string to load dynamically. (Required)
     *  - **`transform`**: The transformation to apply to the image. (Optional)
     *
     * @example
     * <!-- Load an internal image from an object with autoscale -->
     * <dynamic-image autoscale="true" class="img-thumbnail" instance="c-default" ng-model="photo">
     * </dynamic-image>
     * @example
     * <!-- Load an image dynamically from an external source with an overlay for its dimensions -->
     * <dynamic-image ng-model="http://www.example.com/sample-image.jpg" dimensions="true">
     * </dynamic-image>
     */
    .directive('dynamicImage', [
      '$compile', 'DynamicImage', '$q',
      function($compile, DynamicImage, $q) {
        return {
          restrict: 'AE',
          scope: {
            ngModel: '='
          },
          link: function($scope, element, attrs) {
            var children = element.children();
            var html     = DynamicImage.render(attrs, $scope.ngModel);
            var defaults = DynamicImage.getDefaultSize(element);

            $scope.onlyImage = attrs.onlyImage === 'true';
            $scope.height    = defaults.height;
            $scope.width     = defaults.width;

            var maxHeight = element.height();
            var maxWidth  = element.width();

            if (!maxWidth || !maxHeight) {
              maxHeight = element.parent().height();
              maxWidth  = element.parent().width();
            }

            if (DynamicImage.isFlash && $scope.onlyImage) {
              // Try to calculate height and width before compiling for flash
              if ($scope.ngModel && $scope.ngModel.height &&
                  $scope.ngModel.width) {
                var settings = DynamicImage.getSettings($scope.ngModel.height,
                  $scope.ngModel.width, maxHeight, maxWidth);

                $scope.height = settings.height;
                $scope.width  = settings.width;
              }
            }

            // Add watcher to update src when scope changes
            $scope.$watch('ngModel', function(nv) {
              $q.when(DynamicImage.getItem(nv), function(item) {
                if (attrs.reescale === 'true') {
                  $scope.height = item.height;
                  $scope.width  = item.width;

                  if (item.width > 354) {
                    var ratio = 354 / item.width;

                    $scope.height = item.height * ratio;
                    $scope.width  = item.width * ratio;
                  }
                }

                $scope.src = DynamicImage.generateUrl(item, attrs.transform,
                  attrs.instance, attrs.property, attrs.raw, $scope.onlyImage);
              });
            });

            var img = new Image();

            img.onload = function() {
              $scope.bg = this.src;
              $scope.loading = false;

              $scope.settings = DynamicImage.getSettings(this.height,
                this.width, maxHeight, maxWidth);

              $scope.$apply();
            };

            $scope.$watch('src', function(nv) {
              if (!nv) {
                return;
              }

              if (!DynamicImage.isFlash(nv) || $scope.onlyImage) {
                $scope.loading = true;
                img.src        = nv;
              }
            });

            var e = $compile(html)($scope);

            e.append(children);
            element.replaceWith(e);
          }
        };
      }
    ]);
})();
