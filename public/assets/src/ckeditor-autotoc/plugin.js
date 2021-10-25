CKEDITOR.plugins.add('autotoc', {
  lang: [ 'es', 'en' ],
  icons: 'autotoc',
  init: function(editor) {
    editor.addCommand('autotoc', {
      exec: function(editor) {
        var template = '<ul[style]>[contents]</ul>';
        var body     = editor.getData();

        body         = body + '[end]';
        var contents = '';

        var generate = function(text) {
          var regex  = new RegExp('<(h([1-5]).*?)>.*?(<h|\\[end\\])', 's');
          var header = text.match(regex);

          if (!header || header.length === 0) {
            return '';
          }

          var id = text.match(/id="(.+?)"/);

          id = id ? id[1] : '';

          var titleRegex = new RegExp('<h' + header[2] + '.*?>(.+?)<');
          var title      = header[0].match(titleRegex);

          title = title && title.length > 0 ? title[1] : '';

          contents += '<li><a href="#' + id + '">' + title + '</a></li>';

          body = body.replace(header[0].substr(0, header[0].length - 2), '');

          generate(body);
        };

        generate(body);

        template = template.replace('[style]', '');
        template = template.replace('[contents]', contents);

        var result =  '[toc]' + editor.getData();

        editor.setData(result.replace('[toc]', template));
      }
    });

    editor.ui.addButton('Automatic table of contents', {
      label: editor.lang.autotoc.toolbar,
      command: 'autotoc',
      toolbar: 'links',
      icon: '/assets/src/ckeditor-autotoc/icons/autotoc.png'
    });
  }
});
