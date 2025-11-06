module.exports = {
	// OPTION 1 (Recommended): Keep purging enabled with proper content detection
	// This keeps CSS small (~50KB) while detecting all used classes
	// purge: {
	// 	content: [
	// 		'./resources/**/*.blade.php',
	// 		'./resources/**/*.js',
	// 		'./resources/**/*.vue',
	// 		'./resources/**/*.ts',
	// 		'./resources/**/*.tsx',
	// 	],
	// 	// Safelist for dynamically generated classes that purging might miss
	// 	options: {
	// 		safelist: [
	// 			{
	// 				// Pattern matching for gap utilities
	// 				pattern: /gap-(1|2|3|4|5|6|8|10|12|16|20|24)/,
	// 			},
	// 			{
	// 				// Pattern matching for grid columns
	// 				pattern: /grid-cols-(1|2|3|4|5|6|7|8|9|10|11|12)/,
	// 			},
	// 			{
	// 				// Pattern matching for responsive grid columns
	// 				pattern: /md:grid-cols-(1|2|3|4|5|6|7|8|9|10|11|12)/,
	// 			},
	// 		],
	// 	},
	// },
	
	// OPTION 2 (Not recommended): Disable purging entirely
	// Uncomment this and remove 'purge' above to include ALL Tailwind classes
	// This will make your CSS file ~10MB+ instead of ~50KB
	purge: false,
	
	darkMode: false,
	theme: {
		extend: {},
	},
	variants: {
		extend: {},
	},
	plugins: [],
};