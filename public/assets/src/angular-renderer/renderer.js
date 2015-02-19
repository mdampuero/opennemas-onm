/**
* onm.render Module
*
* Module to render HTML from objects.
*/
angular.module('onm.Renderer', [])
  .service('renderer', [ function() {
    /**
     * Returns the HTML code to insert in a text for an image.
     *
     * @param Object image The image to render.
     *
     * @return string The HTML code.
     */
    this.renderImage = function(image, align) {
      var alt   = '';
      var align = '';
      var html  = '<img src="' + image.path_img + '"[alt]>';

      if (image.description) {
        alt = ' alt="' + image.description + '"';
      }

      html = html.replace('[alt]', alt);

      return html;
    };

    return this;
  }]);
