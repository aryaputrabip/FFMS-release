const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/theme/default/app.scss', 'public/css')
    .sass('resources/sass/theme/default/bootstrap.scss', 'public/css')
    .sass('resources/sass/theme/default/datatables.scss', 'public/css')
    .sass('resources/sass/theme/default/fonts.scss', 'public/css')
    .js('resources/js/custom-script.js', 'public/js');
