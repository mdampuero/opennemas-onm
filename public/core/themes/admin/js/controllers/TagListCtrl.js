(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TagListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires $uibModal
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in tag list.
     */
    .controller('TagListCtrl', [
      '$controller', '$scope', '$timeout', 'oqlEncoder', '$uibModal', 'http', 'messenger',
      function($controller, $scope, $timeout, oqlEncoder, $uibModal, http, messenger) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf TagListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_tag_delete_item',
          deleteList: 'api_v1_backend_tag_delete_list',
          getList:    'api_v1_backend_tag_get_list',
          saveItem:   'api_v1_backend_tag_save_item',
          updateItem: 'api_v1_backend_tag_update_item',
          moveItem:   'api_v1_backend_tag_move_item',
        };

        /**
         * @function init
         * @memberOf TagListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          $scope.app.columns.selected = [ 'name', 'slug', 'contents' ];
          oqlEncoder.configure({ placeholder: {
            name: 'name ~ "%[value]%" or slug ~ "%[value]%"',
          } });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function() {
          return false;
        };

        /**
         * @function parseList
         * @memberOf TagListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          data.extra.locales = $scope.addEmptyValue(
            $scope.toArray(data.extra.locales, 'id', 'name'));

          return data;
        };

        /**
         * @function move
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm move action.
         *
         * @param {Integer} id The tag id.
         * @param {Object} tag The tag object.
         */
        $scope.move = function(id, item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-move',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  exclude: [ id ],
                  source: item
                };
              },
              success: function() {
                return function(modal, template) {
                  var route = {
                    name: $scope.routes.moveItem,
                    params: { id: id }
                  };

                  return http.put(route, { target: template.target[0] });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };
      }
    ]);
})();
