angular.module('BackendApp.filters', []).filter('moment', function($locale) {
    return function(input, format, language) {
        var lang = 'en';
        var fmt = 'LLLL'

        if (language) {
            lang = language;
        }

        if (format) {
            fmt = format;
        }

        var date = new Date(input);

        return moment(date).format(fmt);
    };
});
