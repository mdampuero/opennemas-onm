/**
* Service to handle route generation by using FosJsRouting.
*/
angular.module('onm.history', []).factory('history', function($location) {

    /**
     * The history service.
     *
     * @type Object
     */
    var history = {
        routes: []
    };

    /**
     * Saves the page status for an URL
     *
     * @param string url    The URL to save.
     * @param Object params The URL parameters.
     */
    history.push = function(url, params) {
        return history.routes.push(
            { route: url, params: params }
        );
    };

    /**
     * Restores the last page status basing on the URL.
     *
     * @param string url The URL to restore.
     *
     * @return boolean True if the page could be restored successfully.
     *                 Otherwise, returns false.
     */
    history.restore = function(url) {
        for (var i = history.routes.length - 1; i >= 0; i--) {
            if (history.routes[i].route == url) {
                for (var name in history.routes[i].params) {
                    $location.search(name, history.routes[i].params[name]);
                    delete history.routes[i].params[name];
                };

                return true;
            }
        };

        return false;
    };

    return history;
});
