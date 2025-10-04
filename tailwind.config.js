/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php", // <-- Esta línea es crucial
    "./resources/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}