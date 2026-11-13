const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');

const wcDepMap = {
	'@woocommerce/blocks-registry': ['wc', 'wcBlocksRegistry'],
	'@woocommerce/settings'       : ['wc', 'wcSettings']
};

const wcHandleMap = {
	'@woocommerce/blocks-registry': 'wc-blocks-registry',
	'@woocommerce/settings'       : 'wc-settings'
};

const requestToExternal = (request) => {
	if (wcDepMap[request]) {
		return wcDepMap[request];
	}
};

const requestToHandle = (request) => {
	if (wcHandleMap[request]) {
		return wcHandleMap[request];
	}
};

// Export configuration.
module.exports = {
	...defaultConfig,
	// One source, two outputs: `blocks.js` is the readable build WordPress
	// serves under SCRIPT_DEBUG, `blocks.min.js` is what production loads.
	entry: {
		'frontend/blocks': '/resources/js/frontend/index.js',
		'frontend/blocks.min': '/resources/js/frontend/index.js',
	},
	optimization: {
		...defaultConfig.optimization,
		minimizer: [
			// The default minimizer's own settings, applied to the `.min` output only.
			new TerserPlugin({
				test: /\.min\.js$/,
				parallel: true,
				extractComments: false,
				terserOptions: defaultConfig.optimization.minimizer[0].options.minimizer.options,
			}),
		],
	},
	output: {
		path: path.resolve( __dirname, 'assets/js' ),
		filename: '[name].js',
	},
	plugins: [
		...defaultConfig.plugins.filter(
			(plugin) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin({
			requestToExternal,
			requestToHandle
		})
	]
};
