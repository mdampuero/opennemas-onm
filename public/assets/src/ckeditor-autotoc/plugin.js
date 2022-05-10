CKEDITOR.plugins.add('autotoc', {
  lang: [ 'es', 'en' ],
  icons: 'autotoc',
  init: function(editor) {
    editor.addCommand('autotoc', {
      exec: function(editor) {
        var template = '<ol class="table-of-contents">[contents]</ol>';
        var body     = editor.getData();
        var contents = '';
        var ids      = [];

        var slugify = function(str) {
          str = str.replace(/^\s+|\s+$/g, '');
          str = str.toLowerCase();

          var from    = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;#"\'()$';
          var to      = 'aaaaeeeeiiiioooouuuunc------------';
          var escape  = '()$\'';

          for (var i = 0, l = from.length; i < l; i++) {
            var char = from.charAt(i);

            if (escape.includes(char)) {
              char = '\\' + char;
            }

            str = str.replace(new RegExp(char, 'g'), to.charAt(i));
          }

          str = str.replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

          return str;
        };

        var generate = function(text) {
          var text    = text + '[end]';
          var element = text.match(/<(h([1-6]).*?)>.*?(<h\2|\[end\])/);

          if (!element) {
            return '';
          }

          var header = element[0].match(/<(h([1-6]).*(id="(.*?)")?)>(.*?)(<\/h\2>)/);

          if (!header) {
            return '';
          }

          var title  = header[5] ? header[5] : '';
          var id     = header[4] ? header[4] : slugify(title);

          ids.push(id);

          contents += '<li><a href="#' + id + '">' + title + '</a><ul>';

          generate(element[0].substr(3));

          contents += '</ul>';

          text = text.replace(element[0].substr(0, element[0].length - 3), '');

          generate(text);
        };

        generate(body.replace(/(\r\n|\n|\r)/gm, ''));

        template = template.replace('[style]', '');
        template = template.replace('[contents]', contents);

        var result =  '[toc]' + editor.getData();

        var headers = result.match(/<h[1-6].*>/g);

        if (headers) {
          for (var i = 0; i < headers.length; i++) {
            var resultHeader = headers[i].replace(/id="[^"]"/, '')
              .replace(/<h([0-9])/, '<h$1 id="' + ids[i] + '"');

            result = result.replace(headers[i], resultHeader);
          }
        }

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
