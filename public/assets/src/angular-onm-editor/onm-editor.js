(function() {
  'use strict';

  var $defer = null;
  var loaded = false;

  /**
   * @ngdoc module
   * @name  onm.editor
   *
   * @description
   *   The `onm.editor` module provides a service and a directive to create and
   *   initialize CKEditor components.
   */
  angular.module('onm.editor', [])

    /**
     * @ngdoc provider
     * @name  Editor
     *
     * @description
     *   Provider to handle CKEditor instances.
     */
    .provider('Editor', [
      '$windowProvider',
      function($windowProvider) {
        /**
         * @memberOf Editor
         *
         * @description
         *   Default options for CKEditor.
         *
         * @type {Object}
         */
        this.defaults = {
          plugins: 'a11yhelp,autogrow,autokeywords,autolink,basicstyles,blockquote,clipboard,contextmenu,elementspath' +
            ',enterkey,entities,filebrowser,floatingspace,font,format,horizontalrule,htmlwriter,image,imageresize,' +
            'indentblock,justify,link,list,magicline,maximize,pastefromword,pastespecial,pastetext,removeformat,' +
            'resize,scayt,sourcearea,stylescombo,tab,table,tabletools,toolbar,undo,wordcount,wsc,wysiwygarea',
          disableNativeSpellChecker: false,
          uiColor: '#ffffff',
          autoGrow_onStartup: true,
          autoGrow_maxHeight: 500,
          wordcount: {
            showWordCount: true,
            showCharCount: false
          },
          allowedContent: true,
          extraPlugins: 'magicline,font',
          magicline_color: 'blue',
          forcePasteAsPlainText: false,
          ignoreEmptyParagraph: true,
          preset: 'standard',
          width: '100%',
          format_tags: 'p;h1;h2;h3;h4;pre',
          entities: false
        };

        /**
         * @memberOf Editor
         *
         * @description
         *   Presets for CKEditor.
         *
         * @type {Object}
         */
        this.presets = {
          simple: {
            removeButtons: 'Anchor,Strike,Subscript,Superscript,Font,Format,Styles,Cut,Copy,Paste,PasteText,Table,HorizontalRule',
            toolbarGroups: [
              {
                name: 'basicstyles',
                groups: [ 'basicstyles', 'cleanup' ]
              }, {
                name: 'align',
                groups: [ 'align' ]
              }, {
                name: 'editing',
                groups: [ 'find', 'selection' ]
              }, {
                name: 'forms'
              }, {
                name: 'styles'
              }, {
                name: 'links'
              }, {
                groups: [ 'list' ]
              }, {
                name: 'others',
              }, {
                name: 'document',
                groups: [ 'mode', 'document', 'doctools' ]
              }
            ],
            autoGrow_maxHeight: 200,
          },

          full: {
            removeButtons: 'Cut,Copy,Paste,PasteText',
            toolbarGroups: [
              {
                name: 'align',
                groups: [ 'align' ]
              }, {
                name: 'document',
                groups: [ 'mode', 'document', 'doctools' ]
              }, {
                name: 'editing',
                groups: [ 'find', 'selection' ]
              }, {
                name: 'forms'
              },
              '/',
              {
                name: 'basicstyles',
                groups: [ 'basicstyles', 'cleanup' ]
              }, {
                name: 'paragraph',
                groups: [ 'list', 'indent', 'blocks', 'align' ]
              }, {
                name: 'links'
              }, {
                name: 'insert'
              },
              '/', {
                name: 'styles'
              }, {
                name: 'tools'
              }, {
                name: 'others',
                groups: [ 'undo' ]
              },
            ],
          },

          standard: {
            removeButtons: 'Strike,Subscript,Superscript,Cut,Copy,Paste,PasteText',
            toolbarGroups: [
              {
                name: 'styles'
              },
              // { name: 'clipboard',   groups: [ 'clipboard' ] },
              {
                name: 'editing',
                groups: [ 'find', 'selection' ]
              }, {
                name: 'forms'
              }, {
                name: 'others',
                groups: [ 'undo' ]
              }, {
                name: 'tools'
              }, {
                name: 'document',
                groups: [ 'mode', 'document', 'doctools' ]
              },
              '/', {
                name: 'basicstyles',
                groups: [ 'basicstyles', 'cleanup' ]
              }, {
                name: 'align',
                groups: [ 'align' ]
              }, {
                name: 'paragraph',
                groups: [ 'list', 'indent', 'blocks' ]
              }, {
                name: 'links'
              }, {
                name: 'insert'
              },
            ],
          }
        };

        /**
         * @function configure
         * @memberOf Editor
         *
         * @description
         *   Creates a new configuration for a CKEditor.
         *
         * @param {String} preset The new value for the preset.
         */
        this.configure = function(preset) {
          var presets = Object.keys(this.presets);

          if (presets.indexOf(preset) === -1) {
            preset = this.defaults.preset;
          }

          return angular.extend({}, this.defaults, this.presets[preset]);
        };

        /**
         * @function addExternal
         * @memberOf Editor
         *
         * @description
         *   Registers one or more resources to be loaded from an external path.
         *
         * @param {String} names    The resource names.
         * @param {String} path     The path of the folder.
         * @param {String} filename The resource file name.
         */
        this.addExternal = function(names, path, filename) {
          $windowProvider.$get().CKEDITOR.plugins
            .addExternal(names, path, filename);
        };

        /**
         * @function destroy
         * @memberOf Editor
         *
         * @description
         *   Destroys a CKEditor instance.
         *
         * @param {String} name The CKEditor instance name.
         */
        this.destroy = function(name) {
          var instance = this.get(name);

          if (instance) {
            instance.destroy();
          }
        };

        /**
         * @function get
         * @memberOf Editor
         *
         * @description
         *   Returns the CKEditor instance given its name.
         *
         * @param string name The CKEditor instance name.
         *
         * @return {Object} The CKEditor instance.
         */
        this.get = function(name) {
          if ($windowProvider.$get().CKEDITOR.instances[name]) {
            return $windowProvider.$get().CKEDITOR.instances[name];
          }

          return false;
        };

        /**
         * @function init
         * @memberOf Editor
         *
         * @description
         *   Initializes a CKEditor.
         *
         * @param {Object} element The element for the CKEditor.
         * @param {Object} options The options for the CKEditor.
         *
         * @return {Object} The CKEditor instance.
         */
        this.init = function(element, options) {
          if (element.tagName.toLowerCase() === 'textarea') {
            return $windowProvider.$get().CKEDITOR.replace(element, options);
          }

          return $windowProvider.$get().CKEDITOR.inline(element, options);
        };

        /**
         * @function setCompatible
         * @memberOf Editor
         *
         * @description
         *   Updates the compatible flag for the current environment.
         *
         * @param {Boolean} compatible Compatible value.
         */
        this.setCompatible = function(compatible) {
          $windowProvider.$get().CKEDITOR.env.isCompatible = compatible;
        };

        /**
         * @function $get
         * @memberOf Editor
         *
         * @description
         *   Returns the current service.
         *
         * @return {Object} The current object.
         */
        this.$get = function() {
          return this;
        };
      }
    ])

    /**
     * @ngdoc directive
     * @name  onmEditor
     *
     * @requires Editor
     *
     * @description
     *   Directive to create CKEditor instances from elements.
     *
     *  ###### Attributes:
     *  - **`ng-model`**: The model for the current CKEditor. (Optional)
     *  - **`onm-editor`**: Initializes the directive. (Required)
     *  - **`onm-editor-preset`**: The CKEditor configuration preset. (Optional)
     *
     * @example
     * <!-- Initializes a CKEditor for this textarea with simple preset -->
     * <textarea onm-editor onm-editor-preset="simple" ng-model="description">
     * </textarea>
     */
    .directive('onmEditor', [
      'Editor', '$q', '$timeout', '$window',
      function(Editor, $q, $timeout, $window) {
        return {
          // E = Element, A = Attribute, C = Class, M = Comment
          restrict: 'A',
          scope: {
            ngModel: '=',
          },
          require: [ 'ngModel', '^?form' ],
          link: function(scope, element, attrs, ctrls) {
            var ngModel = ctrls[0];
            var form    = ctrls[1] || null;

            // Flag to prevent infinite updates between CKEditor and model.
            var stop = false;

            /**
             * Initializes the current CKEditor instance.
             */
            var onLoad = function() {
              var options  = Editor.configure(attrs.onmEditorPreset);
              var instance = Editor.init(element[0], options);

              // Updates CKEditor when model changes
              scope.$watch('ngModel', function(nv) {
                // Prevent infinite loop when comparing '' and undefined
                var value = angular.isUndefined(nv) ? '' : nv;

                if (stop) {
                  stop = !stop;
                  return;
                }

                if (instance.getData() !== value) {
                  instance.setData(value, { internal: false });
                }
              }, true);

              /**
               * Updates model when CKEditor changes and model is not equals.
               *
               * @param {Object} e The event object.
               */
              var setModelData = function(e) {
                if (stop) {
                  stop = !stop;
                  return;
                }

                // Use 'key' event only when in source mode
                if (e.name === 'key' && instance.mode !== 'source') {
                  return;
                }

                var data = instance.getData();

                if (data !== ngModel.$viewValue) {
                  $timeout(function() {
                    stop = true;
                    scope.ngModel = data;
                  }, 0);
                }
              };

              instance.on('change', setModelData);
              instance.on('dialogHide', setModelData);

              // For source view
              instance.on('key', setModelData);

              // Initializes the CKEditor with data
              instance.on('instanceReady', function() {
                // Data from HTML value
                var data = element[0].innerText;

                // If model, data from model
                if (scope && scope.ngModel) {
                  data = scope.ngModel;
                }

                scope.$apply(function() {
                  stop = true;

                  instance.setData(data);

                  if (form) {
                    form.$setPristine(true);
                  }
                });

                scope.$broadcast('ckeditor.ready.' + instance.name);
              });

              // Destroy CKEditor when element is destroyed
              element.bind('$destroy', function() {
                Editor.destroy(instance.name);
              });
            };

            if ($window.CKEDITOR.status === 'loaded') {
              loaded = true;
            }

            if (loaded) {
              onLoad();
            } else {
              $defer.promise.then(onLoad);
            }
          }
        };
      }
    ])

    /**
     * @ngdoc run
     * @name  onm.editor:run
     *
     * @requires $q
     * @requires $timeout
     * @requires $window
     *
     * @description
     *   Initialize and check CKEditor on application run.
     */
    .run([
      '$q', '$timeout', '$window', function($q, $timeout, $window) {
        $defer = $q.defer();

        if (angular.isUndefined($window.CKEDITOR)) {
          throw new Error('CKEDITOR not found');
        }

        $window.CKEDITOR.disableAutoInline = true;

        /**
         *  Check if the ckeditor is loaded
         */
        function checkLoaded() {
          if ($window.CKEDITOR.status === 'loaded') {
            loaded = true;
            $defer.resolve();
          } else {
            checkLoaded();
          }
        }

        $window.CKEDITOR.on('loaded', checkLoaded);
        $timeout(checkLoaded, 0);
      }
    ]);
})();
