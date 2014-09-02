/**
 * Handles all actions in commands listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('OpcacheCtrl', [
    '$scope', 'itemService', 'data',
    function ($scope, itemService, data) {
        /**
         * Opcache statistics
         *
         * @type Object
         */
        $scope.serverData = data;

        $scope.chartObjectMem = {
            "data": {
                "cols": [
                    {id: "t", label: "Name", type: "string"},
                    {id: "s", label: "Value", type: "number"}
                ],
                "rows": [
                    {c: [
                        {v: "Used memory " + formatSpace(data.mem.used_memory)},
                        {v: data.mem.used_memory},
                    ]},
                    {c: [
                        {v: "Free memory " + formatSpace(data.mem.free_memory)},
                        {v: data.mem.free_memory}
                    ]},
                    {c: [
                        {v: "Wasted memory " + formatSpace(data.mem.wasted_memory)},
                        {v: data.mem.wasted_memory},
                    ]}
                ]
            },
            "type" : 'PieChart',
            "options" : {
                'title': 'Memory usage'
            }
        };

        $scope.chartObjectKeys = {
            "data" : {
                "cols": [
                    {id: "t", label: "Name", type: "string"},
                    {id: "s", label: "Value", type: "number"}
                ],
                "rows": [
                    {c: [
                        {v: "Used keys ("+data.stats.num_cached_keys+")"},
                        {v: data.stats.num_cached_keys},
                    ]},
                    {c: [
                        {v: "Free keys ("+data.free_keys+")"},
                        {v: data.free_keys}
                    ]}
                ]
            },
            "type" : 'PieChart',
            "options" : {
                'title': 'Keys usage'
            }
        };

        $scope.chartObjectHits = {
            "data": {
                "cols": [
                    {id: "t", label: "Name", type: "string"},
                    {id: "s", label: "Value", type: "number"}
                ],
                "rows": [
                    {c: [
                        {v: "Misses ("+data.stats.misses+")"},
                        {v: data.stats.misses},
                    ]},
                    {c: [
                        {v: "Hits ("+data.stats.hits+")"},
                        {v: data.stats.hits}
                    ]}
                ]
            },
            "type" : 'PieChart',
            "options" : {
                'title': 'Hit rate'
            }
        };


        /**
         * Formats a number into a computer space measure
         */
        function formatSpace(bytes, precision) {
            if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) return '-';
            if (typeof precision === 'undefined') precision = 1;
            var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'],
                number = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
        }

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
            $scope.serverData      = null;
            $scope.chartObjectMem  = null;
            $scope.chartObjectKeys = null;
            $scope.chartObjectHits = null;
        });

    }
]);
