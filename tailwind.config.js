import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                pp: {
                    50: '#f5f3ff',
                    100: '#ebe7ff',
                    200: '#d9d2ff',
                    500: '#5140c8',
                    600: '#4634b7',
                    700: '#38299b',
                    800: '#2c1e7a',
                    900: '#20165f',
                },
            },
            boxShadow: {
                soft: '0 10px 35px -5px rgba(42, 31, 120, .08), 0 4px 12px -2px rgba(0,0,0,0.03)',
                card: '0 4px 20px rgba(31, 25, 79, .06)',
                hover: '0 12px 30px rgba(81, 64, 200, .12)',
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
