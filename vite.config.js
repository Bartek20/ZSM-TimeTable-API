import Components from 'unplugin-vue-components/vite';
import AutoImport from 'unplugin-auto-import/vite';
import autoprefixer from 'autoprefixer';
import path from 'path';
import { glob } from 'glob';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

function getGlobs(pattern) {
  return glob.sync(pattern);
}

const root = process.env.ROOT_PATH || '/plan_api/';

const now = new Intl.DateTimeFormat('en-US', {
  timeZone: 'Europe/Warsaw',
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: '2-digit',
  hour: '2-digit',
  minute: '2-digit',
  second: '2-digit',
}).format(new Date());

export default defineConfig({
  base: root,
  server: {
    base: '/',
    proxy: {
      plan_vulcan: {
        target: 'https://zsm.resman.pl',
        changeOrigin: true,
      },
    },
  },
  plugins: [
    vue(),
    Components({
      dirs: ['src/components'],
      extensions: ['vue'],
    }),
    AutoImport({
      include: [/\.js$/, /\.vue$/, /\.vue\?vue/],
      imports: [
        'vue',
        'vue-router',
        {
          axios: [['default', 'axios']],
          dropzone: [['default', 'Dropzone']],
        },
      ],
      // dirs: ['src/functions'],
      vueTemplate: true,
    }),
  ],
  build: {
    outDir: './dist/plan_api',
    minify: 'terser',
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        manualChunks: {
          axios: ['axios'],
          vue: ['vue', 'vue-router'],
          components: [].concat(getGlobs('./src/{views,functions,router}/**/*')).concat(getGlobs('./src/components/**/*')),
        },
      },
    },
  },
  css: {
    postcss: {
      plugins: [autoprefixer({})],
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
      '@bootstrap': path.resolve(__dirname, 'node_modules/bootstrap/scss'),
      '@dropzone': path.resolve(__dirname, 'node_modules/dropzone/dist'),
    },
  },
});
