angular.module('BackendApp.filters', []).filter('moment', function($locale) {
    return function(input, format, language) {
        var lang = 'en';
        var fmt = 'MMMM Do, YYYY, HH:mm'

        if (language) {
            lang = language;

            if (language == 'es') {
                fmt = 'DD [de] MMMM [de] YYYY, HH:mm'
            }
        }

        if (format) {
            fmt = format;
        }

        var date = new Date(input);

        moment.lang(lang);
        return moment(date).format(fmt);
    };
});
