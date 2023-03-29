const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve('resources/js'),
                ziggy: path.resolve('vendor/tightenco/ziggy/dist/js/route.js'),
                'inertia': path.resolve('node_modules/@inertiajs/inertia'),
                '@inertiajs/inertia-vue3': path.resolve('node_modules/@inertiajs/inertia-vue3'),
                '@inertiajs/progress': path.resolve('node_modules/@inertiajs/progress'),
            },
        },
    })
    .options({
        hmrOptions: {
            host: 'localhost',
            port: '8080'
        },
        processCssUrls: false,
    })
    .sourceMaps();
