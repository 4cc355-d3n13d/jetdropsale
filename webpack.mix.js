let mix = require('laravel-mix');
let path = require('path');

mix.js('resources/assets/js/main.js', 'public/js')
    .js('resources/assets/ssr_js/ssr.js', 'public/js')
    .sass('resources/assets/styles/styles.scss', 'public/css')
    .webpackConfig({
        resolve: {
            alias: {
                app: path.resolve(__dirname, 'resources/assets/'),
            }
        }

    })
    .copyDirectory('resources/assets/img', 'public/img')

if (mix.inProduction()) {
    mix.version();
}


