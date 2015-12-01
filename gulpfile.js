(function () {
  'use strict';

  var exec       = require('child_process').exec;
  var gulp       = require('gulp');
  var livereload = require('gulp-livereload');
  var notifier   = require('node-notifier');
  var path       = require('path');

  gulp.task('phpunit', function () {
    exec('phpunit -c app/phpunit.xml.dist | tail -1', function(error, stdout) {
      var summary = stdout;

      if (summary.indexOf('Tests') === -1) {
        notifier.notify({
          'title':   'Unable to complete the tests!',
          'icon':    path.join(__dirname, 'public/assets/images/fail.png'),
          'message': 'There was an error while executing tests'
        });

        return;
      }

      var result  = summary.replace(/,|\./g, '').replace(/: /g,':').split(' ');

      var r = {};
      for (var i = 0; i < result.length; i++) {
        var token = result[i].split(':');
        r[token[0]] = parseInt(token[1]);
      }

      var icon = 'pass.png';

      if (r.Errors > 0) {
        icon = 'fail.png';
      }

      notifier.notify({
        'title':   'Tests executed!',
        'icon':    path.join(__dirname, 'public/assets/images', icon),
        'message': summary
      });
    });
  });

  gulp.task('watch', function () {
    livereload.listen();

    gulp.watch([ 'app/models/**/*.php', 'libs/**/*.php', 'src/**/*.php' ],
      function () {
          gulp.start('phpunit');
          livereload.reload();
      });
  });

  gulp.task('default', [ 'watch' ]);
})();


