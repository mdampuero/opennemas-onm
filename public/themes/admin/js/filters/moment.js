(function () {
  'use strict';

  angular.module('BackendApp.filters')
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
        var tmz  = 'Europe/Madrid';

        if (language) {
          lang = language;

          if (language === 'es') {
            fmt = 'DD [de] MMMM [de] YYYY, HH:mm';
          }
        }

        if (format) {
          fmt = format;
        }

        if (timezone) {
          tmz = timezone;
        }

        $window.moment.locale(lang);

        return $window.moment(input).tz(tmz).format(fmt);
      };
    }]);
})();
