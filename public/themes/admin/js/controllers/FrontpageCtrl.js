/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('FrontpageCtrl', [
  '$controller', '$http', '$modal', '$scope', 'routing',
  function($controller, $http, $modal, $scope, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    function getContentsInFrontpage() {
      var els = [];

      $('div.placeholder').each(function() {
        var placeholder = $(this).data('placeholder');
        $(this).find('div.content-provider-element').each(function(index) {
          els.push({
            'id' : $(this).data('content-id'),
            'content_type': $(this).data('class'),
            'placeholder': placeholder,
            'position': index,
            'params': {}
          });
        });
      });

      return els;
    }

    $scope.preview = function(category) {
      $scope.loading = true;

      var contents = getContentsInFrontpage();
      var encoded  = JSON.stringify(contents);

      var data = {
        'contents': encoded,
        'category_name': category
      };

      var url = routing.generate('admin_frontpage_preview',
        {category: category});

      $http.post(url, data).success(function() {
        $modal.open({
          templateUrl: 'modal-preview',
          windowClass: 'modal-fullscreen',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
                src: routing.generate('admin_frontpage_get_preview',
                  {category: category})
              };
            },
            success: function() {
              return null;
            }
          }
        });

        $scope.loading = false;
      }).error(function() {
        $scope.loading = false;
      });
    };
  }
]);
