(function () {
  'use strict';

  angular.module('onm.moment', [])
    /**
     * Formats a date.
     */
    .filter('moment', ['$window', function($window) {

      /**
       * Formats a date.
       *
       * @param  string input    The date to format.
       * @param  string format   Format for the date.
       * @param  string language Language used while formating.
       * @return string          The formated date.
       */
      return function(input, format, language, timezone) {
        var fmt  = 'MMMM Do, YYYY, HH:mm';
        var lang = 'en';

        if (language) {
          lang = language;

          if (language === 'es') {
            fmt = 'DD [de] MMMM [de] YYYY, HH:mm';
          }
        }

        if (format) {
          fmt = format;
        }

        $window.moment.locale(lang);

        var date = $window.moment(input);

        if (timezone) {
          date = $window.moment.tz(input, timezone);
        }

        return date.format(fmt);
      };
    }]);
})();
