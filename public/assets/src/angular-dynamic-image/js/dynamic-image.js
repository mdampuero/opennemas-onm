'use strict';

/**
 * Module to load images dynamically.
 */
 angular.module('onm.dynamicImage', ['swfobject', 'onm.routing'])
  /**
   * Service to load images from objects.
   *
   * @param Object routingProvider The routing service.
   */
  .provider('dynamicImage', [ 'routingProvider', function(routingProvider) {
    /**
     * Template for the dynamic image.
     *
     * @type string
     */
    var dynamicImageTpl = '<div class="dynamic-image-wrapper[autoscaleClass]">' +
      '<img ng-class="{ loading: loading }" ng-src="[% src %]" [attributes][autoscale]>' +
      '<div class="dynamic-image-loading-overlay" ng-if="loading">' +
        '<i class="fa fa-circle-o-notch fa-spin fa-2x"></i>' +
      '</div>' +
      '[dimensions]' +
    '</div>';

    /**
     * Template for the dynamic image.
     *
     * @type string
     */
    var dynamicSwfTpl = '<div class="dynamic-image-wrapper[autoscaleClass]">' +
      '<swf-object [attributes] swf-params="{wmode: \'opaque\'}" swf-url="[% src %]" swf-width="[% width %]" swf-height="[% height %]"></swf-object>' +
      '<div class="swf-overlay"></div>' +
      '[dimensions]' +
    '</div>';

    /**
     * Allowed attributes with this directive.
     *
     * @type array
     */
    this.allowedAttributes = ['class', 'height', 'width'];

    /**
     * Property with the path to the image.
     *
     * @type string
     */
    this.property = 'path_img';

    /**
     * The image folder name.
     *
     * @type string
     */
    this.imageFolder = 'images';

    /**
     * Generates an URL for a given image.
     *
     * @param object image         The image to generate URL from.
     * @param string transform     The transform parameters.
     * @param string instanceMedia The instance media folder path.
     * @param string property      The object property name.
     *
     * @return string The generated URL.
     */
    this.generateUrl = function(image, transform, instanceMedia, property) {
      var prefix = '';

      if (!image) {
        return '';
      }

      if (typeof image === 'object') {
        if (property) {
          image = image[property];
        } else {
          image = image[this.property];
        }
      }

      if (!/^http/.test(image)) {
        if (!instanceMedia) {
          throw 'Invalid instance media folder path';
        }

        prefix = instanceMedia + this.imageFolder;
      }

      if (!transform || /.*\.swf/.test(image)) {
        return prefix + image;
      }

      return routingProvider.generate(
        'asset_image',
        {
          'real_path':  prefix + image,
          'parameters': encodeURIComponent(transform),
        }
      );
    };

    /**
     * Returns the height and width basing on the available space.
     *
     * @param integer height    The image original height.
     * @param integer width     The image original width.
     * @param integer maxHeight The available height.
     * @param integer maxWidth  The available width.
     *
     * @return Object The height and width for the available space.
     */
    this.getSettings = function(height, width, maxHeight, maxWidth) {
      var h = maxHeight;
      var w = (width * maxHeight) / height;

      if (w > maxWidth) {
        w = maxWidth;
        h = (height * maxWidth) / width;
      }

      return { height: h, width: w };
    };

    /**
     * Checks if the image is a flash object.
     *
     * @param object image    The image to check.
     * @param string property The object property name.
     *
     * @return boolean True if the given image is a flash object. Otherwise,
     *                 returns false
     */
    this.isFlash = function(image, property) {
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
     * Returns the HTML for the current image.
     *
     * @param Object options The image options.
     * @param Object model   The image object.
     *
     * @return string The HTML code for the current image.
     */
    this.render = function(options, model) {
      var html = dynamicImageTpl;

      if (this.isFlash(model)) {
        html = dynamicSwfTpl;
      }

      var attributes = [];
      for (var i = 0; i < this.allowedAttributes.length; i++) {
        var name = this.allowedAttributes[i];

        if (options[name]) {
          attributes.push(name + '="' + options[name] + '"');
        }
      }

      var autoscale = '';
      var autoscaleClass = '';
      if (options.ngModel && options.autoscale && options.autoscale === 'true') {
        autoscale      = 'style="height: [% height %]px; width: [% width %]px;"';
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
     * Sets the name of the object property with the image path.
     *
     * @param string property The object property name.
     */
    this.setProperty = function(property) {
      this.property = property;
    };

    /**
     * Returns the current service.
     *
     * @return object The current service.
     */
    this.$get = function () {
      return this;
    };
  }])

  /**
   * Directive to load images dynamically from a given source.
   *
   * @param Object $compile     The compile service.
   * @param Object dynamicImage The dynamicImage service.
   */
  .directive('dynamicImage', ['$compile', 'dynamicImage',
    function ($compile, dynamicImage) {
    return {
      restrict: 'AE',
      scope: {
        'ngModel': '='
      },
      link: function ($scope, element, attrs) {
        var maxHeight = element.height();
        var maxWidth  = element.width();

        if (!maxWidth || !maxHeight) {
          maxHeight = element.parent().height();
          maxWidth  = element.parent().width();
        }

        var children  = element.children();
        var html      = dynamicImage.render(attrs, $scope.ngModel);

        // Try to calculate height and width before compiling for flash
        if ($scope.ngModel && dynamicImage.isFlash($scope.ngModel) &&
            $scope.ngModel.height && $scope.ngModel.width) {

          var settings = dynamicImage.getSettings($scope.ngModel.height,
            $scope.ngModel.width, maxHeight, maxWidth);

          $scope.height = settings.height;
          $scope.width  = settings.width;
        }

        if (attrs.ngModel) {
          // Add watcher to update src when scope changes
          $scope.$watch('ngModel', function(nv) {
            $scope.src = dynamicImage.generateUrl(nv, attrs.transform,
              attrs.instance, attrs.property);
          });
        } else {
          $scope.src = dynamicImage.generateUrl(attrs.path, attrs.transform,
            attrs.instance, attrs.property);
        }

        $scope.$watch('src', function(nv) {
          if (!dynamicImage.isFlash(nv)) {
            $scope.loading = true;
          }
        });

        var e = $compile(html)($scope);

        // Remove loading spinner and scale image on load
        e.find('img').bind('load', function() {
          $scope.loading = false;

          if (attrs.autoscale && attrs.autoscale === 'true') {
            var image = new Image();
            image.src = $scope.src;

            var h = image.height;
            var w = image.width;

            if ($scope.ngModel.height && $scope.ngModel.width) {
              h = $scope.ngModel.height;
              w = $scope.ngModel.width;
            }

            var settings = dynamicImage.getSettings(h, w, maxHeight, maxWidth);

            $scope.height = settings.height;
            $scope.width  = settings.width;
          }

          $scope.$apply();
        });

        e.append(children);
        element.replaceWith(e);
      }
    };
    }
  ]);
