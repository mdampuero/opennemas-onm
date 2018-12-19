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
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          pk_fk_content_category: null,
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
         * @function getCover
         * @memberOf EventListCtrl
         *
         * @description
         *   Returns the cover image for a given content
         */
        $scope.getCover = function(element) {
          var cover = '';

          if (element.related_contents.length > 0) {
            var coverId = element.related_contents[0].pk_content2;

            cover = $scope.data.extra.related_contents[coverId];
          }

          return cover;
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
          return item.pk_content;
        };

        /**
         * @function getRelation
         * @memberOf EventListCtrl
         *
         * @description
         *   Returns the elements taht are in a relation.
         *
         * @param {Object} item     The item.
         * @param {String} relation The name of the relation to fetch.
         * @param {Object} single   Whether to return only one element or not.
         *
         * @return {Array} The list of relations
         */
        $scope.getRelation = function(item, name, single) {
          var relation = item.related_contents.filter(function(el) {
            return el.relationship === name;
          });

          if (single) {
            relation = relation.shift();
          }

          return relation;
        };
      }
    ]);
})();
