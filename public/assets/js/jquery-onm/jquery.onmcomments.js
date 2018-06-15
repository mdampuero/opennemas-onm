/**
 * OnmComments jQuery plugin - v.0.1
 *
 * A simple plugin for lazy loading comments for any content
 */
(function($, window, document) {
  $.fn.onmComments = function(options) {
    options = $.extend({}, $.fn.onmComments.options, options);

    var methods = {
      loadIframe: function(elem, options) {
        var iframe = $('<iframe>', {
          id: 'onmcomments-' + options.content_id,
          src: options.url + '?content_id=' + options.content_id + '&elems_per_page=' + options.elems_per_page,
          style: 'width: 100%; border: medium none; min-height: 180px; overflow: hidden;'
        });

        setInterval(function() {
          iframe.css('height', iframe.contents().height());
        }, 1000);

        elem.html(iframe);
      }
    };

    return this.each(function() {
      var elem = $(this);

      elem.data('onmComments', options);
      methods.loadIframe(elem, options);
    });
  };

  $.fn.onmComments.options = {
    url: '/comments/get',
    elems_per_page: 10
  };
})(jQuery, window, document);
