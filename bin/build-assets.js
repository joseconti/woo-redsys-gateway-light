#!/usr/bin/env node
/**
 * Minifies the hand-written stylesheets: every `assets/css/<name>.css`
 * gets a `<name>.min.css` beside it.
 *
 * The unminified file is the source and the only one edited; the minified
 * file is build output and is never written by hand. The Blocks script has
 * its own pair, written by webpack (`webpack.config.js`).
 *
 *   node bin/build-assets.js           writes the minified files
 *   node bin/build-assets.js --check   writes nothing; exits 1 when a
 *                                      minified file is missing or stale
 */
const fs = require( 'fs' );
const path = require( 'path' );
const postcss = require( 'postcss' );
const cssnano = require( 'cssnano' );

const cssDir = path.join( __dirname, '..', 'assets', 'css' );
const check = process.argv.includes( '--check' );

async function main() {
	const sources = fs
		.readdirSync( cssDir )
		.filter( ( file ) => file.endsWith( '.css' ) && ! file.endsWith( '.min.css' ) )
		.sort();
	const stale = [];

	for ( const file of sources ) {
		const from = path.join( cssDir, file );
		const to = from.replace( /\.css$/, '.min.css' );
		const result = await postcss( [ cssnano( { preset: 'default' } ) ] ).process( fs.readFileSync( from, 'utf8' ), { from, to, map: false } );
		const minified = result.css + '\n';
		const current = fs.existsSync( to ) ? fs.readFileSync( to, 'utf8' ) : null;

		if ( check ) {
			if ( current !== minified ) {
				stale.push( path.relative( process.cwd(), to ) );
			}
			continue;
		}
		if ( current !== minified ) {
			fs.writeFileSync( to, minified );
		}
		console.log( `${ path.relative( process.cwd(), to ) } — ${ Buffer.byteLength( minified ) } bytes` );
	}

	const orphans = fs
		.readdirSync( cssDir )
		.filter( ( file ) => file.endsWith( '.min.css' ) && ! sources.includes( file.replace( /\.min\.css$/, '.css' ) ) );
	for ( const file of orphans ) {
		stale.push( `${ path.join( 'assets', 'css', file ) } (no source)` );
	}

	if ( stale.length ) {
		console.error( `Out of sync with its source: ${ stale.join( ', ' ) }. Run: npm run build:css` );
		process.exit( 1 );
	}
	if ( check ) {
		console.log( `${ sources.length } stylesheet(s) in sync with their minified pair` );
	}
}

main().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
