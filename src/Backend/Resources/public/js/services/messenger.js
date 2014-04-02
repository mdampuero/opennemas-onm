/**
 * Service used to show messages.
 */
angular.module('BackendApp.services').service('messenger',  function () {
    /**
     * Run messenger post action.
     *
     * @param array params Array of parameters.
     */
    this.post = function(params) {
        Messenger.options = {
            extraClasses: 'messenger-fixed messenger-on-bottom messenger-on-right',
            theme: 'flat'
        };

        Messenger().post(params);
    }
});
