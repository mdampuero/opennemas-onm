(function () {
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
      function ($scope, itemService) {
        // Update the chart configuration when server data changes
        $scope.$watch('serverData', function(nv) {
          /**
           * Formats a number into a computer space measure
           */
          function formatSpace(bytes, precision) {
            if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) {
              return '-';
            }

            if (typeof precision === 'undefined') {
              precision = 1;
            }

            var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'],
            number = Math.floor(Math.log(bytes) / Math.log(1024));

            return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
          }

          $scope.chartObjectMem = {
            'data': {
              'cols': [
              { id: 't', label: 'Name', type: 'string' },
              { id: 's', label: 'Value', type: 'number' }
              ],
              'rows': [
              { c: [
                { v: 'Used memory ' + formatSpace(nv.mem.used_memory) },
                { v: nv.mem.used_memory },
              ]},
              { c: [
                { v: 'Free memory ' + formatSpace(nv.mem.free_memory) },
                { v: nv.mem.free_memory}
              ]},
              { c: [
                { v: 'Wasted memory ' + formatSpace(nv.mem.wasted_memory) },
                { v: nv.mem.wasted_memory },
              ]}
              ]
            },
            'type' : 'PieChart',
            'options' : {
              'title': 'Memory usage'
            }
          };

          $scope.chartObjectKeys = {
            'data' : {
              'cols': [
                { id: 't', label: 'Name', type: 'string' },
                { id: 's', label: 'Value', type: 'number' }
              ],
              'rows': [
                { c: [
                  { v: 'Used keys (' + data.stats.num_cached_keys + ')' },
                  { v: nv.stats.num_cached_keys },
                ] },
                { c: [
                  { v: 'Free keys (' + nv.free_keys+')'},
                  { v: nv.free_keys}
                ] }
              ]
            },
            'type' : 'PieChart',
            'options' : {
              'title': 'Keys usage'
            }
          };

          $scope.chartObjectHits = {
            'data': {
              'cols': [
                { id: 't', label: 'Name', type: 'string' },
                { id: 's', label: 'Value', type: 'number' }
              ],
              'rows': [
                { c: [
                  { v: 'Misses (' + nv.stats.misses+')' },
                  { v: nv.stats.misses},
                ]},
                { c: [
                  { v: 'Hits (' + nv.stats.hits+')' },
                  { v: nv.stats.hits }
                ]}
              ]
            },
            'type' : 'PieChart',
            'options' : {
              'title': 'Hit rate'
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
