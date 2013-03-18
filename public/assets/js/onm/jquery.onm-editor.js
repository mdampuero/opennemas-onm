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

            var presets = {

                simple : {
                    plugins: 'basicstyles,clipboard,list,indent,enterkey,entities,link,pastetext,toolbar,undo,wysiwygarea',
                    forcePasteAsPlainText : true,
                    removeButtons: 'Anchor,Underline,Strike,Subscript,Superscript',
                    toolbarGroups: [
                      { name: 'document',      groups: [ 'mode', 'document', 'doctools' ] },
                      { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                      { name: 'forms' },
                      { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                      { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                      { name: 'links' },
                      { name: 'insert' },
                      { name: 'styles' },
                      { name: 'colors' },
                      { name: 'tools' },
                      { name: 'others' },
                      { name: 'about' }
                    ]
                },

                full : {
                    plugins: 'a11yhelp,basicstyles,blockquote,clipboard,contextmenu,elementspath,enterkey,entities,filebrowser,floatingspace,font,format,horizontalrule,htmlwriter,image,indent,link,list,magicline,maximize,pastefromword,pastetext,removeformat,resize,scayt,sourcearea,specialchar,stylescombo,tab,table,tabletools,toolbar,undo,wsc,wysiwygarea',
                    removeButtons: '',
                    toolbarGroups: [
                      { name: 'document',      groups: [ 'mode', 'document', 'doctools' ] },
                      { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                      { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                      { name: 'forms' },
                      '/',
                      { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                      { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                      { name: 'links' },
                      { name: 'insert' },
                      '/',
                      { name: 'styles' },
                      { name: 'colors' },
                      { name: 'tools' },
                      { name: 'others' },
                      { name: 'about' }
                    ]
                  },

                  standard: {}

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
                    magicline_color: 'blue'
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