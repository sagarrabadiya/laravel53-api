/**
 * Created by sagar on 23/08/16.
 */
var webpack = require('webpack');

module.exports = {
    entry: {
        app: './resources/assets/js/app.js',
        vendor: ['./resources/assets/js/vendor.js', 'vue', 'vue-router', 'vuex', 'jquery', 'lodash', 'highcharts', 'vue-strap']
    },
    output: {
        filename: "[name].js",
        publicPath: '/js/',
        chunkFilename: "partial.[id].js"
    },
    plugins: [ new webpack.optimize.CommonsChunkPlugin({ name: 'vendor', filename: 'vendor.js'}) ],
    loaders: [
        { test: /\.css$/, loader: "style-loader!css-loader" },
        { test: /\.styl$/, loader: 'css-loader!stylus-loader' }
    ]
};
