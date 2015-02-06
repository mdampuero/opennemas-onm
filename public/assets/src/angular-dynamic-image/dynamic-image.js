/**
 * Directive to generate a dynamic image.
 */
 angular.module('onm.dynamicImage', [])
  .directive('dynamicImage', function ($compile, routing) {
    return {
      restrict: 'AE',
      link: function ($scope, $element, $attrs) {
        var baseUrl;
        var imgClass = '';
        var html = '<img [class] ng-src="[% src %]" [% extra_parameters %]>';
        var src  = $attrs['path'];
        var extra_parameters = '';

        if ($attrs['class']) {
          imgClass = 'class="' + $attrs['class'] + '"';
        }

        html = html.replace('[class]', imgClass);

        if (src.match('@http://@')) {
          baseUrl = '';
        } else if (!$attrs['base_url']) {
          baseUrl = $attrs['instance'] + 'images';
        } else {
          baseUrl = $attrs['base_url'] + '/';
        }

        var resource = baseUrl + src;
        resource.replace('@(?<!:)//@', '/');

        if ($attrs['transform']) {
          var params = {
            'real_path':  baseUrl + src,
            'parameters': encodeURIComponent($attrs['transform']),
          };

          resource = routing.generate('asset_image', params);
        } else {
          resource = baseUrl + src;
        }

        delete $attrs['src'];
        delete $attrs['base_url'];
        delete $attrs['transform'];

        angular.forEach($attrs, function(value, key){
          extra_parameters = extra_parameters + ' ' + key + '="'+value+'"';
        });

        resource = resource.replace(/[/]+/g, "/");
        $scope.src = resource;
        $scope.extra_parameters = extra_parameters;

        // Compile template and replace elements
        var e = $compile(html)($scope);
        $element.replaceWith(e);
      }
    };
  });

