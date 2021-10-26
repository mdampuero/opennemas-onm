CKEDITOR.plugins.add('autotoc', {
  lang: [ 'es', 'en' ],
  icons: 'autotoc',
  init: function(editor) {
    editor.addCommand('autotoc', {
      exec: function(editor) {
        var template = '<ol[style]>[contents]</ol>';
        var body     = editor.getData();
        var contents = '';

        body = body + '[end]';

        var generate = function(text, level) {
          // Select the correct header
          var pattern = '<(h([1-5]).*?)>.*?(<h\\2|\\[end\\])';

          if (level && !isNaN(parseInt(level))) {
            pattern = pattern.replace('[1-5]', parseInt(level) + 1);
            text    = text + '[end]';
          }

          var regex  = new RegExp(pattern, 's');
          var header = text.match(regex);

          if (!header || header.length === 0) {
            return '';
          }

          var id = text.match(/id="(.+?)"/);

          id = id ? id[1] : '';

          var titleRegex = new RegExp('<h' + header[2] + '.*?>(.+?)</h' + header[2]);
          var title      = header[0].match(titleRegex);

          title = title && title.length > 0 ? title[1] : '';

          // Generate the code for the selected header
          contents += '<li><a href="#' + id + '">' + title + '</a><ul>';

          generate(header[0], header[2]);

          // Remove the processed header
          contents += '</ul></li>';

          pattern = '<(h([1-5]).*?)>.*?(<h|\\[end\\])';

          if (level && !isNaN(parseInt(level))) {
            pattern = pattern.replace('[1-5]', parseInt(level) + 1);
          }

          regex = new RegExp(pattern, 's');

          header = text.match(regex);
          body   = body.substr(header[0].length - 2);

          generate(body, level);
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
