CKEDITOR.plugins.add('autonofollow', {
  lang: [ 'es', 'en' ],
  icons: 'autonofollow',
  init: function(editor) {
    editor.addCommand('addNoFollow', {
      exec: function(editor) {
        var data = editor.getData();

        editor.setData(
          data.replace(/href="http/g, 'rel="nofollow" href="http')
        );
      }
    });

    editor.ui.addButton('Automatic nofollow links', {
      label: editor.lang.autonofollow.toolbar,
      command: 'addNoFollow',
      toolbar: 'links',
      icon: '/assets/src/ckeditor-autonofollow/icons/autonofollow.png'
    });
  }
});
