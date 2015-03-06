'use strict';

angular.module('BackendApp.controllers').controller('FrontpageModalCtrl', [
  '$controller', '$http', '$modalInstance', '$scope', 'routing', 'template',
  function ($controller, $http, $modalInstance, $scope, routing, template) {
    /**
     * Closes the current modal
     */
    $scope.close = function() {
      $modalInstance.close(false);
    };

    /**
     * Frees up memory before controller destroy event
     */
     $scope.$on('$destroy', function() {
      $scope.template = null;
    });

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

    var contents = getContentsInFrontpage();
    var encoded  = JSON.stringify(contents);
    $scope.loading = true;

    var data = {
      'contents': encoded,
      'category_name': template.category
    };

    var url = routing.generate('admin_frontpage_preview',
      {category: template.category});
    $http.post(url, data).then(function(response) {
      $scope.src = routing.generate('admin_frontpage_get_preview',
        {category: template.category});

      if (response.status === 200) {
        $scope.preview = true;
      } else {
        $scope.preview = false;
      }
    });
  }
 ]);
