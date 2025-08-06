import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';
import { glob } from 'glob';

const moduleCssFiles = glob.sync('resources/styles/modules/**/*.scss');
const moduleJsFiles = glob.sync('resources/js/modules/**/*.js');

export default defineConfig({
  base: '/wp-content/themes/sage/public/build/',
  server: {
    cors: true,
    headers: {
      'Access-Control-Allow-Origin': '*',
    },

  },
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/styles/app.scss',
        // 'resources/styles/editor-root-only.scss',
        // 'resources/styles/editor-app.scss',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
        'resources/js/single.js',
        'resources/js/filter.js',
        ...moduleCssFiles,
        ...moduleJsFiles
      ],
      refresh: true,
    }),

    wordpressPlugin(),

    // Generate the theme.json file in the public/build/assets directory
    // based on the Tailwind config and the theme.json file from base theme folder
    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
    }),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js/',
      '@styles': '/resources/styles',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
  build: {
    rollupOptions: {
      output: {
        // Manually create chunks to avoid duplication.
        manualChunks(id) {
          if (id.includes('node_modules/')) {
            // Extract the package name from the path and use it as the chunk name
            const directories = id.split('node_modules/')[1].split('/');
            const name = directories[0].startsWith('@') ? `${directories[0]}/${directories[1]}` : directories[0];
            return `vendor/${name}`;
          }
          // Group internal shared JS modules from a common folder into a 'shared' chunk.
          if (id.includes('resources/js/shared/')) {
            return 'shared';
          }
        },
      },
    },
  },
});
