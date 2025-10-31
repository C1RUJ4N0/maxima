// Archivo: tailwind.config.js

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        // COLORES PERSONALIZADOS PARA EL TEMA AZULADO DE MAXIMA
        'maxima-dark-blue': '#0A1C4F',
        'maxima-hover-blue': '#1B3376',
        'maxima-light-blue-100': '#E0F2FF',
        'maxima-wave-blue-a': '#6a8dbb',
        'maxima-wave-blue-b': '#3f70a1',
        'maxima-wave-blue-c': '#1b4b7c',
      },
    },
  },
  plugins: [],
}