/** @type {import('tailwindcss').Config} */
import withMT from "@material-tailwind/html/utils/withMT";

const withMT = require("@material-tailwind/html/utils/withMT");

module.exports = withMT({
  content: ["./assets/**/*.js", "./assets/**/*.css", "./templates/**/*.twig"],
  
  theme: {
    extend: {
      colors: {
        rouen: {
          blue: '#0047AB',      
          gold: '#FFD700',      
          light: '#E8F4F8',     
          dark: '#1E3A5F',      
        },
        
        primary: {
          50: '#EFF6FF',
          100: '#DBEAFE',
          200: '#BFDBFE',
          300: '#93C5FD',
          400: '#60A5FA',
          500: '#3B82F6',
          600: '#2563EB',
          700: '#1D4ED8',
          800: '#1E40AF',
          900: '#1E3A8A',
        },
      },
      
      spacing: {
        '128': '32rem',
        '144': '36rem',
      },
      
      borderRadius: {
        '4xl': '2rem',
      },
      
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.5s ease-out',
        'bounce-slow': 'bounce 3s infinite',
      },
      
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
      },
      
      screens: {
        'xs': '475px',
        // sm: '640px' (par défaut)
        // md: '768px' (par défaut)
        // lg: '1024px' (par défaut)
        // xl: '1280px' (par défaut)
        '2xl': '1536px',
        '3xl': '1920px',
      },
      
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
        serif: ['Georgia', 'Cambria', 'serif'],
        mono: ['Monaco', 'Courier New', 'monospace'],
      },
      
      fontSize: {
        'xxs': '0.625rem',
      },
      
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'strong': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
      },
    },
  },
  
  plugins: [
    // Plugin pour les formulaires stylisés
    // require('@tailwindcss/forms'),
    
    // Plugin pour la typographie
    // require('@tailwindcss/typography'),
    
    // Plugin pour les aspect ratios
    // require('@tailwindcss/aspect-ratio'),
    
    // Plugin pour le line-clamp
    // require('@tailwindcss/line-clamp'),
  ],
   
});
