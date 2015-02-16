/**
* onm.media-picker Module
*
* Creates a media picker modal to upload/insert contents.
*/
angular.module('onm.Editor', [])
  .provider('onmEditor', [
    function() {
      /**
       * Default options for CKEditor.
       *
       * @type Object
       */
      this.defaults = {
        plugins: 'a11yhelp,about,imageresize,autogrow,autokeywords,basicstyles,blockquote,clipboard,contextmenu,elementspath,enterkey,entities,filebrowser,floatingspace,font,format,justify,horizontalrule,htmlwriter,image,indent,link,list,magicline,maximize,pastefromword,pastetext,pastespecial,removeformat,resize,scayt,sourcearea,stylescombo,tab,table,tabletools,toolbar,undo,wsc,wordcount,wysiwygarea',
        disableNativeSpellChecker: false,
        uiColor: '#E5E9EC',
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
        width: '100%'
      };

      /**
       * Presets for CKEditor.
       *
       * @type Object
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
       * Creates a new configuration for a CKEditor.
       *
       * @param string preset The new value for the preset.
       */
      this.configure = function(preset) {
        var presets = Object.keys(this.presets);

        if (presets.indexOf(preset) == -1) {
          preset = this.defaults.preset;
        }

        return angular.extend({}, this.defaults, this.presets[preset]);
      }

      /**
       * Registers one or more resources to be loaded from an external path.
       *
       * @param string names    The resource names.
       * @param string path     The path of the folder.
       * @param string filename The resource file name.
       */
      this.addExternal = function(names, path, filename) {
        CKEDITOR.plugins.addExternal(names, path, filename);
      }

      /**
       * Initializes a CKEditor.
       *
       * @param boolean replace Whether to replace the given element.
       * @param Object  element The element for the CKEditor.
       * @param Object  options The options for the CKEditor.
       *
       * @return The CKEditor instance.
       */
      this.init = function(replace, element, options) {
        if (replace) {
          return CKEDITOR.replace(element, options);
        }

        return CKEDITOR.inline(element, options);
      }

      /**
       * Returns the CKEditor instance given its name.
       *
       * @param string name The CKEditor instance name.
       *
       * @return Object The CKEditor instance.
       */
      this.get = function(name) {
        if (CKEDITOR.instances[name]) {
          return CKEDITOR.instances[name];
        }

        return false;
      }

      /**
       * Returns the current service.
       *
       * @return Object The current object.
       */
      this.$get = function () {
          return this;
      };
    }
  ])
  .directive('onmEditor', [
    '$timeout', '$q', 'onmEditor',
    function ($timeout, $q, onmEditor) {
      'use strict';

      return {
        restrict: 'A', // E = Element, A = Attribute, C = Class, M = Comment
        require: [],
        scope: {},
        // controller: 'CkEditorCtrl',
        link: function (scope, element, attrs, ctrls) {
          var ngModel = ctrls[0];
          var form    = ctrls[1] || null;
          var EMPTY_HTML = '<p></p>',
          isTextarea = element[0].tagName.toLowerCase() == 'textarea',
          data = [],
          loaded = false,
          isReady = false;

          var options = onmEditor.configure(attrs['onmEditorPreset']);

          if (!isTextarea) {
            element.attr('contenteditable', true);
          }

          var onLoad = function () {
            // // you can use readonly attribute to bind a variable
            // // to set the editor readOnly status
            // if (attrs.readonly) {
            //     // if ckreadonly attribute is present,
            //     // set editor readOnly option
            //     var isReadOnly = scope.$eval(attrs.ckreadonly);
            //     options.readOnly = isReadOnly;

            //     // setup a watch on the attribute value
            //     // to update the editor readOnly mode
            //     // when value changes
            //     scope.$watch(attrs.ckreadonly, function (value) {
            //         // ignore callback if editable instance
            //         // is not ready yet
            //         if (instance && isReady) {
            //             instance.setReadOnly(value);
            //         }
            //     });
            // }

            var instance = onmEditor.init(isTextarea, element[0], options),
            configLoaderDef = $q.defer();

            // element.bind('$destroy', function () {
            //     instance.destroy(
            //         false //If the instance is replacing a DOM element, this parameter indicates whether or not to update the element with the instance contents.
            //     );
            // });

            // var setModelData = function(setPristine) {
            //     var data = instance.getData();
            //     if (data == '') {
            //         data = null;
            //     }
            //     $timeout(function () { // for key up event
            //         (setPristine !== true || data != ngModel.$viewValue) && ngModel.$setViewValue(data);
            //         (setPristine === true && form) && form.$setPristine();
            //     }, 0);
            // }, onUpdateModelData = function(setPristine) {
            //     if (!data.length) { return; }


            //     var item = data.pop() || EMPTY_HTML;
            //     isReady = false;
            //     instance.setData(item, function () {
            //         setModelData(setPristine);
            //         isReady = true;
            //     });
            // }

            // //instance.on('pasteState',   setModelData);
            // instance.on('change',       setModelData);
            // instance.on('blur',         setModelData);
            // //instance.on('key',          setModelData); // for source view

            // instance.on('instanceReady', function() {
            //     scope.$broadcast("editor.ready");
            //     scope.$apply(function() {
            //         onUpdateModelData(true);
            //     });

            //     instance.document.on("keyup", setModelData);
            // });
            // instance.on('customConfigLoaded', function() {
            //     configLoaderDef.resolve();
            // });

            // ngModel.$render = function() {
            //     data.push(ngModel.$viewValue);
            //     if (isReady) {
            //         onUpdateModelData();
            //     }
            // };
          };

          if (CKEDITOR.status == 'loaded') {
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
  ]);
