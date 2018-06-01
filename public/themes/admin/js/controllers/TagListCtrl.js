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
      '$controller', '$scope', 'oqlEncoder', 'http', 'messenger',
      function($controller, $scope, oqlEncoder, http, messenger) {
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
          var locale = $scope.editedTag.language_id ?
            $scope.editedTag.language_id :
            $scope.criteria.language_id;
          var callback = function(response) {
            if (typeof response === 'object') {
              $scope.enableUpdate = false;
            } else {
              $scope.enableUpdate = response;
            }
          };

          return this.checkNewTags(callback, $scope.editedTag.name, locale, $scope.editedTag.id);
        };
      }
    ]);
})();
