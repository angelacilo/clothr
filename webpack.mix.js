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
    .react()
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/js/auth.js', 'public/js')
    .js('resources/js/home.js', 'public/js')
    .copy('resources/css/auth.css', 'public/css/auth.css')
    .copy('resources/css/home.css', 'public/css/home.css');
