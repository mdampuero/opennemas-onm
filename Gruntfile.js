module.exports = function(grunt) {
  grunt.initConfig({
    bower: {
      install: {
        options: {
          cleanTargetDir: false,
          copy:           false,
          verbose:        true,
          targetDir:      'public/assets/components',
        }
      }
    },
    symlink: {
      options: {
        overwrite: true
      },
      ckeditorAutokeywords: {
        src:  'public/assets/src/ckeditor-autokeywords',
        dest: 'public/assets/components/ckeditor/plugins/autokeywords'
      },
      ckeditorPasteSpecial: {
        src:  'public/assets/src/ckeditor-pastespecial',
        dest: 'public/assets/components/ckeditor/plugins/pastespecial'
      },
      imageresize: {
        src:  'public/assets/components/imageresize',
        dest: 'public/assets/components/ckeditor/plugins/imageresize'
      },
      wordcount: {
        src:  'public/assets/components/wordcount/wordcount',
        dest: 'public/assets/components/ckeditor/plugins/wordcount'
      }
    }
  });

  grunt.loadNpmTasks('grunt-bower-task');
  grunt.loadNpmTasks('grunt-contrib-symlink');

  grunt.registerTask('install', [ 'bower:install', 'symlink' ]);
};
