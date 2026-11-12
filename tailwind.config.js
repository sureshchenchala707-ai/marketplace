
/** @type {import('tailwindcss').Config} */
export default {
    prefix: 'tw-',

    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],

    corePlugins: {
        preflight: false,
    },

    theme: {
        extend: {
            colors: {
                ess: {
                    primary: '#0645e8',
                    dark: '#0034bd',
                    accent: '#176bff',
                    light: '#dceaff',
                    surface: '#f6f8fc',
                    text: '#171b2c',
                    muted: '#7b8499',
                },
            },

            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },

            boxShadow: {
                card: '0 4px 20px rgba(20, 35, 80, 0.06)',
                elevated: '0 12px 35px rgba(20, 35, 80, 0.10)',
            },
        },
    },

    plugins: [],
};
