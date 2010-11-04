var Dashboard = Class.create({

    initialize: function(options) {
        this.content_type = options.content_type;
        this.url = options.url || 'http://webdev-xornal.es/admin/dashboard.php?';
    },

    request: function(method, params) {
        var url = this.fixUri(this.url) + params;

        new Ajax.JSONRequest(url, {
            parameters: {
                format: 'JSON'
            },

            onSuccess: this.onSuccessCallback.bind(this, callback),

            onFailure: function(response) {
                console.log("1: fail", response, response.responseJSON);
            }
        });
    },

    render_table: function(data,title) {
        var tpl = '<table class="adminheading"><tr><th nowrap>#{title}</th></tr></table>' +
                  '<table class="adminlist">' +
                  '<tr><th>Título</th><th align="center">Visitas</th></tr>';
        
        data.each(function(value){
            alert(value);
        });

        /*

                  '<tr><td>Tempo total dos usuarios en liña</td><td>#{max_actions}</td></tr>' +
                  '<tr><td>Días en liña</td><td>#{sum_visit_length}</td></tr>' +
                  '<tr><td>Accións máximas nunha visita</td><td>#{max_actions}</td></tr>' +
                  '<tr><td>Porcentaje abandonos</td><td>#{bounce_percent}</td></tr>' +

                  '</table>';
        */
    },

    render_graph: function(data) {

    },

    render: function(data) {

    },

    fixUri: function(url) {
        if(!/\?/.test(url)) {
            url += '?';
        }

        return url;
    },

    /* Piwik clone methods */
    VisitsSummary$getUniqueVisitors: function(period, date, callback) {
        var method = 'VisitsSummary.getUniqueVisitors';
        this.request(method, 'period=' + period + '&date=' + date, callback);
    },

    VisitsSummary$get: function(period, date, callback) {
        var method = 'VisitsSummary.get';
        this.request(method, 'period=' + period + '&date=' + date, callback);
    }
}); // End PiwikService

var DashBoardSrv = new Dashboard({
    url: 'https://webdev-xornal.es/admin/dashboard.php'
});

/*
var piwikSrv = new PiwikService({
    url: 'https://webdev-xornal.es/admin/dashboard.php'
});

processUnique = function(r){
    $('resultado').update(r.value);
};

processSummary = function(r){
    r.sum_visit_length = Math.round(r.sum_visit_length/(3600 * 24));
    r.bounce_percent   = Math.round((r.bounce_count * 100) / r.nb_visits);
    var tpl = '<table>' +
        '<tr><td>Visitas</td><td>#{nb_visits}</td></tr>' +
        '<tr><td>Impresións de páxina</td><td>#{nb_actions}</td></tr>' +
        '<tr><td>Tempo total dos usuarios en liña</td><td>#{max_actions}</td></tr>' +
        '<tr><td>Días en liña</td><td>#{sum_visit_length}</td></tr>' +
        '<tr><td>Accións máximas nunha visita</td><td>#{max_actions}</td></tr>' +
        '<tr><td>Porcentaje abandonos</td><td>#{bounce_percent}</td></tr>' +
        '</table>';

    $('resultado').update( tpl.interpolate(r) );
};

*/