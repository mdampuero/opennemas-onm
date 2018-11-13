(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  EventCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     *
     * @description
     *   Check billing information when saving user.
     */
    .controller('EventCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'oqlDecoder', 'messenger', 'cleaner', 'linker', 'localizer', '$timeout',
      function($controller, $scope, oqlEncoder, oqlDecoder, messenger, cleaner, linker, localizer, $timeout) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf EventCtrl
         *
         * @description
         *  The cover object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'event',
          fk_content_type: 19,
          category: null,
          content_status: 0,
          cover: null,
          date: '',
          description: '',
          favorite: 0,
          file: '',
          price: 0,
          starttime: null,
          endtime: null,
          tag_ids: [],
          tags: [],
          thumbnail: null,
          title: '',
          type: 0,
          with_comments: 0,

          image: null,
          event_startdate: null,
          event_enddate: null,
          external_link: '',
          place_name: ''
        };

        $scope.files = [];

        /**
         * @memberOf EventCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_event_create',
          redirect: 'backend_event_show',
          save:     'api_v1_backend_event_save',
          show:     'api_v1_backend_event_show',
          update:   'api_v1_backend_event_update'
        };
      }
    ]);
})();
