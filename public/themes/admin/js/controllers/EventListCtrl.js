(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  EventListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('EventListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'linker', 'localizer',
      function($controller, $scope, oqlEncoder, linker, localizer) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          pk_content_category: null,
          content_type_name: 'event',
          epp: 10,
          in_litter: 0,
          orderBy: { starttime:  'desc' },
          page: 1
        };

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          delete:         'api_v1_backend_event_delete',
          deleteSelected: 'api_v1_backend_events_delete',
          list:           'api_v1_backend_events_list',
          patch:          'api_v1_backend_event_patch',
          patchSelected:  'api_v1_backend_events_patch'
        };

        /**
         * @function init
         * @memberOf EventListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key     = 'event-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: { title: '[key] ~ "%[value]%"' } });
          $scope.list();
        };

        /**
         * @function parseList
         * @memberOf RestListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          var lz = localizer.get({
            default: data.extra.default,
            available: data.extra.available,
            translators: data.extra.translators
          });

          $scope.categories = lz.localize(data.extra.categories,
            [ 'title' ], $scope.locale);

          $scope.config.linkers.categories =
            linker.get('categories', $scope, false, 'title');

          $scope.config.linkers.categories.setKey($scope.locale);
          $scope.config.linkers.categories.link(data.extra.categories, $scope.categories);

          return data;
        };

        /**
         * @function getId
         * @memberOf EventListCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The item id.
         */
        $scope.getId = function(item) {
          return item.id;
        };
      }
    ]);
})();
