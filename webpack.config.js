const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('global', './assets/javascript/global.js')

    // -- DONATION -- //
    .addEntry('donation_common', './assets/javascript/Donation/common.js')
    .addEntry('donation_edit', './assets/javascript/Donation/edit.js')

    // -- HOME -- //
    .addEntry('home_index', './assets/javascript/Home/index.js')

    // -- MEMBERSHIP -- //
    .addEntry('membership_common', './assets/javascript/Membership/common.js')
    .addEntry('membership_new', './assets/javascript/Membership/new.js')
    .addEntry('membership_edit', './assets/javascript/Membership/edit.js')

    // -- PEOPLE -- //
    .addEntry('people_show', './assets/javascript/People/show.js')
    .addEntry('people_donations_list', './assets/javascript/People/partials/people_donations_list.js')
    .addEntry('people_memberships_list', './assets/javascript/People/partials/people_memberships_list.js')
    .addEntry('people_payments_list', './assets/javascript/People/partials/people_payments_list.js')
    .addEntry('people_generate_from_year', './assets/javascript/People/generate-from-year.js')
    .addEntry('people_phone', './assets/javascript/People/phone.js')

    // -- RECEIPT -- //
    .addEntry('receipt_list', './assets/javascript/Receipt/list.js')
    .addEntry('receipt_generate_from_year', './assets/javascript/Receipt/generate-from-year.js')

    // -- TAG -- //
    .addEntry('tag_index', './assets/javascript/Tag/index.js')

    // -- USER -- //
    .addEntry('user_new', './assets/javascript/User/new.js')
    .addEntry('user_edit', './assets/javascript/User/edit.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
