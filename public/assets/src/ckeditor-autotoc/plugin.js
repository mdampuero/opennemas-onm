CKEDITOR.plugins.add('autotoc', {
  lang: [ 'es', 'en' ],
  icons: 'autotoc',
  init: function(editor) {
    editor.addCommand('autotoc', {
      exec: function(editor) {
        var template = '<ol[style]>[contents]</ol>';
        var body     = editor.getData();
        var contents = '';

        var generate = function(text) {
          var text    = text + '[end]';
          var element = text.match(/<(h([1-5]).*?)>.*?(<h\2|\[end\])/s);
          
          if (!element) {
            return '';
          }

          var header = element[0].match(/<(h(?<level>[1-5])(.*id="(?<id>.*?)")?)>(?<title>.*?)(<\/h\2>)/s);
          var id     = header.groups.id ? header.groups.id : '';
          var title  = header.groups.title ? header.groups.title : '';

          contents += '<li><a href="#' + id + '">' + title + '</a><ul>';

          generate(element[0].substr(3));

          contents += '</ul>';

          text = text.replace(element[0].substr(0, element[0].length - 3), '');

          generate(text);
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
