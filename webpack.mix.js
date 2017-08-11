const { mix } = require('laravel-mix');
const webpack = require('webpack');
const { forEach } = require('lodash');
const { getColorNames } = require('./resources/assets/js/bootswatch');
const pug = require('pug');
const fs = require('fs');
const pugOptions = {
	pretty: !mix.config.inProduction
};

/**
 * Render pug files in a given directory to a given directory
 *
 * @param readPath
 * @param writePath
 */
function renderPugFilesInDir(readPath, writePath) {
	checkForAndCreateDirectory(writePath);

	fs.readdir(readPath, (err, files) => {
		files.forEach(filename => {
			let fileStat = fs.lstatSync(readPath + filename);
			if (fileStat.isDirectory()) {
				checkForAndCreateDirectory(writePath + filename);
				renderPugFilesInDir(readPath + filename + '/', writePath + filename + '/');
			} else if (fileStat.isFile(filename)) {
				fs.writeFile(writePath + filename.replace('.pug', '.blade.php'), pug.renderFile(readPath + filename, pugOptions));
			}
		});
	});

	return this;
}

/**
 * Check for and create a directory if it doesn't exist
 *
 * @param writePath
 */
function checkForAndCreateDirectory(writePath) {
	if (!fs.existsSync(writePath + '/')) {
		fs.mkdirSync(writePath + '/');
	}
}

mix.pug = renderPugFilesInDir;

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

mix.
	webpackConfig({
		module: {
			rules: [
				{
					test: /\.pug$/,
					loaders: ['raw-loader', 'pug-html-loader'],
				}
			]
		},
		plugins: [
			new webpack.ProvidePlugin({
				'$': 'jquery',
				'jQuery': 'jquery',
				'jquery': 'jquery',
				'window.$': 'jquery',
				'window.jQuery': 'jquery',
				'window.jquery': 'jquery'
			})
		]
	})
	.js('resources/assets/js/app.js', 'public/js')
	.extract([
		'jquery',
		'jquery-ui',
		'angular',
		'angular-route',
		'angular-resource',
		'angular-sanitize',
		'angular-loading-bar',
		'angular-animate',
		'angular-ui-bootstrap',
		'angular-ui-sortable',
		'angular-ui-mask',
		'ng-file-upload'
	])
	.sass('resources/assets/sass/app.scss', 'public/css')
	.pug('./resources/assets/pug/layouts/pages/', './resources/views/')
	.pug('./resources/assets/pug/emails/', './resources/views/emails/')
	.copy('node_modules/font-awesome/fonts', 'public/fonts')
	.copy('node_modules/bootswatch/bower_components/bootstrap/fonts', 'public/css/fonts')
;

forEach(getColorNames(), bootswatch => {
	mix
		.copy('node_modules/bootswatch/' + bootswatch + '/bootstrap.min.css', 'public/css/' + bootswatch)
		.copy('node_modules/bootswatch/' + bootswatch + '/thumbnail.png', 'public/images/' + bootswatch)
	;
});

if (mix.config.inProduction) {
	mix.version();
}
else {
	mix.sourceMaps();
}