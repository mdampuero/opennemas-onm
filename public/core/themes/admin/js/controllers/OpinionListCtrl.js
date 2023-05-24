(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpinionListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires messenger
     * @requires oqlEncoder
     * @requires queryManager
     *
     * @description
     *   Controller for opinion list.
     */
    .controller('OpinionListCtrl', [
      '$controller', '$location', '$scope', 'http', 'messenger', 'oqlEncoder', 'routing',
      function($controller, $location, $scope, http, messenger, oqlEncoder, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'opinion',
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
          tag: null
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
          deleteItem: 'api_v1_backend_opinion_delete_item',
          deleteList: 'api_v1_backend_opinion_delete_list',
          getList:    'api_v1_backend_opinion_get_list',
          patchItem:  'api_v1_backend_opinion_patch_item',
          patchList:  'api_v1_backend_opinion_patch_list',
          public:     'frontend_opinion_show'
        };

        /**
         * @function init
         * @memberOf OpinionListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          $scope.app.columns.hidden = [ 'category' ];

          oqlEncoder.configure({ placeholder: {
            title: '[key] ~ "%[value]%"',
            starttime: '[key] > "[value]"',
            created: '[key] ~ "%[value]%"'
          } });

          $scope.list();
        };

        /**
         * Returns the frontend url for the content given its object.
         *
         * @param {String} item  The object item to generate the url from.
         *
         * @return {String} The frontend URL.
         */
        $scope.getFrontendUrl = function(item) {
          if (!item.pk_content) {
            return '';
          }
          var date = item.created;

          var formattedDate = moment(date).format('YYYYMMDDHHmmss');

          var author = !item.fk_author ? {} : $scope.data.extra.authors.filter(function(author) {
            return author.id === item.fk_author;
          })[0];

          return $scope.data.extra.base_url + $scope.getL10nUrl(
            routing.generate('frontend_opinion_show', {
              id: item.pk_content.toString().padStart(6, '0'),
              created: formattedDate,
              author_name: author.slug,
              opinion_title: item.slug
            })
          );
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
        };
      }
    ]);
})();
