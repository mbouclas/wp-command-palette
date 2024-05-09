import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import typescript from '@rollup/plugin-typescript';
import sveltePreprocess from 'svelte-preprocess'


export default defineConfig({
  plugins: [svelte({
    compilerOptions: {
      customElement: true, // Enables custom element mode
    },
    preprocess: sveltePreprocess(), // Enables preprocessing
  }),
    typescript({ sourceMap: !process.env.production }) // TypeScript support
  ],
  build: {
    sourcemap: true,
    lib: {
      entry: 'src/lib/index.ts', // Entry file for your library
      name: 'WpAdminCommandPalette',
      // the proper extensions will be added
      fileName: (format) => `wp-admin-command-palette.${format}.js` // Corrected with backticks
    },
    rollupOptions: {
      // make sure to externalize deps that shouldn't be bundled
      // into your library
      // external: ['svelte'],
      output: {
        // Provide global variables to use in the UMD build
        // for externalized deps
        globals: {
          svelte: 'Svelte'
        }
      }
    }
  }
});
