/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('FrontpageCtrl', [
  '$controller', '$http', '$uibModal', '$scope', 'routing',
  function($controller, $http, $uibModal, $scope, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * The list of selected elements.
     *
     * @type array
     */
    $scope.selected = {
      contents: []
    };

    $scope.deselectAll = function() {
      $scope.selected.contents = [];
    };

    /**
     * Removes the selected contents from this frontpage
     */
    $scope.removeSelectedContents = function () {
      var modal = $uibModal.open({
        templateUrl: 'modal-drop-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return true;
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          var selected =
            $('.content-provider-element input[type="checkbox"]:checked')
              .closest('.content-provider-element');

          selected.each(function() {
              $(this).fadeTo('slow', 0.01, function() {
                  $(this).slideUp('slow', function() {
                      $(this).remove();
                  });
               });
          });

          showMessage(frontpage_messages.remember_save_positions, 'info', 5);

          $scope.selected.contents = [];
        };
      });
    };

    /**
     * Archives the selected contents from all the frontpages
     */
    $scope.archiveSelectedContents = function () {
      var modal = $uibModal.open({
        templateUrl: 'modal-archive-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = frontpage_urls.set_arquived;

              return $http.get(url, { params: {'ids[]': $scope.selected.contents} })
                .success(function(response){
                  showMessage(response, 'success', 5);
                }).error(function(response){
                  showMessage(response.responseText, 'error', 5);
                })
            };
          }
        }
      });

      modal.result.then(function(response) {
        if(response.success) {
          var selected =
            $('.content-provider-element input[type="checkbox"]:checked')
              .closest('.content-provider-element');

          selected.each(function() {
              $(this).fadeTo('slow', 0.01, function() {
                  $(this).slideUp('slow', function() {
                      $(this).remove();
                  });
               });
          });

          $scope.selected.contents = [];
        }
      });
    };

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

    function showMessage(message, type, time) {
      Messenger.options = {
          extraClasses: 'messenger-fixed messenger-on-bottom',
      };

      Messenger().post({
        message: message,
        type: type,
        hideAfter: time,
        showCloseButton: true,
        id: new Date().getTime()
      });
    }

    $scope.changeCategory = function(category) {
      window.location = routing.generate('admin_frontpage_list',
        {category: category});
    };

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
        $uibModal.open({
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
