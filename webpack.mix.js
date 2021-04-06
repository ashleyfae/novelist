const mix = require( 'laravel-mix' );

mix.options( {
	processCssUrls: false
} )
	.sass( 'assets/css/novelist-admin.scss', 'assets/css' )
	.sass( 'templates/novelist-front-end.scss', 'templates' )
	.minify( [
		'assets/js/admin-scripts.js',
		'assets/js/media-upload.js',
		'assets/js/widget-settings.js'
	] )
