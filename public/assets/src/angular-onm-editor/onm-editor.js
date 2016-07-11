(function () {
  'use strict';

  var $defer, loaded = false;

  /**
   * @ngdoc module
   * @name  onm.editor
   *
   * @description
   *   The `onm.editor` module provides a service and a directive to create and
   *   initialize CKEDitor components.
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
      function() {
        /**
         * @memberOf Editor
         *
         * @description
         *   Default options for CKEditor.
         *
         * @type {Object}
         */
        this.defaults = {
          plugins: 'a11yhelp,autogrow,autokeywords,autolink,basicstyles,blockquote,clipboard,contextmenu,elementspath'+
            ',enterkey,entities,filebrowser,floatingspace,font,format,horizontalrule,htmlwriter,image,imageresize,'+
            'indent,justify,link,list,magicline,maximize,pastefromword,pastespecial,pastetext,removeformat,resize,'+
            'scayt,sourcearea,stylescombo,tab,table,tabletools,toolbar,undo,wordcount,wsc,wysiwygarea',
          disableNativeSpellChecker: false,
          uiColor: '#ffffff',
          autoGrow_onStartup: true,
          autoGrow_maxHeight: 500,
          wordcount: {
            showWordCount: true,
            showCharCount: false
          },
          allowedContent: true,
          // language: settings.language,
          extraPlugins: 'magicline,font',
          magicline_color: 'blue',
          forcePasteAsPlainText: false,
          ignoreEmptyParagraph: true,
          preset: 'standard',
          width: '100%',
          format_tags: 'p;h1;h2;h3;h4;pre'
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
                groups: ['basicstyles', 'cleanup']
              }, {
                name: 'align',
                groups: ['align']
              }, {
                name: 'editing',
                groups: ['find', 'selection']
              }, {
                name: 'forms'
              }, {
                name: 'styles'
              }, {
                name: 'links'
              }, {
                name: 'others',
                groups: ['undo']
              }, {
                name: 'insert'
              }, {
                name: 'document',
                groups: ['mode', 'document', 'doctools']
              }
            ],
            autoGrow_maxHeight: 200,
          },

          full: {
            removeButtons: 'Cut,Copy,Paste,PasteText',
            toolbarGroups: [
              {
                name: 'align',
                groups: ['align']
              }, {
                name: 'document',
                groups: ['mode', 'document', 'doctools']
              }, {
                name: 'editing',
                groups: ['find', 'selection', 'spellchecker']
              }, {
                name: 'forms'
              },
              '/',
              {
                name: 'basicstyles',
                groups: ['basicstyles', 'cleanup']
              }, {
                name: 'paragraph',
                groups: ['list', 'indent', 'blocks', 'align']
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
                groups: ['undo']
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
                groups: ['find', 'selection', 'spellchecker']
              }, {
                name: 'forms'
              }, {
                name: 'others',
                groups: ['undo']
              }, {
                name: 'tools'
              }, {
                name: 'document',
                groups: ['mode', 'document', 'doctools']
              },
              '/', {
                name: 'basicstyles',
                groups: ['basicstyles', 'cleanup']
              }, {
                name: 'align',
                groups: ['align']
              }, {
                name: 'paragraph',
                groups: ['list', 'indent', 'blocks']
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

          if (presets.indexOf(preset) == -1) {
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
          CKEDITOR.plugins.addExternal(names, path, filename);
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
          if (CKEDITOR.instances[name]) {
            return CKEDITOR.instances[name];
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
         * @param boolean replace Whether to replace the given element.
         * @param Object  element The element for the CKEditor.
         * @param Object  options The options for the CKEditor.
         *
         * @return {Object} The CKEditor instance.
         */
        this.init = function(replace, element, options) {
          if (replace) {
            return CKEDITOR.replace(element, options);
          }

          return CKEDITOR.inline(element, options);
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
          CKEDITOR.env.isCompatible = compatible;
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
        this.$get = function () {
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
    .directive('onmEditor', ['Editor', '$q', '$timeout',
      function (Editor, $q, $timeout) {
        return {
          restrict: 'A', // E = Element, A = Attribute, C = Class, M = Comment
          scope: false,
          require: ['ngModel', '^?form'],
          link: function (scope, element, attrs, ctrls) {
            var ngModel    = ctrls[0];
            var form       = ctrls[1] || null;
            var EMPTY_HTML = '<p></p>';
            var isTextarea = element[0].tagName.toLowerCase() === 'textarea';
            var data       = [];
            var isReady    = false;

            if (!isTextarea) {
              element.attr('contenteditable', true);
            }

            var onLoad = function () {
              var options = Editor.configure(attrs.onmEditorPreset);
              var instance = (isTextarea) ? CKEDITOR.replace(element[0], options) : CKEDITOR.inline(element[0], options),
              configLoaderDef = $q.defer();

              element.bind('$destroy', function () {
                if (instance && CKEDITOR.instances[instance.name]) {
                  CKEDITOR.instances[instance.name].destroy();
                }
              });

              var setModelData = function (setPristine) {
                var data = instance.getData();
                if (data === '') {
                  data = null;
                }
                $timeout(function () { // for key up event
                  if (setPristine !== true || data !== ngModel.$viewValue) {
                    ngModel.$setViewValue(data);
                  }

                  if (setPristine === true && form) {
                    form.$setPristine();
                  }
                }, 0);
              };

              var onUpdateModelData = function (setPristine) {
                if (!data.length) {
                  return;
                }

                var item = data.pop() || EMPTY_HTML;
                isReady = false;
                instance.setData(item, function () {
                  setModelData(setPristine);
                  isReady = true;
                });
              };

              instance.on('pasteState', setModelData);
              instance.on('change', setModelData);
              instance.on('blur', setModelData);
              //instance.on('key', setModelData); // for source view

              instance.on('instanceReady', function () {
                scope.$broadcast('ckeditor.ready');
                scope.$apply(function () {
                  onUpdateModelData(true);
                });

                instance.document.on('keyup', setModelData);
              });

              instance.on('customConfigLoaded', function () {
                configLoaderDef.resolve();
              });

              ngModel.$render = function () {
                data.push(ngModel.$viewValue);
                if (isReady) {
                  onUpdateModelData();
                }
              };
            };

            if (CKEDITOR.status === 'loaded') {
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

    // Initialize and check CKEditor on application run.
    .run(['$q', '$timeout', function ($q, $timeout) {
      $defer = $q.defer();

      if (angular.isUndefined(CKEDITOR)) {
        throw new Error('CKEDITOR not found');
      }

      CKEDITOR.disableAutoInline = true;

      function checkLoaded() {
        if (CKEDITOR.status === 'loaded') {
          loaded = true;
          $defer.resolve();
        } else {
          checkLoaded();
        }
      }

      CKEDITOR.on('loaded', checkLoaded);
      $timeout(checkLoaded, 100);
    }]);
})();
