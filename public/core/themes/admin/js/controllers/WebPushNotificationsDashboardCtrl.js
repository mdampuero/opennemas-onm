(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WebPushNotificationsDashboardCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     * @requires $location
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
          send_date: $window.moment().subtract(1, 'months').format('YYYY-MM-DD HH:mm:ss'),
          epp: null
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
            $scope.generateStats();
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
            $scope.items = [];
          });
        };

        $scope.generateStats = function() {
          // Gets monthly data for notifications in list
          $scope.monthlyImpressions = 0;
          $scope.monthlyInteractions = 0;

          $scope.items.forEach(function(item) {
            $scope.monthlyImpressions += item.impressions;
            $scope.monthlyInteractions += item.clicks;
            $scope.monthlyInteractions += item.closed;
          });

          $scope.monthlyCTR = Math.round($scope.monthlyInteractions / $scope.monthlyImpressions * 10000) / 100;
          $scope.monthlyCTR = isNaN($scope.monthlyCTR) ? 0 : $scope.monthlyCTR;
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

          $scope.data = $scope.settings.webpush_active_subscribers ?
            $scope.settings.webpush_active_subscribers.reverse() :
            [];

          for (var i = 0; i < numberOfDays - numberOfSubs; i++) {
            $scope.data.unshift(0);
          }

          $scope.data = [ $scope.data ];

          $scope.options = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 4,
            scales: {
              yAxes: [
                {
                  ticks: {
                    beginAtZero: true,
                    maxTicksLimit: 11,
                    stepSize: 1,
                    callback: function(value) {
                      if (value > 100) {
                        return Math.round(value / 10) * 10;
                      }
                      if (value % 1 === 0) {
                        return value;
                      }
                    }
                  }
                }
              ]
            }
          };
        };
      }
    ]);
})();
