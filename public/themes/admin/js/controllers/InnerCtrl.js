/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('InnerCtrl', [
  '$rootScope', '$scope', '$timeout', 'Editor', 'http', 'messenger', 'Renderer',
  function($rootScope, $scope, $timeout, Editor, http, messenger, Renderer) {
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
     * @memberOf InnerCtrl
     *
     * @description
     *  The list of overlays
     *
     * @type {Object}
     */
    $scope.overlay = {};

    /**
     * @function configure
     * @memberOf InnerCtrl
     *
     * @description
     *   Configures the inner form.
     *
     * @param {Object} data The data to configure the form.
     */
    $scope.configure = function(data) {
      // Configure the form
      if ($scope.config.multilanguage === null) {
        $scope.config.multilanguage = data.multilanguage;
      }

      if ($scope.config.locale === null) {
        $scope.config.locale = data.locale;
      }

      if ($scope.forcedLocale && Object.keys(data.options.available)
          .indexOf($scope.forcedLocale)) {
        $scope.config.locale = $scope.forcedLocale;
      }
    };

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
      if (!(items instanceof Array)) {
        items = [ items ];
      }

      for (var i = 0; i < items.length; i++) {
        Editor.get(target).insertHtml(Renderer.renderImage(items[i]));
      }

      Editor.get(target).fire('change');
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
      var keys  = from.split('.');
      var model = $scope;

      for (var i = 0; i < keys.length - 1; i++) {
        if (!model[keys[i]]) {
          model[keys[i]] = {};
        }

        model = model[keys[i]];
      }

      if (angular.isArray(model[keys[i]])) {
        model[keys[i]].splice(index, 1);
        return;
      }

      model[keys[i]] = null;
    };

    /**
     * Insert the selected items in media picker in the target element.
     *
     * @param String name The overlay name.
     */
    $scope.toggleOverlay = function(name) {
      $scope.overlay[name] = !$scope.overlay[name];
    };

    /**
     * Request a slug to the server.
     *
     * @param {String}   slug     The value to calculate slug from.
     * @param {Function} callback The callback to execute on success.
     */
    $scope.getSlug = function(slug, callback) {
      var config = {name: 'api_v1_backend_tools_slug', params: { 'slug': slug }};
      http.get(config).then(callback);
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
