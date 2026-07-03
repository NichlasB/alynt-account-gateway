import * as esbuild from 'esbuild';

const isWatch = process.argv.includes('--watch');

const buildOptions = {
	entryPoints: [
		'assets/src/admin/index.js',
		'assets/src/frontend/index.js',
	],
	bundle: true,
	minify: !isWatch,
	sourcemap: isWatch,
	outdir: 'assets/dist',
	target: ['es2020'],
	loader: {
		'.css': 'css',
	},
};

if (isWatch) {
	const context = await esbuild.context(buildOptions);
	await context.watch();
	console.log('Watching for changes...');
} else {
	await esbuild.build(buildOptions);
	console.log('Build complete.');
}
