var gulp = require('gulp');
var zip = require('gulp-zip');
var composer = require('gulp-composer');

gulp.task('build', function () {
    return gulp.src(['kintone_form/**/*'], {base: "."})
        .pipe(gulp.dest('./build'))
        .pipe(composer({ cwd: './build/kintone_form' }));
});

gulp.task('zip', function () {
    return gulp.src(['build/**/*'], {base: "./build"})
        .pipe(zip('kintone_form.zip'))
        .pipe(gulp.dest('./release'));
});

gulp.task('default', ['build']);