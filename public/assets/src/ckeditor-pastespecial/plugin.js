/* eslint-disable no-unused-expressions */
/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

(function() {
  // The pastetext command definition.
  var pasteSpecialCmd = {
    // Snapshots are done manually by editable.insertXXX methods.
    canUndo: false,
    async: true,

    exec: function(editor) {
      editor.getClipboardData({ title: editor.lang.pastetext.title }, function(
          data
      ) {
        var isFirefox =
          navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

        if (isFirefox) {
          var newData = data.dataValue.replace(/<br>/g, '<br><br>');
        } else {
          var newData = data.dataValue
            .replace(/<div><br><\/div>/g, '')
            .replace(/<\/div><div>/g, '</div><div><br></div><div>');
        }

        // Do not use editor#paste, because it would start from beforePaste event.
        data && editor.fire('paste', { type: 'text', dataValue: newData });

        editor.fire('afterCommandExec', {
          name: 'pastetext',
          command: pasteSpecialCmd,
          returnValue: Boolean(data)
        });
      });
    }
  };

  // Register the plugin.
  CKEDITOR.plugins.add('pastespecial', {
    requires: 'clipboard',
    icons: 'pastetext,pastetext-rtl',
    init: function(editor) {
      var commandName = 'pastespecial';

      editor.addCommand(commandName, pasteSpecialCmd);

      editor.ui.addButton &&
        editor.ui.addButton('PasteSpecial', {
          label: editor.lang.pastetext.button,
          command: commandName,
          icon: 'pastetext'
        });

      if (editor.config.forcePasteAsPlainText) {
        editor.on('beforePaste', function(evt) {
          /*
           * Do NOT overwrite if HTML format is explicitly requested.
           * This allows pastefromword dominates over pastetext.
           */
          if (evt.data.type !== 'html') {
            evt.data.type = 'text';
          }
        });
      }

      editor.on('pasteState', function(evt) {
        editor.getCommand(commandName).setState(evt.data);
      });
    }
  });
})();
