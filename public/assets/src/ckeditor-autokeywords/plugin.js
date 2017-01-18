CKEDITOR.plugins.add( 'autokeywords', {
    lang: ['es', 'en'],
    icons: 'autokeywords',
    init: function( editor ) {
        editor.addCommand( 'replaceKeywords', {
            exec: function( editor ) {
                $.ajax({
                    url: '/admin/keywords/autolink',
                    type : "POST",
                    data : {
                        'text' : editor.getData()
                    },
                    success : function(text) {
                        editor.setData(text);
                    }
                });
            }
        });

        editor.ui.addButton( 'Automatic keywords', {
            label: editor.lang.autokeywords.toolbar,
            command: 'replaceKeywords',
            toolbar: 'links',
            icon: '/assets/src/ckeditor-autokeywords/icons/autokeywords.png'
        });
    }
});
