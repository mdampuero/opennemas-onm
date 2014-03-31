angular.module('BackendApp.directives')
    .directive('dynamicImage', function ($compile, fosJsRouting) {
        return {
            restrict: 'AE',
            link: function ($scope, $element, $attrs) {
                var baseUrl;
                var html = '<img ng-src="[% src %]">';
                var src  = $attrs['path'];

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

                    resource = fosJsRouting.generate('asset_image', params);
                } else {
                    resource = baseUrl + src;
                }

                resource = resource.replace(/[/]+/g, "/");
                $scope.src = resource;

                // Compile template and replace elements
                var e = $compile(html)($scope);
                $element.replaceWith(e);
            }
        };
    });

