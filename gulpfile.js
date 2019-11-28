// Include gulp
let gulp = require('gulp');
let sass = require('gulp-sass');
// Include Our Plugins
let jshint = require('gulp-jshint');
let concat = require('gulp-concat');
let terser = require('gulp-terser');
// var sourcemaps = require('gulp-sourcemaps');
let del = require('del'); // Подключаем библиотеку для удаления файлов и папок
let autoprefixer = require('gulp-autoprefixer');// Подключаем библиотеку для автоматического добавления префиксов
let multiDest = require('gulp-multi-dest');
const args = require('yargs').argv;

//var streamCombiner = require('stream-combiner');
// stream-combiner for array in dest
// https://github.com/gulpjs/vinyl-fs/issues/67
// https://www.npmjs.com/package/stream-combiner

let build = {
    'frontend': {
        'build': true,
        'path': 'frontend/web/'
    },
    'backend': {
        'build': true,
        'path': 'backend/web/'
    },
    'terminal': {
        'build': true,
        'path': 'terminal/web/'
    }
};

function foldersNamesTwoFullPaths(folder) { // принимает название папки (строку или массив з нескольких и на основе build[] возвращает массив полных путей к папкам

    let fullPaths = [];
    let folderNames = [];

    if (!folder) {
        folderNames = ['css', 'js', 'img'];
    } else if (Array.isArray(folder)) {
        folderNames = folder;
    } else if (typeof (folder) == 'string' && folder !== '') {
        folderNames = [folder];
    }
    let webDir = args.path;
    if (build[webDir]['build']) {
        for (let i = folderNames.length - 1; i >= 0; i--) {
            let path = build[webDir]['path'] + '/' + folderNames[i];
            fullPaths.push(path);
        }
    }


    return fullPaths; // массив с путями
}

gulp.task('clean', function () {
    return del(foldersNamesTwoFullPaths(['css', 'js', 'img']), {force: true});
});

gulp.task('clean-js', function () {
    return del(foldersNamesTwoFullPaths(['js']), {force: true});
});

gulp.task('clean-css', function () {
    return del(foldersNamesTwoFullPaths(['css']), {force: true});
});

gulp.task('clean-img', function () {
    return del(foldersNamesTwoFullPaths(['img']), {force: true});
});

// Lint Task
gulp.task('lint', function () {
    let webDir = args.path;
    return gulp.src(build[webDir]['path'] + 'source/js/**/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function () {
    let webDir = args.path;
    return gulp.src(build[webDir]['path'] + 'source/scss/**/*.scss')
        .pipe(sass())
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'safari 5', 'ie7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'], {cascade: true})) // Создаем префиксы
        .pipe(multiDest(foldersNamesTwoFullPaths('css')));
});

// Concatenate & Minify JS
gulp.task('scripts', function () {
    let webDir = args.path;
    return gulp.src([build[webDir]['path'] + 'source/js/**/*.js'])
        .pipe(concat('main.js'))
        .pipe(terser())
        .pipe(multiDest(foldersNamesTwoFullPaths('js')));
});

// Переносим картинки в продакшен
gulp.task('img', function () {
    let webDir = args.path;
    return gulp.src([build[webDir]['path'] + 'source/img/**/*',])
        .pipe(multiDest(foldersNamesTwoFullPaths('img')));
});

// Watch Files For Changes
gulp.task('watch', function () {
    let webDir = args.path;
    if (webDir) {
        gulp.watch(build[webDir]['path'] + 'source/scss/**/*.scss', gulp.series('clean-css', 'sass'));
        gulp.watch(build[webDir]['path'] + 'source/js/**/*.js', gulp.series('lint', 'clean-js', 'scripts'));
        gulp.watch(build[webDir]['path'] + 'source/img/**/*', gulp.series('clean-img', 'img'));
    } else {
        throw 'Ошибка. Не указан параметр --path [terminal, backend, frontend]';
    }
});

gulp.task('build', function () {
    gulp.series('clean', gulp.parallel('img', 'sass', 'scripts'))
});

