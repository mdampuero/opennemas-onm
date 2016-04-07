(function () {
  'use strict';

  var batch      = require('gulp-batch');
  var exec       = require('child_process').exec;
  var gulp       = require('gulp');
  var livereload = require('gulp-livereload');
  var notifier   = require('node-notifier');
  var path       = require('path');

  gulp.task('phpunit', function () {
    exec('./vendor/phpunit/phpunit/phpunit 2>&1',
      function(error, stdout) {
        var title   = '';
        var icon    = 'fail.png';

        // Remove trainling NL and get the last one
        var report = stdout.replace(/\n$/, '').split(/\r?\n/);
        report = report[report.length - 1];
        if (report.indexOf('Tests') !== -1 || report.indexOf('OK') === -1) {
          title   = 'Unable to complete the tests!';
          icon    = 'fail.png';

          console.log(stdout);
        } else {
          title   = 'Tests executed!';
          icon    = 'pass.png';
        }

        notifier.notify({
          'title':   title,
          'icon':    path.join(__dirname, 'public/assets/images', icon),
          'message': report
        });
      }
    );
  });

  gulp.task('touch', function () {
    exec('find public -type f -name main.less | xargs touch');
  });

  gulp.task('watch', function () {
    livereload.listen();

    // Executes tests and reload browser
    gulp.watch(
      ['app/models/**/*.php', 'libs/**/*.php', 'src/**/*.php', 'public/themes/**/*.tpl', 'tests/**/*.php'],
      {
        interval: 1000, // default 100
        debounceDelay: 1000, // default 500
      },
      batch(function (events, done) {
        gulp.start('phpunit', done);
        livereload.reload();
      })
    );

    // Executes tests and reload browser
    gulp.watch(
      ['public/assets/src/**/*.less', 'public/themes/**/*.less', '!public/assets/src/**/main.less', '!public/themes/**/main.less', ],
      {
        interval: 1000, // default 100
        debounceDelay: 1000, // default 500
      },
      batch(function (events, done) {
          gulp.start('touch', done);
          livereload.reload();
      })
    );
  });

  gulp.task('default', [ 'watch' ]);
})();
