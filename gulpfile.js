'use strict';

const gulp        = require('gulp');
const sass        = require('gulp-sass')(require('sass'));
const postcss     = require('gulp-postcss');
const autoprefixer= require('autoprefixer');
const cssnano     = require('cssnano');
const terser      = require('gulp-terser');
const rename      = require('gulp-rename');
const sourcemaps  = require('gulp-sourcemaps');

const paths = {
  scss:  'sass/main.scss',
  scssWatch: 'sass/**/*.scss',
  js:    'js/main.js',
  dest:  './',
  jsDest:'js/',
};

// ---- CSS ----
function css() {
  return gulp.src(paths.scss)
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'expanded' }).on('error', sass.logError))
    .pipe(postcss([ autoprefixer() ]))
    .pipe(rename('style.css'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.dest));
}

function cssMin() {
  return gulp.src(paths.scss)
    .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
    .pipe(postcss([ autoprefixer(), cssnano({ preset: 'default' }) ]))
    .pipe(rename('style.min.css'))
    .pipe(gulp.dest(paths.dest));
}

// ---- JS ----
function js() {
  return gulp.src(paths.js)
    .pipe(terser())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.jsDest));
}

// ---- Watch ----
function watch() {
  gulp.watch(paths.scssWatch, css);
  gulp.watch(paths.js, js);
}

// ---- Exports ----
exports.css   = css;
exports.cssMin= cssMin;
exports.js    = js;
exports.watch = watch;
exports.build = gulp.series(css, cssMin, js);
exports.default = gulp.series(css, js, watch);
