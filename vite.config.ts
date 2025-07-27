import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig } from 'vite'
import { nodePolyfills } from 'vite-plugin-node-polyfills'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      ssr: 'resources/js/ssr.ts',
      refresh: true,
    }),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    nodePolyfills({
      protocolImports: true, // Important for crypto
    }),
  ],
  define: {
    'process.env': {}, // Prevent process.env undefined
  },
  optimizeDeps: {
    esbuildOptions: {
      target: 'esnext', // Required for polyfills
    },
  },
})
