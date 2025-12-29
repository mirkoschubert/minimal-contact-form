const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    admin: path.resolve(__dirname, 'admin-app/src/index.tsx'),
    'blocks/contact-form/index': path.resolve(__dirname, 'blocks/contact-form/index.tsx'),
  },
  output: {
    path: path.resolve(__dirname, 'assets'),
    filename: (pathData) => {
      return pathData.chunk.name === 'admin'
        ? 'admin/js/[name].js'
        : '[name].js';
    },
    chunkFilename: (pathData) => {
      // Put chunk files in admin/js/ directory
      return 'admin/js/[id].js';
    },
  },
  plugins: defaultConfig.plugins.map(plugin => {
    if (plugin.constructor.name === 'MiniCssExtractPlugin') {
      const MiniCssExtractPlugin = plugin.constructor;
      return new MiniCssExtractPlugin({
        filename: (pathData) => {
          return pathData.chunk.name === 'admin'
            ? 'admin/css/[name].css'
            : '[name].css';
        },
      });
    }
    if (plugin.constructor.name === 'RtlCssPlugin') {
      const RtlCssPlugin = plugin.constructor;
      return new RtlCssPlugin({
        filename: 'admin/css/[name]-rtl.css',
      });
    }
    return plugin;
  }),
};
