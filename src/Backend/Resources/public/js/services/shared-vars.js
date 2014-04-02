/**
 * Service used to share variable between controllers.
 */
angular.module('BackendApp.services')
    .service('sharedVars',  function ($rootScope) {
        /**
         * Object to share variables.
         *
         * @type Object
         */
        var vars = {};

        return {
            /**
             * Returns the value of the variable given by its name.
             *
             * @param  string name Variable name.
             * @return mixed       Variable value.
             */
            get: function(name) {
                if (name) {
                    return vars[name];
                }

                return vars;
            },

            /**
             * Sets the value of the variable given its name.
             *
             * @param string name  Variable name.
             * @param string value Variable value.
             */
            set: function(name, value) {
                vars[name] = value;
                $rootScope.$broadcast('SharedVarsChanged', vars);
            }
       };
    });
