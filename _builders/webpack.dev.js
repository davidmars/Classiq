const webpack = require('webpack');
const merge = require('webpack-merge');

const common = require('../webpack.common.js');
    module.exports = merge(common, {
        devtool: 'source-map',
        devServer: {
         contentBase: './dist'
        },
        plugins: [
            new webpack.DefinePlugin({
                NODE_ENV: JSON.stringify('developpement'),
                PRODUCTION: JSON.stringify(false)
            })
        ],

    }
 );