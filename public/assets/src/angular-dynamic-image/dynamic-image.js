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
            }
          );
        } else {
          $scope.src = dynamicImage.generateUrl($attrs['path'], $attrs['transform'], instanceMedia);
        }

        // Allowed attributes with this directive
        var allowedAttributes = [ 'class', 'height', 'width' ];

        var html = '<img ng-src="[% src %]" [attributes]>';

        var attributes = [];
        for (var i = 0; i < allowedAttributes.length; i++) {
          if ($attrs[allowedAttributes[i]]) {
            attributes.push(
              allowedAttributes[i] + '="' + $attrs[allowedAttributes[i]] + '"'
            );
          }
        };

        html = html.replace('[attributes]', attributes.join(' '));

        var e = $compile(html)($scope);
        $element.replaceWith(e);
      }
    };
  });

