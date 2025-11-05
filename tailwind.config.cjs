module.exports = {
	purge: {
		content: [
			'./resources/**/*.blade.php',
			'./resources/**/*.js',
			'./resources/**/*.vue',
			'./resources/**/*.ts',
			'./resources/**/*.tsx',
		],
		options: {
			safelist: [
				'grid-cols-1',
				'grid-cols-2',
				'grid-cols-3',
				'grid-cols-4',
				'grid-cols-5',
				'grid-cols-6',
				'grid-cols-7',
				'grid-cols-8',
				'grid-cols-9',
				'grid-cols-10',
				'grid-cols-11',
				'grid-cols-12',
				'md:grid-cols-1',
				'md:grid-cols-2',
				'md:grid-cols-3',
				'md:grid-cols-4',
			],
		},
	},
	darkMode: false,
	theme: {
		extend: {},
	},
	variants: {
		extend: {},
	},
	plugins: [],
};