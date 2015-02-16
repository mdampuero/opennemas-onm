/**
 * Directive to generate a dynamic image.
 */
 angular.module('onm.dynamicImage', ['onm.routing'])
  .provider('dynamicImage', [ 'routingProvider', function(routingProvider) {
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
    this.imageFolder = 'images'

    /**
     * Generates an URL for a given image.
     *
     * @param object image         The image to generate URL from.
     * @param string transform     The transform parameters.
     * @param string instanceMedia The instance media folder path.
     *
     * @return string The generated URL.
     */
    this.generateUrl = function(image, transform, instanceMedia) {
      var prefix = '';

      if (typeof image == 'object') {
        image = image[this.property];
      }

      if (!image.match('@http://@')) {
        if (!instanceMedia) {
          throw 'Invalid instance media folder path';
        }

        prefix = instanceMedia + this.imageFolder;
      }

      return routingProvider.generate(
        'asset_image',
        {
          'real_path':  prefix + image,
          'parameters': encodeURIComponent(transform),
        }
      );
    }

    /**
     * Returns the height and width basing on the available space.
     *
     * @param Object image The image object.
     *
     * @return Object The height and width for the available space.
     */
    this.getSettings = function(image) {
      var oH = $('.dynamic-image-wrapper.autoscale').parent().height();
      var oW = $('.dynamic-image-wrapper.autoscale').parent().width();
      var m = 0;

      if (parseInt(image.height) > parseInt(image.width)) {
        var w = (image.width * oH) / image.height;

        return {
          height: oH,
          width: w
        }
      }

      var h = (image.height * oW) / image.width;

      return {
        height: h,
        width:  oW
      }
    }

    /**
     * Sets the name of the object property with the image path.
     *
     * @param string property The object property name.
     */
    this.setProperty = function(property) {
      this.property = property;
    }

    /**
     * Returns the current service.
     *
     * @return object The current service.
     */
    this.$get = function () {
      return this;
    }
  }])
  .directive('dynamicImage', function ($compile, dynamicImage) {
    return {
      restrict: 'AE',
      scope: {
        'ngModel': '='
      },
      link: function ($scope, $element, $attrs) {
        if ($attrs['ngModel']) {
          // Add watcher to update src when scope changes
          $scope.$watch(
            function() {
              return $scope.ngModel;
            },
            function(nv, ov) {
              $scope.src = dynamicImage.generateUrl(nv, $attrs['transform'], instanceMedia);
              if ($attrs['autoscale'] && $attrs['autoscale'] == 'true') {
                var settings = dynamicImage.getSettings(nv);
                $scope.height = settings.height;
                $scope.width  = settings.width;
              }
            }
          );
        } else {
          $scope.src = dynamicImage.generateUrl($attrs['path'], $attrs['transform'], instanceMedia);
        }

        // Allowed attributes with this directive
        var allowedAttributes = [ 'class', 'height', 'width' ];

        var html = '<div class="dynamic-image-wrapper[autoscaleClass]">\
          <img ng-class="{ loading: loading }" ng-src="[% src %]" [attributes][autoscale]>\
            <div class="dynamic-image-loading-overlay" ng-if="loading">\
              <i class="fa fa-circle-o-notch fa-spin fa-2x"></i>\
            </div>\
          [dimensions]\
        </div>';

        var attributes = [];
        for (var i = 0; i < allowedAttributes.length; i++) {
          if ($attrs[allowedAttributes[i]]) {
            attributes.push(
              allowedAttributes[i] + '="' + $attrs[allowedAttributes[i]] + '"'
            );
          }
        };

        var autoscale = '';
        var autoscaleClass = '';
        if ($attrs['ngModel']
            && $attrs['autoscale'] && $attrs['autoscale'] == 'true') {
          autoscale      = 'style="height: [% height %]px; width: [% width %]px;"';
          autoscaleClass = ' autoscale';
        }

        var dimensions = '';
        if ($attrs['ngModel'] && $attrs['dimensions']) {
          dimensions = "<div class=\"dynamic-image-dimensions-overlay\" ng-if=\"!loading\">\
            <span class=\"dynamic-image-dimensions-label\">\
              [% ngModel.width %]x[% ngModel.height %]\
            </span>\
          </div>";
        }

        html = html.replace('[attributes]', attributes.join(' '));
        html = html.replace('[dimensions]', dimensions);
        html = html.replace('[autoscale]', autoscale);
        html = html.replace('[autoscaleClass]', autoscaleClass);

        var e = $compile(html)($scope);
        e.find('img').bind('load', function(event) {
          $scope.loading = false;
          $scope.$apply();
        });

        $element.replaceWith(e);

        $scope.$watch('src', function(nv, ov) {
          $scope.loading = true;
        });
      }
    };
  });

