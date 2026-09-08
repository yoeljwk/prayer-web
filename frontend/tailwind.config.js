/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        serif: ['Newsreader', 'Cormorant Garamond', 'Georgia', 'serif'],
        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
        'serif-custom': ['Newsreader', 'Cormorant Garamond', 'Georgia', 'serif'],
        'sans-custom': ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
      },
      colors: {
        mono: {
          bg: '#FFFFFF',
          'bg-subtle': '#FAFAFA',
          black: '#09090B',
          'black-hover': '#27272A',
          border: '#E4E4E7',
          muted: '#71717A',
          subtle: '#A1A1AA',
        }
      }
    },
  },
  plugins: [],
}
