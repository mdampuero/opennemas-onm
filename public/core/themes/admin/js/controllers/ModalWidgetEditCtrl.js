(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ModalWidgetEditCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires $timeout
     * @requires fullUrl
     * @requires $compile
     * @requires http
     * @requires widgetID
     *
     * @description
     *   Controller for the widget edit modal.
     */
    .controller('ModalWidgetEditCtrl', [
      '$controller', '$scope', 'id',
      function($controller, $scope, id) {
        $.extend(this, $controller('WidgetCtrl', { $scope: $scope }));

        $scope.id = id;
      }
    ]);
})();
