(function () {
  'use strict';
  angular.module('onm.imagePreview', [])
    /**
     * @ngdoc directive
     * @name  ngPreview
     *
     * @requires $window
     *
     * @description
     *   Previews an image on client side.
     */
    .directive('ngPreview', ['$window', function($window) {
      var helper = {
        support: !!($window.FileReader && $window.CanvasRenderingContext2D),
        isFile: function(item) {
          return angular.isObject(item) && item instanceof $window.File;
        },
        isImage: function(file) {
          var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
          return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
      };

      return {
        restrict: 'A',
        scope:{
          ngPreview: '='
        },
        link: function(scope, element) {
          if (!helper.support) {
            return;
          }

          element.prepend('<canvas/>');
          scope.$watch('ngPreview', function(nv, ov) {
            var canvas = element.find('canvas');

            function onLoadFile(event) {
              var img = new Image();
              img.onload = onLoadImage;
              img.src = event.target.result;
            }

            function onLoadImage() {
              var h = element.parent().height();
              var w = this.width * element.parent().height() / this.height;

              if (element.parent().width() > 0 && w > element.parent().width()) {
                w = element.parent().width();
                h = this.height * element.parent().width() / this.width;
              }

              canvas.attr({ width: w, height: h });
              canvas[0].getContext('2d').drawImage(this, 0, 0, w, h);
            }

            if (!nv) {
              canvas[0].getContext('2d').clearRect(
                  0, 0, canvas[0].width, canvas[0].height);
              canvas.attr({ width: 0, height: 0 });
              return;
            }

            if (typeof nv !== 'string') {
              var reader = new FileReader();
              reader.onload = onLoadFile ;
              reader.readAsDataURL(nv);

              return;
            }

            var img = new Image();
            img.onload = onLoadImage;
            img.src = nv;
          });
        }
      };
    }]);
})();


