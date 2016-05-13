var gulp = require('gulp'),
    concatCSS = require('gulp-concat-css'),
    notify = require('gulp-notify'),
    minifyCSS = require('gulp-minify-css'),
    autoprefixer = require('gulp-autoprefixer'),
    rename = require('gulp-rename'),
    connect = require('gulp-connect'),
    less = require('gulp-less'),
    livereload = require('gulp-livereload');


    //connect
    gulp.task("connect", function () {
      connect.server({
        root: 'app',
        livereload: true
      });
    });

//css
// gulp.task('css', function () {
//   gulp.src('app/custom-style/*.css')
//     .pipe(concatCSS("bundle.css"))
//     .pipe(minifyCSS(""))
//     .pipe(autoprefixer("last 2 version", "> 1%", "ie 9"))
//     .pipe(rename("bundle.min.css"))
//     .pipe(gulp.dest('app/css'))
//     .pipe(connect.reload());
//     // .pipe(notify("Done!"));
// });

//less
gulp.task('less', function () {
  gulp.src('app/custom-style/*.less')
    .pipe(less())
    .pipe(concatCSS("bundle.css"))
    .pipe(minifyCSS(""))
    .pipe(autoprefixer("last 2 version", "> 1%", "ie 9"))
    .pipe(rename("bundle.min.css"))
    .pipe(gulp.dest('app/css'))
    .pipe(connect.reload());
    // .pipe(notify("Done!"));
});


//html
gulp.task("html", function () {
  gulp.src('app/index.html')
  .pipe(connect.reload());
});

//js
gulp.task("js", function () {
  gulp.src('app/js/script.js')
  .pipe(connect.reload());
});

//watch
gulp.task("watch", function () {
  gulp.watch('app/custom-style/*.less', ['less'])
  gulp.watch('app/index.html', ['html'])
  gulp.watch('app/js/script.js', ['js'])
});

//default
gulp.task("default", ['connect', 'html', 'less', 'js', 'watch']);
