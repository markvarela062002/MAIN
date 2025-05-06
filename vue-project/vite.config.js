import * as path from 'node:path'

import { defineConfig } from 'vite'

import vue from '@vitejs/plugin-vue'
//import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'
import Pages from 'vite-plugin-pages'
import Layouts from 'vite-plugin-vue-layouts';
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import vueI18n from '@intlify/unplugin-vue-i18n/vite'
import pkg from './package.json'
import liveReload from 'vite-plugin-live-reload'

process.env.VITE_APP_BUILD_EPOCH = new Date().getTime()
process.env.VITE_APP_VERSION = pkg.version

/**
 * @type {import('vite').UserConfig}
 */

// https://vite.dev/config/
export default defineConfig({
  server: {
    /* hmr: {
      port: false,
      path: '/ws',
    }, */
    // required to load scripts from custom host
    cors: true,

    // we need a strict port to match on PHP side
    // change freely, but update on PHP to match the same port
    strictPort: true,
    port: 3001
  },

  //root: 'src',
  base: './',

  build: {
    // output dir for production build
    outDir: path.resolve(__dirname, '../assets/vue2'),
    emptyOutDir: true,

    // emit manifest so PHP can find the hashed files
    manifest: true,

    // esbuild target
    target: 'esnext',

    // our entry
    rollupOptions: {
      input: './src/main.ts',
      //output: { 
        plugins: [ // <-- use plugins inside output to not merge chunks on one file
          /* obfuscator({
            fileOptions: {
               // options
            },
            globalOptions: {
              rotateUnicodeArray: true,
              stringArrayEncoding: ['base64'],
            },
            }), */
        ],
      //}
    }
  },

  // https://github.com/antfu/vite-ssg
  ssgOptions: {
    script: 'async',
    formatting: 'minify',
  },
  test: {
    globals: true,
    include: ['test/**/*.test.ts'],
    environment: 'happy-dom',
  },

  optimizeDeps: {
    include: [
      'vue-router',
      '@vueuse/core',
    ],
    exclude: [
      'vue',
      'vue-demi',
    ],
  },

  plugins: [
    vue({
      include: [/\.vue$/, /\.md$/],
      template: {
        compilerOptions: {
          directiveTransforms: {
            styleclass: () => ({
              props: [],
              needRuntime: true,
            }),
            ripple: () => ({
              props: [],
              needRuntime: true,
            }),
          },
        },
      },
    }),

    // https://github.com/antfu/unplugin-auto-import
    AutoImport({
      imports: [
        'vue',
        'vue-router',
        'vue-i18n',
        '@vueuse/head',
      ],
      exclude: [
        '**/dist/**',
      ],
      dts: 'src/auto-import.d.ts',
    }),

    vueI18n({
      include: path.resolve(__dirname, './src/i18n/locales/**'),
    }),

    Components({
      dts: 'src/components.d.ts',
      resolvers: [
      ],
    }),
    //vueDevTools(),
    tailwindcss(),
    Pages({
      // pagesDir: ['src/pages', 'src/pages2'],
      pagesDir: [
        { dir: 'src/pages', baseRoute: '' },
      ],
      extensions: ['vue', 'md'],
      syncIndex: true,
      replaceSquareBrackets: true,
      extendRoute(route) {
        if (route.name === 'about')
          route.props = route => ({ query: route.query.q })

        if (route.name === 'components') {
          return {
            ...route,
            beforeEnter: (route) => {

              // console.log(route)
            },
          }
        }
      },
    }),
    Layouts({
      layoutsDirs: 'src/layouts',
      //pagesDirs: 'src/pages',
      //defaultLayout: 'myDefault'
    }),

    // As the app is SPA
		// We only needs to listen the changes on index.vue.php file for reloading
		// Feel free to edit this part to met your needs
		liveReload(__dirname + "/../application/views/vue2/index.vue.php"),
  ],
  resolve: {
    alias: {
      //'@': fileURLToPath(new URL('./src', import.meta.url)),
      '@': path.resolve(__dirname, 'src'),
      '~': path.resolve(__dirname, 'node_modules/'),
    },
    /* alias: [
      {
        find: '@/',
        replacement: `${path.resolve(__dirname, 'src')}/`,
      },
      {
        find: '@',
        replacement: path.resolve(__dirname, '/src'),
      },
    ], */
  },
})
