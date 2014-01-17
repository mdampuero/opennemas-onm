/******************************************
 * Onm Editor
 *
 * Wrapper for configuring WYSIWYG editors
 *
 * @author          Openhost developers <developers@openhost.es>
 * @version         1.0
 *
 ******************************************/
;(function($){
    $.extend({
        onmEditor: function( options ) {

            var load_plugins = 'webkitdrag,autogrow,autokeywords,a11yhelp,basicstyles,blockquote,clipboard,contextmenu,elementspath,enterkey,entities,filebrowser,floatingspace,font,format,justify,horizontalrule,htmlwriter,image,indent,link,list,magicline,maximize,pastefromword,pastetext,pastespecial,removeformat,resize,scayt,sourcearea,stylescombo,tab,table,tabletools,toolbar,undo,wsc,wordcount,wysiwygarea';
            var presets = {

                simple : {
                    plugins: load_plugins,
                    removeButtons: 'Anchor,Strike,Subscript,Superscript,Font,Format,Styles,Cut,Copy,Paste,PasteText',
                    toolbarGroups: [
                      { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                      { name: 'align', groups: [ 'align' ]},
                      { name: 'editing',     groups: [ 'find', 'selection' ] },
                      { name: 'forms' },
                      { name: 'styles' },
                      // { name: 'clipboard',   groups: [ 'PasteSpecial', 'clipboard' ] },
                      { name: 'links' },
                      { name: 'others', groups: [ 'undo' ] },
                      { items : [ 'Image' ] },
                      { name: 'about' },
                      { name: 'document',      groups: [ 'mode', 'document', 'doctools' ] }
                    ],
                    autoGrow_onStartup: true,
                    autoGrow_maxHeight: 500,
                    wordcount: {
                        showWordCount: true,
                        showCharCount: false
                    }
                },

                full : {
                    plugins: load_plugins,
                    removeButtons: 'Cut,Copy,Paste,PasteText',
                    toolbarGroups: [
                      { name: 'align', groups: [ 'align' ]},
                      { name: 'document',      groups: [ 'mode', 'document', 'doctools' ] },
                      { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                      { name: 'forms' },
                      '/',
                      { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                      { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                      { name: 'links' },
                      { name: 'insert' },
                      '/',
                      // { name: 'clipboard',   groups: [ 'clipboard' ] },
                      { name: 'styles' },
                      { name: 'colors' },
                      { name: 'tools' },
                      { name: 'others', groups: [ 'undo' ] },
                      { name: 'about' }
                    ],
                    autoGrow_onStartup: true,
                    autoGrow_maxHeight: 500
                  },

                  standard: {
                    plugins: load_plugins,
                    removeButtons: 'Strike,Subscript,Superscript,Cut,Copy,Paste,PasteText',
                    toolbarGroups : [
                        { name: 'styles' },
                        // { name: 'clipboard',   groups: [ 'clipboard' ] },
                        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                        { name: 'forms' },
                        { name: 'others' , groups: [ 'undo' ]},
                        { name: 'tools' },
                        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
                        '/',
                        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                        { name: 'align', groups: [ 'align' ]},
                        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks' ] },
                        { name: 'links' },
                        { name: 'insert' },
                        { name: 'colors' },
                        { name: 'about' }
                    ],
                    autoGrow_onStartup: true,
                    autoGrow_maxHeight: 500
                  }
            };

            var defaultOptions = {
                language: "en",
                editor_class: ".onm-editor",
                default_preset: "standard"
            };

            var settings = $.extend({}, defaultOptions, options);

            $(settings.editor_class).each(function(item) {
                var id = $(this).attr('id');
                var data = $(this).data();

                var editor_configuration = {
                    language: settings.language,
                    extraPlugins: 'magicline,font',
                    magicline_color: 'blue',
                    forcePasteAsPlainText : false,
                    fillEmptyBlocks : false,
                    ignoreEmptyParagraph : true,
                };

                var editor_preset;
                if (data['preset'] !== undefined) {
                    editor_preset = data['preset'];
                } else {
                    editor_preset = settings.default_preset;
                }

                editor_configuration = $.extend({}, presets[editor_preset], editor_configuration);

                CKEDITOR.replace(id, editor_configuration);
            });
        }
    });
})(jQuery);
