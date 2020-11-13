const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

/*
 * SplitChunksPlugin is enabled by default and replaced
 * deprecated CommonsChunkPlugin. It automatically identifies modules which
 * should be splitted of chunk by heuristics using module duplication count and
 * module category (i. e. node_modules). And splits the chunks…
 *
 * It is safe to remove "splitChunks" from the generated configuration
 * and was added as an educational example.
 *
 * https://webpack.js.org/plugins/split-chunks-plugin/
 *
 */

/*
 * We've enabled HtmlWebpackPlugin for you! This generates a html
 * page for you when you compile webpack, which will make you start
 * developing and prototyping faster.
 * 
 * https://github.com/jantimon/html-webpack-plugin
 * 
 */

module.exports = {
  mode: 'development',
  entry: {
    "index": './static-src/index.js',
    "pageIndex": './static-src/pageIndex.js',
  },

  output: {
    filename: 'js/[name].js',
    path: path.resolve(__dirname, 'public/static')
  },

  plugins: [
      new webpack.ProgressPlugin(),
      new MiniCssExtractPlugin(),
  ],

  module: {
    rules: [{
      test: /.(js|jsx)$/,
      include: [path.resolve(__dirname, 'static-src')],
      loader: 'babel-loader',

      options: {
        plugins: ['syntax-dynamic-import'],

        presets: [['@babel/preset-env', {
          'modules': false
        }]]
      }
    }, {
      test: /.(css|less)$/,
      use: [
        {
          loader: "file-loader",
          options: {
            name: "css/[name].css",
          },
        },
        {
          loader: "extract-loader",
        },
        {
          loader: "css-loader",
        },
        {
          loader: "less-loader",
        },
      ]
    }, {
      test: /\.(png|jpe?g|gif|svg)$/i,
      use: [
        {
          loader: 'file-loader',
          options: {
            name: 'assets/[name].[ext]',
            publicPath: '../',
          }
        },
      ],
    }]
  },

  optimization: {
    splitChunks: {
      cacheGroups: {
        vendors: {
          priority: -10,
          test: /[\\/]node_modules[\\/]/
        }
      },

      chunks: 'async',
      minChunks: 1,
      minSize: 30000,
      name: false
    }
  },

  devServer: {
    open: true
  }
}