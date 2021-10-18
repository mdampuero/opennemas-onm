(function() {
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
  angular.module('onm.renderer', [ 'onm.routing' ])

    /**
     * @ngdoc service
     * @name  Renderer
     *
     * @description
     *   Service to render HTML elements from objects.
     */
    .service('Renderer', [
      'routing', 'http',
      function(routing, http) {

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
          var html  = '<img[align] src="' + instanceMedia + image.path + '"[alt]>';

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

        this.renderContent = function(item, extra) {
          if (item.content_type_name === 'attachment') {
            return '<a href="' + instanceMedia + item.path.substr(1) + '">' + item.title + '</a><br>';
          }

          var category = !item.categories ? {} : extra.categories.filter(function(category) {
            return category.id === item.categories[0];
          })[0];

          var author = !item.fk_author ? {} : extra.authors.filter(function(author) {
            return author.id === item.fk_author;
          })[0];

          var route  = 'frontend_' + item.content_type_name + '_show';
          var params = {
            id:      item.pk_content,
            created: window.moment(item.created).format('YYYYMMDDHHmmss')
          };

          if (item.content_type_name === 'opinion') {
            params.author_name   = author.slug;
            params.opinion_title = item.slug;
          } else {
            params.slug = item.slug;
          }

          if (item.categories && item.categories.length > 0) {
            params.category_slug = category.name;
          }

          var related = !item.related_contents.length > 0 ? [] : item.related_contents.filter(function(related) {
            return related.type === 'featured_frontpage';
          });

          related = related.length > 0 ? related[0] : null;

          if (!related) {
            return '<div class="content onm-new" style="display: flex; flex-direction: row; justify-content: space-between; width: 100%; border: 1px solid black; padding: 15px;">' +
              '<div class="title-content">' +
              '<a href="' + routing.generate(route, params) + '">' + item.title + '</a>' +
              '</div>' +
              '</div>';
          }

          var photoRoute = {
            name: 'api_v1_backend_photo_get_item',
            params: { id: related.target_id }
          };

          return http.get(photoRoute).then(function(response) {
            var photo = response.data.item;

            return '<div class="content onm-new" style="display: flex; flex-direction: row; justify-content: space-between; width: 100%; border: 1px solid black; padding: 15px;">' +
              '<figure style="width: 25%; flex-basis: 25%;">' +
              '<img src="/asset/zoomcrop,480,270,center,center/' + instanceMedia + photo.path +
              '" height="96" width="96">' +
              '</figure>' +
              '<div style="width: 70%; flex-basis: 70%;" class="title-content">' +
              '<a href="' + routing.generate(route, params) + '">' + item.title + '</a>' +
              '</div>' +
              '</div>';
          }, function() {
            return '<div class="content onm-new" style="display: flex; flex-direction: row; justify-content: space-between; width: 100%; border: 1px solid black; padding: 15px;">' +
              '<div class="title-content">' +
              '<a href="' + routing.generate(route, params) + '">' + item.title + '</a>' +
              '</div>' +
              '</div>';
          });
        };

        return this;
      }
    ]);
})();

