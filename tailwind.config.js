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
            colors: {
                "primary": "#6258ca",
                "background-light": "#f6f6f8",
                "background-dark": "#15141e",
                "success": "#09ad95",
                "warning": "#f5a623",
                "info": "#17a2b8"
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                "DEFAULT": "0.5rem",
                "lg": "1rem",
                "xl": "1.5rem",
                "full": "9999px"
            },
        },
    },

    plugins: [forms],
};
