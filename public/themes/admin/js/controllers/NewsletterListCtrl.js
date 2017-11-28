(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsletterListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $scope
     * @requires $uibModal
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Handles actions for newsletter list.
     */
    .controller('NewsletterListCtrl', [
      '$controller', '$http', '$scope', '$uibModal', 'messenger', 'routing',
      function($controller, $http, $scope, $uibModal, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

        /**
         * @function removePermanetly
         * @memberOf NewsletterListCtrl
         *
         * @description
         *   Removes an item.
         *
         * @param {Object} content The content to remove.
         */
        $scope.removePermanently = function(content) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  content: content
                };
              },
              success: function() {
                return function() {
                  var url = routing.generate('backend_ws_newsletter_delete', { id: content.id });

                  return $http.get(url);
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              messenger.post(response.data.messages);

              if (response.status === 200) {
                $scope.list($scope.route);
              }
            }
          });
        };
      }
    ]);
})();
