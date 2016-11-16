/*
/ Required plugins
*/

var gulp = require('gulp');
var gutil = require('gulp-util');

// CSS
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var cssnano = require('gulp-cssnano');

// JS
var babelify = require('babelify');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');

// Server
var browserSync = require('browser-sync');

// Browserify
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var browserify = require('browserify');

// Images
var plumber = require('gulp-plumber');
var newer = require('gulp-newer');
var imagemin = require('gulp-imagemin');
var pngcrush = require('imagemin-pngcrush');

/*
/ Build flags
*/

var isProduction = true;

if (gutil.env.dev === true) {
  isProduction = false;
}

var onError = function(err) {
  console.log(err);
  this.emit('end');
};

/*
/ Paths
*/

var src = 'src/';
var dist = '';

var paths = {
  css: {
    src: src + 'stylesheets/**/*.scss',
    dist: 'css/',
  },

  js: {
    app: {
      modules: src + 'javascripts/modules/*js',
      src: src + 'javascripts/app.js',
      dist: 'js/',
    },
  },

  img: {
    src: src + 'images/**',
    dist: 'assets/img/',
  },
};

/*
/ Run Sass, autoprefix, minify
*/

gulp.task('sass', function() {
  gulp.src(paths.css.src)
    .pipe(sass({
      includePaths: [
        "./node_modules/bourbon/app/assets/stylesheets",
        "./node_modules/bourbon-neat/app/assets/stylesheets",
        "./node_modules/normalize.css"
      ],
    }).on('error', sass.logError))
    .pipe(isProduction ? cssnano() : gutil.noop())
    .pipe(autoprefix({
      browsers: ['ie >= 9', 'last 2 versions'],
    }))
    .pipe(gulp.dest(paths.css.dist));
});

/*
/ Concatanate and minify main scripts
*/

gulp.task('js', function() {
  var bundler = browserify({
    entries: paths.js.app.src,
    debug: true,
  });
  bundler.transform(babelify);

  bundler.bundle()
    .on('error', function(err) { console.error(err); })
    .pipe(source('app.js'))
    .pipe(buffer())
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(isProduction ? uglify() : gutil.noop())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(paths.js.app.dist));
});

/*
/ Minify images
*/

gulp.task('imagemin', function() {
  gulp.src(paths.img.src)
    .pipe(plumber())
    .pipe(newer(paths.img.dist))
    .pipe(imagemin({
      progressive: true,
      svgoPlugins: [{ removeViewBox: false }],
      use: [pngcrush()],
    }))
    .pipe(gulp.dest(paths.img.dist));
});

/*
/ Local server
*/

gulp.task('browser-sync', function() {
  browserSync.init([dist], {
    notify: false,
    server: {
      baseDir: './' + dist
    },
  });
});

/*
/ Watch
*/

gulp.task('watch', function() {
  gulp.watch(paths.css.src, ['sass']);
  gulp.watch([paths.js.app.src, paths.js.app.modules], ['js']);
  // gulp.watch(paths.img.src, ['imagemin']);
});

/*
/ Default
*/

gulp.task('default', ['watch']);
