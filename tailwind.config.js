import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Inter', ...defaultTheme.fontFamily.sans],
                gaming: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                hyde: {
                    dark: '#070A12',
                    navy: '#0B1120',
                    card: '#10192D',
                    cardHover: '#16233F',
                    border: '#1E293B',
                    purple: '#9333EA',
                    magenta: '#C026D3',
                    neonPink: '#E879F9',
                    cyan: '#38BDF8',
                    ice: '#E0F2FE',
                },
            },
            boxShadow: {
                'neon-purple': '0 0 25px -5px rgba(168, 85, 247, 0.4)',
                'neon-magenta': '0 0 25px -5px rgba(217, 70, 239, 0.4)',
                'neon-cyan': '0 0 25px -5px rgba(56, 189, 248, 0.3)',
            },
        },
    },

    plugins: [forms],
};
