import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    safelist: [
        'my-6',
        'mb-4',
        'mb-6',
        'mt-6',
        'mt-8',
        'pt-6',
        'gap-4',
        'gap-6',
        'space-y-4',
        'space-y-6',
        {
            pattern: /^(m|p)(t|b|l|r|x|y)?-(0|1|2|3|4|5|6|8|10|12)$/,
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
