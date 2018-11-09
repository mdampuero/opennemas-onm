/******************************************
 * Opennemas
 *
 * Handles the
 *
 * @author          Websanova
 * @copyright       Copyright (c) 2012 Websanova.
 * @license         This websanova jQuery boilerplate is dual licensed under the MIT and GPL licenses.
 * @link            http://www.websanova.com
 * @github          http://github.com/websanova/boilerplate
 * @version         1.2.3
 *
 ******************************************/
(function($) {
  $.extend({
    onmStats: function(options) {
      var defaultOptions = {
        controller: '/content/stats'
      };

      var settings = $.extend({}, defaultOptions, options);

      if ('content_id' in settings) {
        $.ajax({
          url: settings.controller,
          method: 'GET',
          data: {
            content_id: settings.content_id,
          }
        });
      }
    }
  });
})(jQuery);
