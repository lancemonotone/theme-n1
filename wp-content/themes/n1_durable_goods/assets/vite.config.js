import { defineConfig } from 'vite';

export default defineConfig( {
  // Load scss to be used globally (functions, variables)
  css: {
  //   preprocessorOptions: {
  //     scss: {
  //       additionalData: ` @import '../src/scss/utility/_normalize.scss';
  //                         @import '../src/scss/utility/_a11y.scss';
  //                         @import '../src/scss/utility/_functions.scss';
  //                         @import '../src/scss/utility/_print.scss';
  //                         @import '../src/scss/utility/_variables.scss';
  //                         @import '../src/scss/utility/_typography.scss';`,
  //     }
  //   }
  },
  build: {
    target: 'es2020',
    lib          : {
      entry: './src/index.js',
      name : 'vite',
    },
    sourcemap    : true,
    minify       : 'terser',
    terserOptions: {
      compress: true,
      output: {
        comments: false,
      },
    },
    outDir       : './build/',
    emptyOutDir  : true,
    rollupOptions: {
      output: {
        entryFileNames: `js/[name].js`,
        chunkFileNames: `js/[name].js`,
        assetFileNames: ( assetInfo ) => {
          let extType = assetInfo.name.split( '.' ).at( 1 );
          if ( /png|jpe?g|svg|gif|tiff|bmp|ico/i.test( extType ) ) {
            extType = 'img';
          }
          // if extType is 'css' return 'index.css'
          let fileName = extType === 'css' ? 'index' : assetInfo.name;
          return `${ extType }/${ fileName }[extname]`;
        },
      },
    },
  },
} );
