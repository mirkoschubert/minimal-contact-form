const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    admin: path.resolve(__dirname, 'admin-app/src/index.tsx'),
  },
  output: {
    path: path.resolve(__dirname, 'assets/admin'),
    filename: 'js/[name].js',
  },
  plugins: defaultConfig.plugins.map(plugin => {
    if (plugin.constructor.name === 'MiniCssExtractPlugin') {
      const MiniCssExtractPlugin = plugin.constructor;
      return new MiniCssExtractPlugin({
        filename: 'css/[name].css',
      });
    }
    if (plugin.constructor.name === 'RtlCssPlugin') {
      const RtlCssPlugin = plugin.constructor;
      return new RtlCssPlugin({
        filename: (pathData) => {
          return pathData.chunk.name.replace('[name]', 'css/[name]-rtl');
        },
      });
    }
    return plugin;
  }),
};
