var elixir      = require('laravel-elixir');
var BrowserSync = require('laravel-elixir-browser-sync-simple');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

var paths = {
    assets: './resources/assets',
    bower: './resources/assets/bower_components',
}

elixir(function(mix) {

    mix.sass('app.scss',
        'resources/assets/css/app.css', {
            includePaths: [
                paths.bower + '/foundation-sites/scss',
                paths.bower + '/angular-material'
            ]
        }
    );
    
    mix.sass('public.scss',
        'public/css/public.css', {
            includePaths: [
                paths.bower + '/foundation-sites/scss',
                paths.bower + '/angular-material'
            ]
        });

    // We have converted the date time directive styles to SCSS for editing.
    mix.styles([
        paths.bower + '/angular-material-data-table/dist/md-data-table.css',
        paths.bower + '/angular-loading-bar/build/loading-bar.css',
        paths.bower + '/textAngular/dist/textAngular.css',
        'app.css'
    ], 'public/css/all.css');

    mix.scripts([
        paths.bower + '/lodash/lodash.js',
        paths.bower + '/sprintf/src/sprintf.js',
        paths.bower + '/papaparse/papaparse.js',
        paths.bower + '/moment/moment.js',
        paths.bower + '/moment-timezone/moment-timezone.js',
    ], 'public/build/js/vendor/vendor.js');

    // We need to use modified versions of sc-date-time and textAngular
    // in order to tie them into Material UI, and fix some bugs.
    mix.scripts([
        paths.bower + '/angular/angular.js',
        paths.bower + '/angular-animate/angular-animate.js',
        paths.bower + '/angular-aria/angular-aria.js',
        paths.bower + '/angular-messages/angular-messages.js',
        paths.bower + '/angular-material/angular-material.js',
        paths.bower + '/angular-ui-router/release/angular-ui-router.js',
        paths.bower + '/ui-router-extras/release/ct-ui-router-extras.js',
        paths.bower + '/angular-ui-router-title/angular-ui-router-title.js',
        paths.bower + '/restangular/dist/restangular.js',
        paths.bower + '/angular-material-data-table/dist/md-data-table.js',
        paths.assets + '/js/vendor/sc-date-time.js',
        paths.bower + '/textAngular/dist/textAngular-rangy.min.js',
        paths.bower + '/textAngular/dist/textAngular-sanitize.js',
        paths.assets + '/js/vendor/textAngular.js',
        paths.bower + '/angular-loading-bar/build/loading-bar.js',
        paths.bower + '/sprintf/src/angular-sprintf.js',
        paths.bower + '/angular-moment/angular-moment.js',
    ], 'public/build/js/vendor/angular.js');
    
    mix.scripts([
        'app/app.js',
        'app/configuration/textangular.config.js',
        'app/configuration/textangular.decorators.js',
        'app/providers/authentication.provider.js',
        'app/controllers/app.controller.js',
        'app/controllers/admin.controller.js',
        'app/controllers/user.controller.js',
        'app/controllers/address.controller.js',
        'app/controllers/template.controller.js',
        'app/controllers/emailer.controller.js',
        'app/controllers/help.controller.js',
        'app/directives/preview.directive.js',
        'app/directives/input-match.directive.js',
        'app/directives/file-change.directive.js',
        'app/directives/scroll-to.directive.js',
        'app/filters/sanitize.filter.js',
        'app/filters/titlecase.filter.js',
        'app/filters/zeropad.filter.js',
    ], 'public/build/js/app.js');

    mix.copy(
        'resources/assets/bower_components/base64/*.js',
        'public/build/js/vendor'
    );
    
    mix.browserSync({
        proxy: 'lumen.app',
        notify: false,
        reloadDebounce: 300,
        injectChanges: true,
        ui: {
            port: 3333
        },
        files: [
            'public/build/**/*',
            'public/css/all.css',
            'resources/views/**/*'
        ]
    });

});
