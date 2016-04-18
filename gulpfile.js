(function () {
  'use strict';

  var batch      = require('gulp-batch');
  var exec       = require('child_process').exec;
  var gulp       = require('gulp');
  var livereload = require('gulp-livereload');
  var logme      = require('logme');
  var notifier   = require('node-notifier');

  // Configuration
  var config = {
    icons: {
      error:   __dirname + '/public/assets/images/error.png',
      success: __dirname + '/public/assets/images/success.png',
      warning: __dirname + '/public/assets/images/warning.png'
    },
    paths: {
      ag: [
        'src',
        'libs',
        'public/themes/admin',
        'public/themes/manager',
        'public/themes/*/*/*/*.php'
      ],
      phpunit: [
        'src/**/*.php',
        'libs/**/*.php',
        'public/themes/**/*.tpl',
        'public/themes/**/*.php',
        'tests/**/*.php'
      ],
      js: [],
      touch: [
        'public/assets/src/**/*.less',
        'public/themes/**/*.less',
        '!public/assets/src/**/main.less',
        '!public/themes/**/main.less',
      ],
    },
    watch: {
      interval:      1000, // default 100
      debounceDelay: 1000, // default 500
    }
  };

  // Executes test cases
  gulp.task('phpunit', function () {
    exec('./vendor/phpunit/phpunit/phpunit 2>&1',
      function(error, stdout) {
        var title = 'Tests executed!';
        var icon  = config.icons.success;
        var type  = 'info';

        // Remove trailing NL and get the last one
        var report = stdout.replace(/\n$/, '').replace(/\n/g, '\n  ');
        var result = report.split(/\r?\n/).pop();

        if (result.indexOf('Tests') !== -1 || result.indexOf('OK') === -1) {
          title  = 'Unable to complete the tests!';
          icon   = config.icons.error;
          type   = 'error';
        }

        logme.log(type, title + '\n  ' + report);
        notifier.notify({ 'title': title, 'icon': icon, 'message': result });
      }
    );
  });

  // Touches less files
  gulp.task('touch', function () {
    exec('find public -type f -name main.less | xargs touch');
  });

  // Searches debug messages in files
  gulp.task('search', function () {
    var cmd = 'ag -l "(var_dump|console.log)\\(.*\\)" ' +
      config.paths.ag.join(' ') +
      ' --ignore adodb5 --ignore webarch --ignore scripts.js';

    exec(cmd, function(error, stdout) {
      if (stdout) {
        notifier.notify({
          'title':   'Check your code, dude!',
          'icon':    config.icons.warning,
          'message': 'Debug messages found in ' +
              stdout.replace(/\n$/, '').split(/\r?\n/).length + ' files',
        });

        logme.warning('Debug messages found in the following files:\n  ' +
            stdout.replace(/\n/g, '\n  '));
      }
    });
  });

  // Watches files and executes tasks on change
  gulp.task('watch', function () {
    livereload.listen();

    // Executes test cases and reloads the browser
    gulp.watch(config.paths.phpunit, config.watch,
      batch(function (events, done) {
        gulp.start('phpunit', done);
        gulp.start('search', done);
        livereload.reload();
      })
    );

    // Touches less files and reloads the browser
    gulp.watch(config.paths.touch, config.watch,
      batch(function (events, done) {
        gulp.start('touch', done);
        livereload.reload();
      })
    );
  });

  gulp.task('default', [ 'watch' ]);
})();
