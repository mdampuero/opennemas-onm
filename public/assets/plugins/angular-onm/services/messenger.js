/**
 * Service used to show messages.
 */
angular.module('onm.messenger', []).factory('messenger',  function () {
    /**
     * The messenger service.
     *
     * @type Object
     */
    var messenger = {};

    /**
     * Run messenger post action.
     *
     * @param array params Array of parameters.
     */
    messenger.post = function(params) {
        Messenger.options = {
            extraClasses: 'messenger-fixed messenger-on-bottom messenger-on-right',
            theme: 'flat'
        };

        params.hideAfter = 5;

        Messenger().post(params);
    };

    return messenger;
});
