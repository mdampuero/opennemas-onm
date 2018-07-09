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
      '$controller', '$scope', 'oqlEncoder', 'http', 'messenger', '$timeout',
      function($controller, $scope, oqlEncoder, http, messenger, $timeout) {
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
          tagValidator:   'api_v1_backend_tags_validator'
        };

        /**
         * @function init
         * @memberOf TagListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function(locale) {
          $scope.columns.key          = 'tag-columns';
          $scope.criteria.language_id = locale;
          $scope.backup.criteria      = $scope.criteria;
          $scope.enableUpdate         = false;

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
          $scope.enableUpdate = false;
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
          $scope.enableUpdate = false;
          $scope.editedTag = { name: '', language_id: $scope.criteria.language_id };
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
         * @function validateTag
         * @memberOf TagListCtrl
         *
         * @description
         *   Method for the tag validation. This method check the text added with the DB
         */
        $scope.validateTag = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.enableUpdate = false;
            if ($scope.editedTag.name.length < 2) {
              return false;
            }
            var route = {
              name: $scope.routes.tagValidator,
              params: {
                text: $scope.editedTag.name,
                language_id: $scope.editedTag.language_id ? $scope.editedTag.language_id : $scope.criteria.language_id
              }
            };

            http.get(route).then(
              function(response) {
                if (!response.data.items) {
                  $scope.editedTagError = response.data.message;
                  return null;
                }
                if (response.data.items.length === 0) {
                  $scope.enableUpdate   = true;
                  $scope.editedTagError = null;
                  return null;
                }

                $scope.enableUpdate = $scope.editedTag.id &&
                  response.data.items === 1 &&
                  response.data.items[$scope.editedTag.id];
                return null;
              }, $scope.errorCb
            );
            return null;
          }, 500);

          return false;
        };
      }
    ]);
})();
