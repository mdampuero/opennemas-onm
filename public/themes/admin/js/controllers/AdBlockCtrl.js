angular.module('BackendApp.controllers').controller('AdBlockCtrl', [
  '$uibModal', '$scope', '$http',
  function ($uibModal, $scope, $http) {
    'use strict';

    /**
     * Detects adblockers and raises a modal informing the user
     * that it has to deactivate them.
     **/
    $scope.detectAdBlock = function() {
      $http.get('/ads.html').then(function(data) {
          // console.log('not blocking')
      }, function(data) {
          if (data.status !== 404) {
            $uibModal.open({
              templateUrl: 'modal-adblock',
              backdrop: 'static',
              controller: 'modalCtrl',
              resolve: {
                template: function() {return null; },
                success: function() {return null; }
              }
            });
          }
      });
    };

    $scope.detectAdBlock();
  }
]);
