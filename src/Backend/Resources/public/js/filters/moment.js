angular.module('BackendApp.filters', []).filter('moment', function($locale) {
    return function(input, format, language) {
        var lang = 'en';
        var fmt = 'MMMM DD, YYYY, HH:mm'

        if (language) {
            lang = language;
        }

        if (format) {
            fmt = format;
        }

        var date = new Date(input);

        moment.lang(lang);
        return moment(date).format(fmt);
    };
});
