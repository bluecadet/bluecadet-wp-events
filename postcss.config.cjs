function buildPluginArray(ctx) {

  const plugins = {
    'postcss-easy-import': {
      prefix: false,
      skipDuplicates: false,
      warnOnEmpty: false,
      extensions: ['.css', '.pcss']
    },
    'postcss-advanced-variables': {},
    'postcss-custom-media': {},
    'postcss-preset-env': {
      features: {
        "nesting-rules": false,
      },
    },
    '@bluecadet/postcss-do-math': {},
    'postcss-extend': {},
    'postcss-nested': {},
    'postcss-class-apply/dist/index': {},
    'postcss-hexrgba': {},
    'postcss-assets': {},
    'postcss-utopia': {
      minWidth: 400, // Default minimum viewport
      maxWidth: 1200, // Default maximum viewport
    },
    'postcss-discard-comments': {}
  };

  if (ctx.bldrEnv === 'build') {
    plugins.cssnano = {};
    plugins.autoprefixer = {};
  }

  return plugins
}

module.exports = (ctx) => ({
  plugins: buildPluginArray(ctx)
});
