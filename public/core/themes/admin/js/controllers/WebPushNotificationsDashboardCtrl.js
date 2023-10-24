(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     * @requires $window
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('WebPushNotificationsDashboardCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger', 'oqlEncoder', '$location', '$window',
      function($controller, $scope, cleaner, http, messenger, oqlEncoder, $location, $window) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          send_date: $window.moment().subtract(1, 'months').format('YYYY-MM-DD HH:mm:ss')
        };

        /**
         * @memberOf WebPushNotificationsDashboardCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getList:   'api_v1_backend_webpush_notifications_get_list',
        };

        /**
         * @function init
         * @memberOf WebPushNotificationsDashboardCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          oqlEncoder.configure({
            placeholder: {
              send_date: '[key] > "[value]"',
            }
          });

          http.get('api_v1_backend_webpush_notifications_get_config').then(function(response) {
            $scope.settings = response.data;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });

          $scope.list();
        };

        /**
         * @function list
         * @memberOf RestListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.http.loading = 1;

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data = response.data;

            if (!response.data.items) {
              $scope.data.items = [];
            }

            $scope.items = $scope.data.items;

            // Gets monthly data for notifications in list
            var monthlyImpressions = 0;
            var monthlyClicks      = 0;
            var monthlyClosed      = 0;

            $scope.items.forEach(function(item) {
              monthlyImpressions += item.impressions;
              monthlyClicks += item.clicks;
              monthlyClosed += item.closed;
            });

            $scope.monthlyImpressions    = monthlyImpressions;
            $scope.monthlyInteractions   = monthlyClicks + monthlyClosed;
            $scope.monthlyCTR            = Math.round($scope.monthlyInteractions / monthlyImpressions * 100) / 100;

            // Sets up the active subscribers chart
            var endDate = $window.moment().format('YYYY-MM-DD');
            var startDate = $window.moment().subtract(1, 'months').format('YYYY-MM-DD');

            var labels = [];
            var currentDay = $window.moment(startDate);

            while (currentDay.isSameOrBefore(endDate)) {
              labels.push(currentDay.format('MM-DD'));
              currentDay.add(1, 'days');
            }

            $scope.labels = labels;
            $scope.series = [ 'Subs' ];
            $scope.data = [ $scope.settings.webpush_active_subscribers ];

            var numberOfDays = $scope.labels.length;

            var numberOfSubs = $scope.settings.webpush_active_subscribers.length;

            var dataValues = Array(numberOfDays - numberOfSubs).fill(null);

            dataValues = dataValues.concat($scope.settings.webpush_active_subscribers.reverse());

            $scope.data = [ dataValues ];

            $scope.options = {
              responsive: true,
              maintainAspectRatio: true,
              aspectRatio: 4,
              scales: {
                yAxes: [
                  {
                    ticks: {
                      beginAtZero: true,
                      stepSize: 1,
                      callback: function(value) {
                        if (value % 1 === 0) {
                          return value;
                        }
                      }
                    }
                  }
                ]
              }
            };

            $scope.parseList(response.data);

            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.items = [];
          });
        };

        /**
         * @function parseList
         * @memberOf WebPushNotificationsListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          data.items.forEach(function(item) {
            item.image = Number(item.image);
          });
        };
      }
    ]);
})();
