(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserCtrl
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
    .controller('NewsletterTemplateCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner, http, messenger, routing) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        $scope.days = [
          'Monday',
          'Tuesday',
          'Wednesday',
          'Thursday',
          'Friday',
          'Saturday',
          'Sunday'
        ];

        $scope.hours = [];
        for (var i = 0; i < 24; i++) {
          $scope.hours.push(i + ':00');
        }

        /**
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *  The newsletter object.
         *
         * @type {Object}
         */
        $scope.item = {
          type: 1,
          status: 0,
          title: '',
          contents: [],
          schedule: {
            days:  [],
            hours: [],
          },
          recipients: [],
        };

        $scope.flags.expanded = 'recipients';

        /**
         * @memberOf UserCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_newsletter_template_create',
          redirect: 'backend_newsletter_template_show',
          save:     'api_v1_backend_newsletter_template_save',
          show:     'api_v1_backend_newsletter_template_show',
          update:   'api_v1_backend_newsletter_template_update'
        };

        $scope.loadHours = function($query) {
          return $scope.hours;
        };

        $scope.loadDays = function($query) {
          return $scope.days;
        };

        $scope.removeContainer = function(container) {
          var position = $scope.item.contents.indexOf(container);

          $scope.newsletterContents.splice(position, 1);
        };

        $scope.addContainer = function() {
          $scope.newsletterContents.push({
            id: 0,
            title: '',
            position: '',
            items: []
          });
        };
      }
    ]);
})();
