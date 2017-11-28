(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TrashListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires localizer
     * @requires messenger
     *
     * @description
     *   Controller for Trash list.
     */
    .controller('TrashListCtrl', [
      '$controller', 'http', '$uibModal', '$scope', 'localizer', 'messenger',
      function($controller, http, $uibModal, $scope, localizer, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

        /**
         * Permanently removes a contents by using a confirmation dialog
         */
        $scope.removeAll = function() {
          $uibModal.open({
            backdropClass: 'modal-remove-all',
            controller:  'YesNoModalCtrl',
            templateUrl: 'modal-remove-all',
            resolve: {
              template: function() {
                return {};
              },
              yes: function() {
                return function(modalWindow) {
                  var url = { name: 'backend_ws_contents_empty_trash' };

                  return http.get(url).then(function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  }, function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  });
                };
              },
              no: function() {
                return function(modalWindow) {
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
        };

        /**
         * Takes out of trash a content by using a confirmation dialog
         */
        $scope.restoreFromTrash = function(content) {
          $uibModal.open({
            backdrop: 'static',
            controller:  'YesNoModalCtrl',
            templateUrl: 'modal-restore-from-trash',
            resolve: {
              template: function() {
                return { content: content };
              },
              yes: function() {
                return function(modalWindow) {
                  var url = {
                    name: 'backend_ws_content_restore_from_trash',
                    params: {
                      contentType: content.content_type_name,
                      id: content.id
                    }
                  };

                  return http.get(url).then(function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  }, function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  });
                };
              },
              no: function() {
                return function(modalWindow) {
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
        };

        /**
         * Takes out of trash a list of contents by using a confirmation dialog
         */
        $scope.restoreFromTrashSelected = function() {
          $uibModal.open({
            backdrop: 'static',
            controller:  'YesNoModalCtrl',
            templateUrl: 'modal-batch-restore',
            resolve: {
              template: function() {
                return { selected: $scope.selected };
              },
              yes: function() {
                return function(modalWindow) {
                  var url = {
                    name: 'backend_ws_contents_batch_restore_from_trash',
                    params: { contentType: 'content' }
                  };

                  return http.post(url, { ids: $scope.selected.contents }).then(function(response) {
                    $scope.selected.total = 0;
                    $scope.selected.contents = [];

                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  }, function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  });
                };
              },
              no: function() {
                return function(modalWindow) {
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
        };

        /**
         * Permanently removes a contents by using a confirmation dialog
         */
        $scope.removePermanently = function(content) {
          $uibModal.open({
            templateUrl: 'modal-remove-permanently',
            backdrop: 'static',
            controller: 'YesNoModalCtrl',
            resolve: {
              template: function() {
                return {
                  content: content
                };
              },
              yes: function() {
                return function(modalWindow) {
                  var url = {
                    name: 'backend_ws_content_remove_permanently',
                    params: { contentType: content.content_type_name, id: content.id }
                  };

                  return http.post(url, { ids: $scope.selected.contents }).then(function(response) {
                    $scope.selected.total = 0;
                    $scope.selected.contents = [];

                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  }, function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  });
                };
              },
              no: function() {
                return function(modalWindow) {
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
        };

        /**
         * Permanently removes a list of contents by using a confirmation dialog
         */
        $scope.removePermanentlySelected = function() {
          $uibModal.open({
            templateUrl: 'modal-batch-remove-permanently',
            backdrop: 'static',
            controller: 'YesNoModalCtrl',
            resolve: {
              template: function() {
                return {
                  selected: $scope.selected
                };
              },
              yes: function() {
                return function(modalWindow) {
                  var url = {
                    name: 'backend_ws_contents_batch_remove_permanently',
                    params: { contentType: 'content' }
                  };

                  return http.post(url, { ids: $scope.selected.contents }).then(function(response) {
                    $scope.selected.total = 0;
                    $scope.selected.contents = [];

                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  }, function(response) {
                    messenger.post(response.data.messages);

                    modalWindow.close({ response: false, success: true });

                    $scope.list($scope.route);
                  });
                };
              }
            }
          });
        };

        // Localize titles when content list changes
        $scope.$watch('contents', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          var lz   = localizer.get($scope.extra.options);
          var keys = [ 'title' ];

          $scope.contents = lz.localize(nv, keys, $scope.extra.options.default);
        }, true);
      }
    ]);
})();
