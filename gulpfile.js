
const gulp = require('gulp'),
clean = require('gulp-clean'),
concatCss = require('gulp-concat-css'),
cssnano = require('gulp-cssnano'),
autoprefixer = require('gulp-autoprefixer'),
cssimport = require('gulp-cssimport'),
options = {},
rename = require('gulp-rename');

gulp.task('all-css', function () {
    return new Promise(async (resolve, reject) => {
        await gulp.src('src/css/whole.css')
            .pipe(cssimport(options))
            .pipe(gulp.dest('build/'))
            .on('finish', function(){
                gulp.src('build/whole.css')
                .pipe(autoprefixer('last 3 version'))
                .pipe(concatCss('custom-styles.min.css'))
                .pipe(cssnano())
                .pipe(gulp.dest('build/'));
                resolve();
            })
            .on('error', reject)
    })
});

gulp.task('watch', function () {
    gulp.watch(['build/blocks/**/*.css', 'src/css/custom.css', 'src/css/grid.css', 'src/css/vars.css', 'src/css/whole.css', 'src/acf-blocks/*/style.css', 'src/acf-blocks/*/editor.css']).on(
        'change',
        gulp.series(
            'default'
        )
    );
});

gulp.task('clean-blocks', function () {
    return gulp.src(['build/*.min.css'], {
            read: false,
            allowEmpty: true,
        })
        .pipe(clean());
});

gulp.task('public-block-styles', done => {
        gulp.src(['build/blocks/*/style-index.css', 'src/acf-blocks/*/style.css'])
        .pipe(autoprefixer('last 3 version'))
        .pipe(concatCss('public-block-styles.min.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('build/'));
        done();
});

gulp.task('admin-block-styles', done => {
        gulp.src(['build/blocks/*/style-index.css', 'build/blocks/*/index.css', 'src/acf-blocks/*/style.css', 'src/acf-blocks/*/editor.css'])
        .pipe(autoprefixer('last 3 version'))
        .pipe(concatCss('admin-block-styles.min.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('build/'));
        done();
});

gulp.task(
    'default',
    gulp.series(
        'all-css',
        'public-block-styles',
        'admin-block-styles',
        'clean-blocks',
    )
);
