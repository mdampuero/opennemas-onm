angular.module('BackendApp.controllers')

  /**
   * @ngdoc controller
   * @name  OpinionListCtrl
   *
   * @requires $controller
   * @requires $http
   * @requires $scope
   * @requires routing
   * @requires messenger
   * @requires oqlEncoder
   *
   * @description
   *   Controller for opinion list.
   */
  .controller('MenuListCtrl', [
    '$controller', '$location', '$uibModal', '$scope', 'http', 'routing', 'messenger', 'oqlEncoder',
    function($controller, $location, $uibModal, $scope, http, routing, messenger, oqlEncoder) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

      /**
       * The criteria to search.
       *
       * @type {Object}
       */
      $scope.criteria = {
        epp: 10,
        orderBy: { name:  'desc' },
        page: 1
      };

      /**
       * @memberOf MenuListCtrl
       *
       * @description
       *  The list of routes for the controller.
       *
       * @type {Object}
       */
      $scope.routes = {
        deleteItem: 'api_v1_backend_menu_delete_item',
        deleteList: 'api_v1_backend_menu_delete_list',
        getList:    'api_v1_backend_menu_get_list',
        patchItem:  'api_v1_backend_menu_patch_item',
        patchList:  'api_v1_backend_menu_patch_list'
      };

      /**
       * @inheritdoc
       */
      $scope.getItemId = function(item) {
        return item.pk_menu;
      };

      /**
       * @function init
       * @memberOf MenuListCtrl
       *
       * @description
       *   Configures and initializes the list.
       */
      $scope.init = function() {
        $scope.backup.criteria    = $scope.criteria;
        $scope.app.columns.hidden = [];
        $scope.app.columns.selected = [ 'name', 'position' ];
        oqlEncoder.configure({
          placeholder: {
            name: 'name ~ "%[value]%"'
          }
        });

        $scope.list();
      };

      /**
       * @inheritdoc
       */
      $scope.parseList = function(data) {
        $scope.configure(data.extra);
        $scope.localize($scope.data.items, 'items');
      };

      /**
       * Permanently removes a list of contents by using a confirmation dialog
       */
      $scope.removeSelectedMenus = function() {
        // Enable spinner
        $scope.deleting = 1;

        var modal = $uibModal.open({
          templateUrl: 'modal-batch-remove-permanently',
          backdrop: 'static',
          controller: 'ModalCtrl',
          resolve: {
            template: function() {
              return {
                selected: $scope.selected
              };
            },
            success: function() {
              return function() {
                return http.post('backend_ws_menus_batch_delete',
                  { ids: $scope.selected.contents });
              };
            }
          }
        });

        modal.result.then(function(response) {
          messenger.post(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.success) {
            $scope.list($scope.route);
          }
        });
      };

      /**
       * Permanently removes a menu by using a confirmation dialog
       */
      $scope.removeMenu = function(content) {
        var modal = $uibModal.open({
          templateUrl: 'modal-remove-permanently',
          backdrop: 'static',
          controller: 'ModalCtrl',
          resolve: {
            template: function() {
              return {
                content: content
              };
            },
            success: function() {
              return function() {
                var route = {
                  name: 'backend_ws_menu_delete',
                  params: { id: content.id }
                };

                return http.post(route);
              };
            }
          }
        });

        modal.result.then(function(response) {
          messenger.post(response.data.messages);

          if (response.success) {
            $scope.list($scope.route);
          }
        });
      };
    }
  ]);
