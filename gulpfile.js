(function () {
  'use strict';

  var batch      = require('gulp-batch');
  var exec       = require('child_process').exec;
  var gulp       = require('gulp');
  var livereload = require('gulp-livereload');
  var notifier   = require('node-notifier');
  var path       = require('path');

  gulp.task('phpunit', function () {
    exec('./vendor/phpunit/phpunit/phpunit -c app/phpunit.xml.dist 2>&1 | tail -1',
      function(error, stdout) {
        var title, message = '';
        var summary        = stdout;

        if (summary.indexOf('Tests') === -1 && summary.indexOf('OK') === -1) {
          title   = 'Unable to complete the tests!';
          icon    = 'fail.png';
          message = 'There was an error while executing tests'
        } else {
          icon    = 'pass.png'
          title   = 'Tests executed!',
          message = summary
        }

        if (summary.indexOf('Tests') !== -1) {
          icon = 'fail.png'
        }

        notifier.notify({
          'title':   title,
          'icon':    path.join(__dirname, 'public/assets/images', icon),
          'message': message
        });
      }
    );
  });

  gulp.task('touch', function () {
    exec('find -type f -name main.less | xargs touch');
  });

  gulp.task('watch', function () {
    livereload.listen();

    // Executes tests and reload browser
    gulp.watch([ 'app/models/**/*.php', 'libs/**/*.php', 'src/**/*.php' ],
      batch(function (events, done) {
        gulp.start('phpunit', done);
        livereload.reload();
      }));

    // Executes tests and reload browser
    gulp.watch([ 'public/assets/src/**/*.less', 'public/themes/**/*.less',
      '!public/assets/src/**/main.less', '!public/themes/**/main.less',
    ],
      batch(function (events, done) {
        gulp.start('touch', done);
        livereload.reload();
      }));
  });

  gulp.task('default', [ 'watch' ]);
})();
