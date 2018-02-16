const webpack = require('webpack');
const merge = require('webpack-merge');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const common = require('../webpack.common.js');
var build="prod";

module.exports = merge(common, {
    devtool: 'source-map',
    plugins: [
        new UglifyJSPlugin({sourceMap: true}),
        new webpack.DefinePlugin({
            NODE_ENV: JSON.stringify('production'),
            PRODUCTION: JSON.stringify(true)
        })
   ],
    module:{
        rules:[
            {
                //babelize
                test: [/\.js$/],
                exclude: [/node_modules/],
                loader: 'babel-loader',
                options: { presets: ['es2015'] }
            }
        ]
    }
});