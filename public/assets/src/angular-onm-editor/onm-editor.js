/**
* onm.media-picker Module
*
* Creates a media picker modal to upload/insert contents.
*/
angular.module('onm.editor', [])
.directive('onmeditor', ['$timeout', '$q',
  function ($timeout, $q) {
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
            options = {},
            loaded = false,
            isReady = false;

            // Define tje fallback preset to standard
            var selectedPreset = attrs['onmeditorPreset'] || 'standard';

            // Definitions for each ckeditor preset (buttons to show)
            var presets = {
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

            // Common options
            var commonOptions = {
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
              preset: selectedPreset,
              width: '100%'
            };

            options = angular.extend(commonOptions, presets[selectedPreset]);

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

              var instance = (isTextarea) ? CKEDITOR.replace(element[0], options) : CKEDITOR.inline(element[0], options),
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
    ]
  );
