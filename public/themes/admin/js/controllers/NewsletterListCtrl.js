(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsletterListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires oqlEncoder
     *
     * @description
     *   Handles actions for newsletter list.
     */
    .controller('NewsletterListCtrl', [
      '$controller', '$scope', '$uibModal', 'oqlEncoder',
      function($controller, $scope, $uibModal, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf RestListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          type: 0,
          epp: 25,
          page: 1,
          orderBy: { id: 'desc' }
        };

        /**
         * @memberOf RestListCtrl
         *
         * @description
         *   The newsletter type selected.
         *
         * @type {Object}
         */
        $scope.selectedType = 0;

        /**
         * @memberOf NewsletterListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          delete:         'api_v1_backend_newsletter_delete',
          deleteSelected: 'api_v1_backend_newsletters_delete',
          list:           'api_v1_backend_newsletters_list',
          patch:          'api_v1_backend_newsletter_patch',
          patchSelected:  'api_v1_backend_newsletters_patch'
        };

        /**
         * @function init
         * @memberOf NewsletterListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key     = 'newsletter-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "[value]"',
            }
          });

          $scope.list();
        };

        /**
         * @function init
         * @memberOf NewsletterListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.selectType = function(type) {
          $scope.selectedType = type;
        };

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

        $scope.$watch('selectedType', function(nv, ov) {
          if (nv !== ov) {
            $scope.criteria.type = $scope.selectedType;
          }
        });
      }
    ]);
})();
