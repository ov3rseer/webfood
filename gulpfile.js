// Include gulp
let gulp = require('gulp');
let sass = require('gulp-sass');
let rename = require('gulp-rename');
// Include Our Plugins
let jshint = require('gulp-jshint');
let concat = require('gulp-concat');
let terser = require('gulp-terser');
// var sourcemaps = require('gulp-sourcemaps');

let del = require('del'); // Подключаем библиотеку для удаления файлов и папок
let autoprefixer = require('gulp-autoprefixer');// Подключаем библиотеку для автоматического добавления префиксов
let combineMq = require('gulp-combine-mq');
let multiDest = require('gulp-multi-dest');

const args = require('yargs').argv;

//var streamCombiner = require('stream-combiner');
// stream-combiner for array in dest
// https://github.com/gulpjs/vinyl-fs/issues/67
// https://www.npmjs.com/package/stream-combiner

let frontendWebDir = 'frontend';
let backendWebDir = 'backend';
let terminalWebDir = 'terminal';

let build = {
    'dist': {
        'build': true,
        'path': '/web/'
    }
};

function foldersNamesTwoFullPaths(webDir, folder) { // принимает название папки (строку или массив з нескольких и на основе build[] возвращает массив полных путей к папкам

    let fullPaths = [];
    let folderNames = [];

    if (!folder) {
        folderNames = ['css', 'js', 'img'];
    } else if (Array.isArray(folder)) {
        folderNames = folder;
    } else if (typeof (folder) == 'string' && folder !== '') {
        folderNames = [folder];
    }

    for (let key in build) {
        if (build[key]['build']) {
            for (let i = folderNames.length - 1; i >= 0; i--) {
                let path = webDir + build[key]['path'] + '/' + folderNames[i];
                fullPaths.push(path);
            }
        }
    }

    return fullPaths; // массив с путями
}

gulp.task('clean', function () { // Удаляем папку dist/assets перед сборкой
    let actions = {
        'clean': ['css', 'js', 'img'],
        'clean-img': ['img'],
        'clean-css': ['css'],
        'clean-js': ['js'],
    };
    let webDir = args.path;
    if (webDir) {
        return del(foldersNamesTwoFullPaths(webDir, 'assets'), {force: true});
    } else {
        throw 'ошибк';
    }
});

// Lint Task
gulp.task('lint', function () {
    return gulp.src('src/js/**/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function () {
    console.log(foldersNamesTwoFullPaths('css'));
    return gulp.src('src/scss/**/*.scss')
    // .pipe(sourcemaps.init()) //
        .pipe(sass())
        // .pipe(sourcemaps.write()) //
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'safari 5', 'ie7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'], {cascade: true})) // Создаем префиксы
        .pipe(multiDest(foldersNamesTwoFullPaths('css')));
    // .pipe(gulp.dest('dist/assets/css'));
});


// Concatenate & Minify JS
gulp.task('scripts', function () {
    return gulp.src([
        'src/js/**/*.js'
    ])
        .pipe(concat('all.js'))
        // .pipe(gulp.dest('dist/assets/js'))
        .pipe(rename('all.min.js'))
        .pipe(terser())
        .pipe(multiDest(foldersNamesTwoFullPaths('js')));
});

// Compile Our Sass
gulp.task('fontawesome', function () {
    console.log(foldersNamesTwoFullPaths('webfonts'));
    return gulp.src('node_modules/@fortawesome/fontawesome-free/webfonts/*') // Переносим шрифты в продакшен
        .pipe(multiDest(foldersNamesTwoFullPaths('webfonts')));
    // .pipe(gulp.dest('dist/assets/webfonts'));
});

// Переносим картинки в продакшен
gulp.task('img', function () {

    return gulp.src([
        'src/img/**/*',
        '!src/img/**/__**',
        '!src/img/**/__**/**/*',
    ])
        .pipe(multiDest(foldersNamesTwoFullPaths('img')));
});

// Watch Files For Changes
gulp.task('watch', function () {
    // gulp.watch('src/js/*.js', ['lint', 'scripts']);
    // gulp.watch('src/scss/*.scss', ['scss']);
    gulp.watch('src/scss/**/*.scss', gulp.series('clean-css', 'sass'));
    gulp.watch('src/js/**/*.js', gulp.series('lint', 'clean-js', 'scripts'));
    gulp.watch([
        'src/img/**/*',
        '!src/img/**/__**',
        '!src/img/**/__**/**/*',
    ], gulp.series('clean-img', 'img'));
});

gulp.task('build', gulp.series(
    'clean',
    gulp.parallel(
        // 'fontawesome',
        'img',
        'sass',
        'scripts'
    )
));

// Default Task
gulp.task('default', gulp.series(
    'lint',
    'build',
    'watch'
));

//
// var minifyCss = require('gulp-clean-css');//минификация css
// var notify = require('gulp-notify');
// var clean = require('gulp-clean');
//
// //==============================================================================
// //**************************  FrontEnd  ****************************************
// //==============================================================================
// //ПУТИ
// var F_webDir = 'frontend/web/';
//
// var F_sourseDir = F_webDir + 'source/',
//     F_sassDir = F_sourseDir + 'sass/',
//     F_sassMainFile = F_sassDir + 'main.scss';
//
// var F_destCssDir = F_webDir + 'styles/css/',
//     F_destCssMinDir = F_webDir + 'styles/css-min/';
//
// var sassOptions = {
//     outputStyle: 'nested',
//     precison: 3,
//     errLogToConsole: true,
// };
//
// //------------------------------------------------------------------------------
// //                  компиляция sass
// //------------------------------------------------------------------------------
// gulp.task('front:compileSass', ['front:clean'], function () {
//     return gulp
//         .src([F_sassMainFile])
//         .pipe(sass(sassOptions).on('error', sass.logError))
//         .pipe(autoprefixer({
//             browsers: ['last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'],
//             cascade: false
//         }))
//         .pipe(combineMq({
//             beautify: false
//         }))
//         .pipe(gulp.dest(F_destCssDir))
//         .pipe(rename({suffix: '.min'}))
//         .pipe(minifyCss({processImport: false}))
//         .pipe(gulp.dest(F_destCssMinDir))
//         .pipe(notify("front:compileSass was compiled!"));
// });
//
// // Очистка перед новой записью
// gulp.task('front:clean', function () {
//     return gulp.src([F_destCssDir, F_destCssMinDir], {read: false})
//         .pipe(clean())
//         .pipe(notify("front:clean was compiled!"));
// });
//
// //------------------------------------------------------------------------------
// //Наблюдение за файлами. (запуск из консоли - gulp watch)
// //------------------------------------------------------------------------------
//
// gulp.task('watch', function () {
//
//     gulp.watch(F_sassDir + '**/*.scss', [
//         'front:clean',
//         'front:compileSass',
// //        'front:combineMq'
//     ]);
//
// });

gulp.task('hello', function () {
    console.log('**/*.scss');
});