/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/**/*.blade.php",
        "./resources/**/**/*.js",
        "./app/View/Components/**/**/*.php",
        "./app/Livewire/**/**/*.php",
    ],
    theme: {
        extend: {},
    },

    plugins: [
        require('@tailwindcss/forms'),
    ]
}
