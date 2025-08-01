(function() {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  OpcacheCtrl
     *
     * @requires $scope
     * @requires itemService
     *
     * @description
     *   Displays the cache status.
     */
    .controller('OpcacheCtrl', [
      '$scope', 'itemService',
      function($scope, itemService) {
        /**
         * Formats a number into a computer space measure
         */
        $scope.formatSpace = function(bytes, precision) {
          if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) {
            return '-';
          }

          if (typeof precision === 'undefined') {
            precision = 1;
          }

          var number = Math.floor(Math.log(bytes) / Math.log(1024));
          var units = [ 'bytes', 'kB', 'MB', 'GB', 'TB', 'PB' ];

          return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +
            ' ' + units[number];
        };

        // Update the chart configuration when server data changes
        $scope.$watch('serverData', function(nv) {
          if (!nv) {
            return;
          }

          $scope.memory = {
            data: [ nv.mem.used_memory, nv.mem.free_memory, nv.mem.wasted_memory ],
            labels: [
              'Used ' + $scope.formatSpace(nv.mem.used_memory),
              'Free ' + $scope.formatSpace(nv.mem.free_memory),
              'Wasted ' + $scope.formatSpace(nv.mem.wasted_memory)
            ],
            options: {
              elements: { arc: { borderWidth: 0 } },
              legend: { display: true },
            }
          };

          $scope.keys = {
            data: [ nv.stats.num_cached_keys, nv.free_keys ],
            labels: [ 'Used', 'Free' ],
            options: {
              elements: { arc: { borderWidth: 0 } },
              legend: { display: true }
            }
          };

          $scope.hits = {
            data: [ nv.stats.misses, nv.stats.hits ],
            labels: [ 'Misses', 'Hits' ],
            options: {
              elements: { arc: { borderWidth: 0 } },
              legend: { display: true }
            }
          };
        });

        // Marks data to delete on destroy
        $scope.$on('$destroy', function() {
          $scope.serverData      = null;
          $scope.chartObjectMem  = null;
          $scope.chartObjectKeys = null;
          $scope.chartObjectHits = null;
        });

        itemService.fetchOpcacheStatus('manager_ws_opcache_status').then(function(response) {
          $scope.serverData = response.data;
        });
      }
    ]);
})();
