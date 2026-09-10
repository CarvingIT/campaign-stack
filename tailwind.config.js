import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': ['0.6875rem', { lineHeight: '1rem' }],   /* 11px */
                '3xs': ['0.625rem', { lineHeight: '0.875rem' }], /* 10px */
                '4xs': ['0.5625rem', { lineHeight: '0.75rem' }], /* 9px */
            },
        },
    },

    plugins: [forms],
};
