/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('InnerCtrl', [
  '$rootScope', '$scope', '$timeout', 'onmEditor', 'renderer',
  function($rootScope, $scope, $timeout, onmEditor, renderer) {
    'use strict';

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
          html = renderer.renderImage(items[i]);
          onmEditor.get(target).insertHtml(html);
        }

        return;
      }

      html = renderer.renderImage(items);
      onmEditor.get(target).insertHtml(html);
    };

    /**
     * Updates the scope with the items.
     *
     * @param string target The property to update.
     * @param array  items  The new property value.
     */
    $scope.insertInModel = function(target, items) {
      $scope.loaded = false;
      $scope[target] = items;

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
      $scope[from].splice(index, 1);
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

    // Initialize the scope with the input/select values.
    $('input, select').each(function() {
      var name = $(this).attr('name');
      $scope[name] = $(this).val();
    });
  }
]);
