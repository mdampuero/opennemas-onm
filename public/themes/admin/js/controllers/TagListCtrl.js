(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TagListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in tag list.
     */
    .controller('TagListCtrl', [
      '$controller', '$scope', '$timeout', 'oqlEncoder', 'http', 'messenger',
      function($controller, $scope, $timeout, oqlEncoder, http, messenger) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf TagListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          delete:         'api_v1_backend_tag_delete',
          deleteSelected: 'api_v1_backend_tag_delete',
          update:         'api_v1_backend_tag_update',
          save:           'api_v1_backend_tag_save',
          list:           'api_v1_backend_tags_list',
        };

        /**
         * @function init
         * @memberOf TagListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function(locale) {
          $scope.locale          = locale;
          $scope.columns.key     = 'tag-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });
          $scope.list();
        };

        /**
         * @function editTag
         * @memberOf TagListCtrl
         *
         * @description
         *   Makes some tag editable.
         */
        $scope.editTag = function(tag) {
          $scope.editedTag = tag ? {
            id: tag.id, name: tag.name, language_id: tag.language_id
          } :
            null;
        };

        /**
         * @function createTag
         * @memberOf TagListCtrl
         *
         * @description
         *   Show form for tags.
         */
        $scope.createTag = function() {
          $scope.editedTag = { name: '', language_id: $scope.locale };
        };

        /**
         * @function parseList
         * @memberOf TagListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          data.extra.locales = $scope.addEmptyValue(
            $scope.toArray(data.extra.locales, 'id', 'name'));

          return data;
        };

        /**
         * @function save
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Saves a new item.
         */
        $scope.save = function() {
          var tagAux = JSON.parse(JSON.stringify($scope.editedTag));

          $scope.flags.http.saving = true;

          var route = { name: $scope.routes.save };

          /**
           * Callback executed when subscriber is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags('http');

            if (response.status === 201 || response.status === 200) {
              $scope.editedTag = null;
              $scope.list();
            }
            $scope.flags.http.saving = false;
            messenger.post(response.data);
          };

          if (tagAux && tagAux.id) {
            route.name   = $scope.routes.update;
            route.params = { id: tagAux.id };
            http.put(route, tagAux).then(successCb, $scope.errorCb);
          } else {
            http.post(route, tagAux).then(successCb, $scope.errorCb);
          }
        };

        /**
         * @function getUsername
         * @memberOf UserCtrl
         *
         * @description
         *   Generates an username basing on the name.
         */
        $scope.isValid = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
            $scope.disableFlags('http');
          }

          if (!$scope.editedTag.name) {
            return;
          }

          $scope.flags.http.validating = 1;

          var route = {
            name: 'api_v1_backend_tags_validate',
            params: { text: $scope.editedTag.name, languageId: $scope.locale }
          };

          $scope.tm = $timeout(function() {
            http.get(route).then(function() {
              $scope.disableFlags('http');
              $scope.form.name.$setValidity('exists', true);
            }, function(response) {
              $scope.disableFlags('http');
              $scope.form.name.$setValidity('exists', false);

              $scope.error = '<ul><li>' + response.data.map(function(e) {
                return e.message;
              }).join('</li><li>') + '</li></ul>';
            });
          }, 500);
        };
      }
    ]);
})();
