(function () {
  'use strict';

  angular.module('onm.flyToCart', [])
    .directive('flyToCart', [
      function() {
        return {
          restrict: 'C',
          transclude: true,
          replace: true,
          scope: {},
          template: '<button class="add-to-cart" ng-transclude></button>',
          link: function link(scope, element, attributes) {
            element.on('click', function(){
              var cartElem = angular.element($('.shopping-cart'));
              console.log(cartElem);

              var offset = cartElem.offset();

              var offsetTopCart = offset.top;
              var offsetLeftCart = offset.left;

              var widthCart = cartElem.prop('offsetWidth');
              var heightCart = cartElem.prop('offsetHeight');

              console.log(offsetTopCart, offsetLeftCart, widthCart, heightCart);

              var parentElem = angular.element(element.parent().parent());
              var imgElem = parentElem.find('img');
              console.log('img', imgElem);

              var offset2 = element.offset();
              var offsetLeft = offset2.left;
              var offsetTop = offset2.top;
              var imgSrc = imgElem.prop('currentSrc');
              console.log(offsetLeft + ' ' + offsetTop + ' ' + imgSrc);
              var imgClone = angular.element('<img src="' + imgSrc + '"/>');

              imgClone.css({
                'height': '50px',
                'position': 'fixed',
                'top': offsetTop + 'px',
                'left': offsetLeft + 25 + 'px',
                'opacity': 0.5,
                'z-index': 1000,
                'overflow': 'hidden',
                'width': '50px',
                'border-radius': '100%',
                'box-shadow': '0 0 5px #000'
              });

              imgClone.addClass('itemaddedanimate');
              parentElem.append(imgClone);

              setTimeout(function () {
                imgClone.css({
                  'top': ((offsetTopCart + heightCart / 2) - 15) +'px',
                  'left': ((offsetLeftCart + widthCart / 2) - 5) +'px',
                  'opacity': 1
                });
              }, 150);

              setTimeout(function () {
                imgClone.css({
                  'opacity': 0,
                  'height': 0,
                  'width': 0,
                  'top': ((offsetTopCart+heightCart/2) + 15)+'px',
                  'left': ((offsetLeftCart+widthCart/2) + 5)+'px',
                });
              }, 750);

              setTimeout(function () {
                imgClone.remove();
              }, 1500);
            });
          }
        };
      }
    ]);
})();
