/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('InnerCtrl', [
  '$rootScope', '$scope', '$timeout', 'Editor', 'messenger', 'Renderer',
  function($rootScope, $scope, $timeout, Editor, messenger, Renderer) {
    'use strict';

    /**
     * @memberOf InnerCtrl
     *
     * @description
     *  The list configuration.
     *
     * @type {Object}
     */
    $scope.config = {
      linkers: {},
      locale: null,
      multilanguage: null
    };

    /**
     * @memberOf InnerCtrl
     *
     * @description
     *  The list of flags
     *
     * @type {Object}
     */
    $scope.flags = {};

    /**
     * @function disableFlags
     * @memberOf InnerCtrl
     *
     * @description
     *   Disables all flags.
     */
    $scope.disableFlags = function() {
      for (var key in $scope.flags) {
        $scope.flags[key] = false;
      }
    };

    /**
     * @function errorCb
     * @memberOf InnerCtrl
     *
     * @description
     *   The callback function to execute when an ajax request fails.
     *
     * @param {Object} response The response object.
     */
    $scope.errorCb = function(response) {
      $scope.disableFlags();

      if (response && response.data) {
        messenger.post(response.data);
      }
    };

    /**
     * Inserts an array of items in a CKEditor instance.
     *
     * @param string target The target id.
     * @param array  items  The items to insert.
     */
    $scope.insertInCKEditor = function(target, items) {
      var html;

      if (items instanceof Array) {
        for (var i = 0; i < items.length; i++) {
          html = Renderer.renderImage(items[i]);
          Editor.get(target).insertHtml(html);
        }

        Editor.get(target).fire('change');

        return;
      }

      html = Renderer.renderImage(items);
      Editor.get(target).insertHtml(html);
    };

    /**
     * Updates the scope with the items.
     *
     * @param string target The property to update.
     * @param array  items  The new property value.
     */
    $scope.insertInModel = function(target, items) {
      $scope.loaded = false;

      var keys  = target.split('.');
      var model = $scope;

      for (var i = 0; i < keys.length - 1; i++) {
        if (!model[keys[i]]) {
          model[keys[i]] = {};
        }

        model = model[keys[i]];
      }

      model[keys[i]] = items;

      // Trick to force dynamic image re-rendering
      $timeout(function() {
        $scope.loaded = true;
      }, 0);
    };

    /**
     * Removes the given image from the scope.
     *
     * @param string image The image to remove.
     */
    $scope.removeImage = function(image) {
      delete $scope[image];
    };

    /**
     * Removes an item from an array of related items.
     *
     * @param string  from  The array name in the current scope.
     * @param integer index The index of the element to remove.
     */
    $scope.removeItem = function(from, index) {
      if (angular.isArray($scope[from])) {
        $scope[from].splice(index, 1);
        return;
      }

      delete $scope[from];
    };

    $scope.toggleOverlay = function(overlay) {
      if (!$scope.overlay) {
        $scope.overlay = {};
        $scope.overlay[overlay] = false;
      }

      $scope.overlay[overlay] = !$scope.overlay[overlay];
    };

    /**
     * Insert the selected items in media picker in the target element.
     *
     * @param  Object event The event object.
     * @param  Object args  The event arguments.
     */
    $rootScope.$on('MediaPicker.insert', function (event, args) {
      if (/editor.*/.test(args.target)) {
        var target = args.target.replace('editor.', '');
        return $scope.insertInCKEditor(target, args.items);
      }

      $scope.insertInModel(args.target, args.items);
    });

    /**
     * Insert the selected items in media picker in the target element.
     *
     * @param  Object event The event object.
     * @param  Object args  The event arguments.
     */
    $rootScope.$on('ContentPicker.insert', function (event, args) {
      if (/editor.*/.test(args.target)) {
        var target = args.target.replace('editor.', '');
        return $scope.insertInCKEditor(target, args.items);
      }

      $scope.insertInModel(args.target, args.items);
    });

    // Updates linkers when locale changes
    $scope.$watch('config.locale', function(nv, ov) {
      if (nv === ov) {
        return;
      }

      if (!$scope.config.multilanguage || !$scope.config.locale) {
        return;
      }

      for (var key in $scope.config.linkers) {
        $scope.config.linkers[key].setKey(nv);
        $scope.config.linkers[key].update();
      }
    });

    // Initialize the scope with the input/select values.
    $('input, select, textarea').each(function() {
      var name = $(this).attr('name');
      var value = $(this).val();
      if ($(this).attr('type') === 'number') {
        value = parseInt(value);
      }
      $scope[name] = value;
    });
  }
]);
