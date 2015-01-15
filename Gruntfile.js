module.exports = function(grunt) {
  grunt.initConfig({
    bower: {
      install: {
        options: {
          cleanTargetDir: false,
          copy:           false,
          targetDir:      'public/assets/components',
        }
      }
    },
    symlink: {
      options: {
        overwrite: false
      },
      imageresize: {
        src:  'public/assets/components/imageresize',
        dest: 'public/assets/components/ckeditor/plugins/imageresize'
      },
      ckeditorAutokeywords: {
        src:  'public/assets/components/opennemas/ckeditor-autokeywords',
        dest: 'public/assets/components/ckeditor/plugins/autokeywords'
      },
      ckeditorPasteSpecial: {
        src:  'public/assets/components/opennemas/ckeditor-pastespecial',
        dest: 'public/assets/components/ckeditor/plugins/pastespecial'
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
