const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .react()
   .js('resources/js/auth.js', 'public/js')
   .js('resources/js/home.js', 'public/js')
   .js('resources/js/admin.js', 'public/js')  // <--- Add this line
   .sass('resources/sass/app.scss', 'public/css')
   .copy('resources/css/auth.css', 'public/css/auth.css')
   .copy('resources/css/home.css', 'public/css/home.css');