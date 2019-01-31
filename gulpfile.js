var gulp = require('gulp'),
  watch = require('gulp-watch'),
  sass = require('gulp-sass'),
  livereload = require('gulp-livereload'),
  sourcemaps = require('gulp-sourcemaps');
 
gulp.task('sass', function() {
  gulp.src('css/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('css'))
    .pipe(livereload());
});

gulp.task('css', function() {
  gulp.src('css/*.css')
    .pipe(livereload());
});
 
gulp.task('watch', function() {
  livereload.listen();
  gulp.watch('css/*.scss', ['sass']);
  gulp.watch('css/*.css', ['css']);
});
 
gulp.task('default', ['watch']);