/**
 * Formats a date.
 */
angular.module('BackendApp.filters').filter('moment', function() {
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

            if (language == 'es') {
                fmt = 'DD [de] MMMM [de] YYYY, HH:mm'
            }
        }

        if (format) {
            fmt = format;
        }

        if (timezone) {
            tmz = timezone;
        }

        var date = new Date(input);

        moment.lang(lang);

        return moment(date).tz(tmz).format(fmt);
    };
});
