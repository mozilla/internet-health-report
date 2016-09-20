var config       = require('../config')
if(!config.tasks.lint) return

var gulp         = require('gulp');
var eslint       = require('gulp-eslint');
var path         = require('path');
var browserSync  = require('browser-sync');

var lintTask = function() {
  var src = path.join(config.root.src, config.tasks.lint.src, '/**/*.{' + config.tasks.lint.extensions + '}');

  return gulp.src(src)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(browserSync.stream());
}

gulp.task('lint', lintTask)
module.exports = lintTask
