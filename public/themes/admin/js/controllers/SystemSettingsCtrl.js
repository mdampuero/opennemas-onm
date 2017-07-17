(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  SystemSettingsCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Handles actions for paywall settings configuration form.
     */
    .controller('SystemSettingsCtrl', ['$controller', 'http', '$rootScope', '$scope',
      function($controller, http, $rootScope, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function init
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Initialize list of other ga account codes.
         *
         * @param Object gaCodes The list of other ga account codes.
         */
        $scope.init = function(gaCodes) {
          if (angular.isArray(gaCodes)) {
            $scope.gaCodes = gaCodes;
          } else {
            $scope.gaCodes = [];
          }
        };

        /**
         * @function addInput
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Add new input for ga tracking code.
         *
         * @param integer index The index of the domain to remove.
         */
        $scope.addGanalytics = function() {
          $scope.gaCodes.push({
            apiKey:'',
            baseDomain:'',
            customVar:''
          });
        };


        /**
         * @function removeInput
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Removes a ga tracking code input.
         *
         * @param integer index The index of the input to remove.
         */
        $scope.removeGanalytics = function(gaCodes, index) {
          $scope.gaCodes.splice(index, 1);
        };

        /**
         * @function addFile
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Adds an empty File to the answer list.
         */
        $scope.addFile = function (inputRTBFile) {
          var fileToAdd = $scope.rtbFilesSuggestions[inputRTBFile]
          $scope.rtbFiles.push({id: fileToAdd, name: inputRTBFile});
        };

        /**
         * @function parseRTBFiles
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Parses the RTB files from the template and initializes the scope.
         *
         * @param {Object} name The rtb file.
         */
        $scope.parseRTBFiles = function(rtbFiles) {
          if (rtbFiles) {
            $scope.rtbFiles = rtbFiles;
          } else {
            $scope.rtbFiles = [];
          }
        };

        /**
         * @function expand
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Creates a suggestion list basing on a file list.
         *
         * @param {String} domain The input domain.
         */
        $scope.getSuggestions = function(searchName) {
          if (searchName.length < 3) {
            return [];
          }

          var route = {
            name: 'api_v1_files_autocomplete',
            params: { search: searchName}
          };

          $scope.loading = true;

          return http.get(route).then(function(response) {
            $scope.loading = false;
            if (response.data.results)  {
              var fileKeys = [];

              for (var index in $scope.rtbFiles)  {
                fileKeys.push($scope.rtbFiles[index].id);
              }

              var invert = {};
              var results = [];
              for (var key in response.data.results){
                if(!(fileKeys.indexOf(response.data.results[key].id) > -1))  {
                  invert[response.data.results[key].fileName] = response.data.results[key].id;
                  results.push(response.data.results[key]);
                }
              }
              $scope.rtbFilesSuggestions = invert;

              return results;
            }
          });
        };

        /**
         * @function map
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Listens for the enter key to add a domain to map
         */
        $scope.mapByKeyPress = function(event) {
          if (event.keyCode === 13) {
            $scope.getSuggestions(event.target.value);
          }
        };

        /**
         * @function removeFile
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Removes one files from the file list given its index.
         *
         * @param {Integer} index The index of the file to remove.
         */
        $scope.removeFile = function (index) {
          $scope.rtbFiles.splice(index, 1);
        };

        // Updates internal parsedRTBFiles parameter when rtbFiles change.
        $scope.$watch('rtbFiles', function(nv, ov) {
          $scope.parsedRTBFiles = [];
          if ($scope.rtbFiles) {
            for (var i = $scope.rtbFiles.length - 1; i >= 0; i--) {
              $scope.parsedRTBFiles.push({id: $scope.rtbFiles[i].id, name: $scope.rtbFiles[i].name});
            }
          }
          $scope.parsedRTBFiles = JSON.stringify($scope.parsedRTBFiles.reverse());
        }, true);

      }
    ]);
})();
