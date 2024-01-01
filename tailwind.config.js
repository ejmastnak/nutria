const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            gridTemplateColumns: {
                '16': 'repeat(16, minmax(0, 1fr))',
            },
            gridColumn: {
                'span-16': 'span 16 / span 16',
            },
        },
        screens: {
            'xs': '475px',
            ...defaultTheme.screens,
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
