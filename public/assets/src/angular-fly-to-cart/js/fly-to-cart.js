(function () {
  'use strict';

  angular.module('onm.flyToCart', [])
    /**
     * @ngdoc directive
     * @name  flyToCart
     *
     * @description
     *   Directive to animate an image from button to shopping cart.
     *
     * @example
     * <button class="fly-to-cart" type="button"><!-- Button content --></button>
     */
    .directive('flyToCart', [
      function() {
        return {
          restrict: 'C',
          scope: {},
          link: function link(scope, element) {
            element.on('click', function(){
              var cart = angular.element($('.shopping-cart'));

              var top = $(window).scrollTop();

              var target = cart.offset();

              var width = cart.prop('offsetWidth');
              var height = cart.prop('offsetHeight');

              var img = angular.element(element.parent().parent().parent()).find('img');
              var src = img.prop('currentSrc');

              var source = element.offset();

              img = angular.element(
                  '<img class="flying-item"src="' + src + '"/>');

              img.css({
                'top': source.top - top + 'px',
                'left': source.left + 25 + 'px',
              });

              $('body').append(img);

              setTimeout(function () {
                img.css({
                  'top': ((target.top - top + height / 2) - 15) +'px',
                  'left': ((target.left + width / 2) - 5) +'px',
                  'opacity': 1
                });
              }, 150);

              setTimeout(function () {
                img.css({
                  'opacity': 0,
                  'height': 0,
                  'width': 0,
                  'top': ((target.top - top + height / 2) + 15)+'px',
                  'left': ((target.left + width / 2) + 5)+'px',
                });
              }, 750);

              setTimeout(function () {
                img.remove();
              }, 1500);
            });
          }
        };
      }
    ]);
})();
