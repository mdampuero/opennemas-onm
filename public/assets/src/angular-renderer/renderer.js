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
      '$q', 'routing', 'http',
      function($q, routing, http) {
        /**
         * @memberOf Renderer
         *
         * @description
         *  The template for the group of related contents.
         *
         * @type {String}
         */
        this.base = '<div class="related-content related-content-inner clearfix">' +
          '<ul class="colorize-text">' +
          '[contents]' +
          '</ul>' +
          '</div>';

        /**
         * @memberOf Renderer
         *
         * @description
         *  The html template for the related content.
         *
         * @type {String}
         */
        this.template = '<li>' +
          '[figure]' +
          '<div class="article-data"><a href="[url]">[title]</a></div>' +
          '</li>';

        /**
         * @memberOf Renderer
         *
         * @description
         *  The html template for the figure of the content.
         *
         * @type {String}
         */
        this.figure = '<figure class="image capture"><img src="[path]" width="120" height="68"></figure>';

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
          var html  = '<figure class="image"><img[align] width="[width]" height="[height]" src="' + instanceMedia + image.path + '"[alt]><figcaption>[caption]</figcaption></figure>';

          if (image.description) {
            alt = ' alt="' + image.description.replace(/"/g, '&quot;') + '"';
          }

          if (align) {
            align = 'align=' + align;
          }

          var caption = !image.description ? '' : image.description;

          html = html.replace('[align]', align);
          html = html.replace('[alt]', alt);
          html = html.replace('[caption]', caption);
          html = html.replace('[width]', image.width);
          html = html.replace('[height]', image.height);

          return html;
        };

        /**
         * @function renderRelatedContents
         * @memberOf Renderer
         *
         * @description
         *  Returns the html code to insert in the ckeditor for a group of related contents.
         *
         * @param {Array}    items  The array of related contents selected.
         * @param {Array}    extra  The array of extra data.
         * @param {String}   target The target of the picker.
         *
         * @returns {String} The html code for a group of related contents.
         */
        this.renderRelatedContents = function(items, extra, target) {
          var html     = '';
          var promises = items.map(function(item) {
            return this.renderContent(item, extra, target);
          }.bind(this));

          return $q.all(promises).then(function(result) {
            result.forEach(function(code) {
              html += code;
            });

            return this.base.replace('[contents]', html);
          }.bind(this));
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
         *
         * @return {String} The html code for a related content.
         */
        this.renderContent = function(item, extra) {
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

          // Generates the url for the content.
          html = html.replace('[url]', routing.generate(route, params));

          var related = !item.related_contents.length > 0 ? [] : item.related_contents.filter(function(related) {
            return related.type === 'featured_frontpage';
          });

          related = related.length > 0 ? related[0] : null;

          if (!related) {
            html = item.information && item.information.thumbnail ?
              html.replace('[figure]', this.figure.replace('[path]', item.information.thumbnail)) :
              html.replace('[figure]', '');

            return html;
          }

          var route = {
            name: 'api_v1_backend_content_get_item',
            params: { id: related.target_id }
          };

          return http.get(route).then(function(response) {
            var related = response.data.item;

            if (related.content_type_name !== 'photo') {
              var frontpage = this.getFeaturedFrontpage(related);

              if (!frontpage) {
                html = html.replace('[figure]', '');

                return html;
              }

              if (typeof frontpage !== 'number') {
                return html.replace(
                  '[figure]',
                  this.figure.replace('[path]', frontpage)
                );
              }

              route.params.id = frontpage;

              return http.get(route).then(function(response) {
                return html.replace(
                  '[figure]',
                  this.figure.replace(
                    '[path]', '/asset/zoomcrop,480,270,center,center/' +
                    instanceMedia + response.data.item.path
                  )
                );
              }.bind(this), function() {
                html = html.replace('[figure]', '');

                return html;
              });
            }

            return html.replace(
              '[figure]',
              this.figure.replace('[path]', '/asset/zoomcrop,480,270,center,center/' + instanceMedia + related.path)
            );
          }.bind(this), function() {
            html = html.replace('[figure]', '');

            return html;
          });
        };

        /**
         * @function getFeaturedFrontpage
         * @memberOf Renderer
         *
         * @description
         *   Returns the featured frontpage for a given item.
         *
         * @param {Object} item The item to get the featured frontpage.
         *
         * @return {Object} The featured frontpage.
         */
        this.getFeaturedFrontpage = function(item) {
          var relatedContents = Array.isArray(item.related_contents) ?
            item.related_contents :
            [];

          var related = relatedContents.filter(function(related) {
            return related.type === 'featured_frontpage';
          }).shift();

          if (!related) {
            return item.information && item.information.thumbnail ? item.information.thumbnail : null;
          }

          return related.target_id;
        };

        return this;
      }
    ]);
})();

