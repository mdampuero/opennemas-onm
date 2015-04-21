(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.renderer
   *
   * @requires require
   *
   * @description
   *   The `onm.renderer` module provides a service to render HTML elements from
   *   objects.
   */
  angular.module('onm.renderer', [])
    /**
     * @ngdoc service
     * @name  Renderer
     *
     * @description
     *   Service to render HTML elements from objects.
     */
    .service('Renderer', [
      function() {
        /**
         * @function renderImage
         * @memberOf Renderer
         *
         * @description
         *   Returns the HTML code to insert in a text for an image.
         *
         * @param {Object} image The image to render.
         * @param {String} align Image alignment.
         *
         * @return {String} The HTML code.
         */
        this.renderImage = function(image, align) {
          var alt   = '';
          var align = '';
          var html  = '<img[align] src="' + instanceMedia + 'images' +
            image.path_img + '"[alt]>';

          if (image.description) {
            alt = ' alt="' + image.description + '"';
          }

          if (align) {
            align = 'align=' + align;
          }

          html = html.replace('[align]', align);
          html = html.replace('[alt]', alt);

          return html;
        };

        return this;
      }
    ]);
})();

