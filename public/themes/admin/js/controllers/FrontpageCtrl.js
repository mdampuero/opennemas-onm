'use strict';

/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('FrontpageCtrl', [
  '$controller', '$http', '$modal', '$scope', 'routing',
  function($controller, $http, $modal, $scope, routing) {


    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.preview = function(category) {
      $modal.open({
        templateUrl: 'modal-preview',
        controller: 'FrontpageModalCtrl',
        resolve: {
          template: function() {
            return {
              category: category
            };
          },
          success: function() {
            return null;
          }
        }
      });




      // return;
      // $.ajax({
      //     type: 'POST',
      //     url: frontpage_urls.preview_frontpage,
      //     ,
      //     beforeSend: function(xhr) {
      //         $('#warnings-validation').html(
      //             "<div class='alert alert-notice'>" +
      //                 "<button class='close' data-dismiss='alert'>×</button>" +
      //                 "Generating frontpage. Please wait..." +
      //             "</div>"
      //         );
      //     },
      //     success: function() {
      //         $.colorbox();
      //         $('#warnings-validation').html('');
      //     }
      // });
    };
  }
]);
