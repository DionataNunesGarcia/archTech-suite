const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.twig",
    "./**/*.twig",
    "./src/js/**/*.js",
    "./src/scss/**/*.scss",
  ],

  safelist: [
    'md:justify-end', 'ml-auto', 'gap-8', 'flex', 'hidden', 'lg:flex', 'lg:hidden',
    'backdrop-blur-sm', 'text-emerald-700', 'hover:text-neutral-600',
    'transition-colors', 'px-6', 'py-2.5', 'bg-emerald-800', 'hover:bg-emerald-900',
    'text-white', 'text-sm', 'tracking-wide', 'relative', 'group',
    'has-dropdown', 'dropdown', 'absolute', 'z-50', 'rounded-md'
  ],

  theme: {
    extend: {

      /* ========================
         COLORS (via CSS vars)
      ======================== */
      colors: {
        background: 'var(--background)',
        foreground: 'var(--foreground)',

        primary: {
          DEFAULT: 'var(--primary)',
          foreground: 'var(--primary-foreground)',
        },
        secondary: {
          DEFAULT: 'var(--secondary)',
          foreground: 'var(--secondary-foreground)',
        },
        muted: {
          DEFAULT: 'var(--muted)',
          foreground: 'var(--muted-foreground)',
        },
        accent: {
          DEFAULT: 'var(--accent)',
          foreground: 'var(--accent-foreground)',
        },
        destructive: {
          DEFAULT: 'var(--destructive)',
          foreground: 'var(--destructive-foreground)',
        },

        border: 'var(--border)',
        input: 'var(--input)',
        ring: 'var(--ring)',

        chart: {
          1: 'var(--chart-1)',
          2: 'var(--chart-2)',
          3: 'var(--chart-3)',
          4: 'var(--chart-4)',
          5: 'var(--chart-5)',
        },

        sidebar: {
          DEFAULT: 'var(--sidebar)',
          foreground: 'var(--sidebar-foreground)',
          primary: 'var(--sidebar-primary)',
          'primary-foreground': 'var(--sidebar-primary-foreground)',
          accent: 'var(--sidebar-accent)',
          'accent-foreground': 'var(--sidebar-accent-foreground)',
          border: 'var(--sidebar-border)',
          ring: 'var(--sidebar-ring)',
        },
      },

      /* ========================
         TYPOGRAPHY
      ======================== */
      fontFamily: {
        sans: ['var(--font-family)'],
      },

      fontSize: {
        xs: ['var(--text-xs)', { lineHeight: 'var(--text-xs--line-height)' }],
        sm: ['var(--text-sm)', { lineHeight: 'var(--text-sm--line-height)' }],
        base: ['var(--text-base)', { lineHeight: 'var(--text-base--line-height)' }],
        lg: ['var(--text-lg)', { lineHeight: 'var(--text-lg--line-height)' }],
        xl: ['var(--text-xl)', { lineHeight: 'var(--text-xl--line-height)' }],
        '2xl': ['var(--text-2xl)', { lineHeight: 'var(--text-2xl--line-height)' }],
        '3xl': ['var(--text-3xl)', { lineHeight: 'var(--text-3xl--line-height)' }],
        '4xl': ['var(--text-4xl)', { lineHeight: 'var(--text-4xl--line-height)' }],
        '5xl': ['var(--text-5xl)', { lineHeight: 'var(--text-5xl--line-height)' }],
        '7xl': ['var(--text-7xl)', { lineHeight: 'var(--text-7xl--line-height)' }],

        // Clamp responsivo
        h1: 'clamp(2rem, 5vw, 3rem)',
        h2: 'clamp(1.75rem, 4vw, 2.5rem)',
        h3: 'clamp(1.375rem, 3vw, 2rem)',
      },

      fontWeight: {
        normal: 'var(--font-weight-normal)',
        medium: 'var(--font-weight-medium)',
        semibold: 'var(--font-weight-semibold)',
        bold: '700',
      },

      letterSpacing: {
        tight: 'var(--tracking-tight)',
        wide: 'var(--tracking-wide)',
        widest: 'var(--tracking-widest)',
      },

      lineHeight: {
        tight: 'var(--leading-tight)',
        relaxed: 'var(--leading-relaxed)',
      },

      /* ========================
         SPACING
      ======================== */
      spacing: {
        1: 'var(--space-1)',
        4: 'var(--space-4)',
        6: 'var(--space-6)',
        16: 'var(--space-16)',
      },

      /* ========================
         RADIUS / SHADOW
      ======================== */
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },

      boxShadow: {
        sm: 'var(--shadow-sm)',
        md: 'var(--shadow-md)',
      },

      /* ========================
         BLUR / ANIMATION
      ======================== */
      blur: {
        sm: 'var(--blur-sm)',
      },

      transitionTimingFunction: {
        DEFAULT: 'var(--default-transition-timing-function)',
      },

      transitionDuration: {
        DEFAULT: 'var(--default-transition-duration)',
      },

      /* ========================
         CONTAINERS
      ======================== */
      maxWidth: {
        '200': '200px',
        '250': '250px',
        '300': '300px',
        '350': '350px',
        '400': '400px',
        '450': '450px',
        '500': '500px',
        'container-sm': 'var(--container-sm)',
        'container-lg': 'var(--container-lg)',
        'container-xl': 'var(--container-xl)',
      },

    },
  },

  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),

    plugin(function ({ addUtilities, theme }) {
      const colors = theme('colors');
      const newUtilities = {};

      function extractColors(obj, prefix = '') {
        Object.entries(obj).forEach(([key, value]) => {
          const name = prefix ? `${prefix}-${key}` : key;

          if (typeof value === 'string') {
            newUtilities[`.border-${name}`] = {
              borderColor: value,
            };
          }

          if (typeof value === 'object') {
            extractColors(value, name);
          }
        });
      }

      extractColors(colors);

      addUtilities(newUtilities);
    }),
  ],
};