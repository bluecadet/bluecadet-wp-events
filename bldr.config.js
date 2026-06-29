import { bldrConfig } from '@bluecadet/bldr/config';
import injectProcessEnv from 'rollup-plugin-inject-process-env';

export default bldrConfig({
  sass: [
    {
      src: './assets/classicEditor/src/*.scss',
      dest: './assets/classicEditor/dist',
    }
  ],
  js: [
    {
      src: './assets/classicEditor/src/*.js',
      dest: './assets/classicEditor/dist',
    }
  ],
  watchPaths: [
    './assets/classicEditor',
    './editor/styles',
    './editor/components',
    './BluecadetEvents/Admin/Editor/ClassicEditor',
  ],
  reloadExtensions: ['twig', 'php', 'html'],
  eslint: {
    useEslint: false,
  },
  stylelint: {
    useStyleLint: false,
    forceBuildIfError: false,
  },
  biome: {
    useBiome: false,
    forceBuildIfError: false,
    dev: false,
    devWrite: true,
    devFormat: true,
  },
  rollup: {
    outputOptions: {
      format: 'iife',
      globals: {
        '$': '$',
        'jQuery': '$',
        'drupal': 'Drupal',
        'wp': 'wp',
      }
    },
    useSWC: true,
    useTerser: true,
    inputPlugins: [
      injectProcessEnv({ NODE_ENV: 'production' }),
    ],
  },
  browsersync: {
    proxy: 'https://basecadet-wpe.ddev.site/'
  }
});
