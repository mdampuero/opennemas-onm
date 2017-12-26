(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     *
     * @description
     *   Check billing information when saving user.
     */
    .controller('UserCtrl', [
      '$controller', '$http', '$scope', '$uibModal',
      function($controller, $http, $scope, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf UserCtrl
         *
         * @description
         *  Activated changed flag
         *
         * @type {Boolean}
         */
        $scope.activatedChanged = false;

        /**
         * @function confirmUser
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.confirmUser = function(isMaster) {
          if (!isMaster && $scope.activated == '1' && $scope.activatedChanged) {
            var modal = $uibModal.open({
              templateUrl: 'modal-update-selected',
              backdrop: 'static',
              controller: 'modalCtrl',
              resolve: {
                template: function() {
                  return {
                    name:           $scope.id ? 'update' : 'create',
                    backend_access: true,
                    value:          1,
                    extra:          $scope.extra,
                  };
                },
                success: function() {
                  return null;
                }
              }
            });

            modal.result.then(function(response) {
              if (response) {
                $('form').submit();
              }
            });

            return false;
          }

          $('form').submit();
        };

        /**
         * @function parseOptions
         * @memberOf UserCtrl
         *
         * @description
         *   Parses the options for additional data fields of type options.
         *
         * @return {Array} The parsed options list.
         */
        $scope.parseOptions = function(options) {
          var options = options.split(/\s*,\s*/);
          var values  = [];

          for (var i = 0; i < options.length; i++) {
            var option = options[i].trim().split(/\s*:\s*/);

            values.push({ key: option[0], value: option[1] });
          }

          return values;
        };

        // Updates activated changed flag when activated changes
        $scope.$watch('activated', function(nv, ov) {
          if (ov !== null && nv && nv !== ov) {
            $scope.activatedChanged = true;
          }
        }, true);

        // Parses options for fields when settings change
        $scope.$watch('extra.settings.fields', function(nv) {
          if (!nv) {
            return;
          }

          for (var i = 0; i < nv.length; i++) {
            if (nv[i].type === 'options' && typeof nv[i].values === 'string') {
              nv[i].values = $scope.parseOptions(nv[i].values);
            }
          }
        }, true);
      }
    ]);
})();
