let mix = require('laravel-mix');
let glob = require('glob');

mix.options({
    processCssUrls: false,
    clearConsole: true,
    terser: {
        extractComments: false,
    }
});

// Run all webpack.mix.js in app
glob.sync('./platform/**/**/webpack.mix.js').forEach(item => require(item));

// Run only for a package
// require('./platform/packages/[package]/webpack.mix.js');

// Run only for a plugin
// require('./platform/plugins/[plugin]/webpack.mix.js');

// Run only for themes
// glob.sync('./platform/themes/**/webpack.mix.js').forEach(item => require(item));

