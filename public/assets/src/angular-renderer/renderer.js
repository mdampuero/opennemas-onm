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
         * @memberOf Renderer
         *
         * @description
         *  The html template for the related content.
         *
         * @type {String}
         */
        this.template = '<p>' +
          '<div class="content onm-new">' +
          '[figure]' +
          '<div class="title-content">' +
          '<a href="[url]">[title]</a>' +
          '</div>' +
          '</div>' +
          '</p>';

        /**
         * @memberOf Renderer
         *
         * @description
         *  The html template for the figure of the content.
         *
         * @type {String}
         */
        this.figure = '<figure><img src="[path]" height="96" width="96"></figure>';

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
          var html  = '<img[align] style="max-width: 100%;" src="' + instanceMedia + image.path + '"[alt]>';

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

        /**
         * @function renderContent
         * @memberOf Renderer
         *
         * @description
         *   Returns the HTML code to insert in a text for a related content.
         *
         * @param {Object} item   The related content.
         * @param {Array}  extra  The array of extra data.
         * @param {String} target The target of the picker.
         *
         * @return {String} The HTML code.
         */
        this.renderContent = function(item, extra, target) {
          var html = this.template.replace('[title]', item.title);

          if (item.content_type_name === 'attachment') {
            html = html.replace('[url]', instanceMedia + item.path.substr(1));
            html = html.replace('[figure]', '');

            return html;
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

          if (target === 'description') {
            return '<a href="' + routing.generate(route, params) + '">' +
              item.title + '</a>';
          }

          // Generates the url for the content.
          html = html.replace('[url]', routing.generate(route, params));

          var related = !item.related_contents.length > 0 ? [] : item.related_contents.filter(function(related) {
            return related.type === 'featured_frontpage';
          });

          related = related.length > 0 ? related[0] : null;

          if (!related) {
            html = html.replace('[figure]', '');

            return html;
          }

          var photoRoute = {
            name: 'api_v1_backend_photo_get_item',
            params: { id: related.target_id }
          };

          return http.get(photoRoute).then(function(response) {
            var photo = response.data.item;

            return html.replace(
              '[figure]',
              this.figure.replace('[path]', '/asset/zoomcrop,480,270,center,center/' + instanceMedia + photo.path)
            );
          }.bind(this), function() {
            html = html.replace('[figure]', '');

            return html;
          }.bind(this));
        };

        return this;
      }
    ]);
})();

