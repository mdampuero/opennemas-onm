(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WebPushNotificationsDashboardCtrl
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
     *   Provides actions to list notifications .
     */
    .controller('WebPushNotificationsDashboardCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'oqlEncoder', '$location', '$window',
      function($controller, $scope, http, messenger, oqlEncoder, $location, $window) {
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
          getConfig: 'api_v1_backend_webpush_notifications_get_config'
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

          http.get($scope.routes.getConfig).then(function(response) {
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
            $scope.monthlyImpressions = 0;
            var monthlyClicks      = 0;
            var monthlyClosed      = 0;

            $scope.items.forEach(function(item) {
              $scope.monthlyImpressions += item.impressions;
              monthlyClicks += item.clicks;
              monthlyClosed += item.closed;
            });

            $scope.monthlyInteractions = monthlyClicks + monthlyClosed;

            $scope.monthlyCTR = Math.round($scope.monthlyInteractions / $scope.monthlyImpressions * 100) / 100;

            // Sets up the active subscribers chart
            $scope.labels = [];
            var currentDay = $window.moment($window.moment().subtract(1, 'months').format('YYYY-MM-DD'));

            while (currentDay.isSameOrBefore($window.moment().format('YYYY-MM-DD'))) {
              $scope.labels.push(currentDay.format('MM-DD'));
              currentDay.add(1, 'days');
            }

            $scope.series = [ 'Subs' ];

            var numberOfDays = $scope.labels.length;

            var numberOfSubs = $scope.settings.webpush_active_subscribers &&
              $scope.settings.webpush_active_subscribers.length ?
              $scope.settings.webpush_active_subscribers.length : 0;

            var dataValues = [];

            for (var i = 0; i < numberOfDays - numberOfSubs; i++) {
              dataValues.push(null);
            }

            if ($scope.settings.webpush_active_subscribers) {
              var subscribersArray = [].concat($scope.settings.webpush_active_subscribers);

              dataValues = dataValues.concat(subscribersArray.reverse());
            }
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

            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.items = [];
          });
        };
      }
    ]);
})();
