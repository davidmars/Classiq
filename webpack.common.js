const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const webpack = require('webpack');
var build="dev";
//less
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const extractLess = new ExtractTextPlugin({
    filename: "[name].css",
    disable: process.env.NODE_ENV === "development"
});

let d=new Date();
let webpackTime=function()
{
    return "" +
    d.getFullYear()
    + "-"
    + String(d.getMonth() + 1).padStart(2, "0")
    + "-"
    + String(d.getDate()).padStart(2, "0")
    + "."
    + String(d.getHours()).padStart(2, "0")
    + ":"
    + String(d.getMinutes()).padStart(2, "0")
    + ":"
    + String(d.getSeconds()).padStart(2, "0")
    + "-"+build;

};
//---assets-----

function getAssetPath(file,addHash=true,baseDirInAsset=""){
    let p=require('path');
    let ext=p.extname(file);
    ext=ext.replace(".","",ext);
    let name=p.basename(file);
    name=name.replace("."+ext,"",name);
    let hash=require('md5-file').sync(file);
    if(addHash){
        name+="-"+hash;
    }
    let dirs=["assets"];
    if(baseDirInAsset){
        dirs.push(baseDirInAsset);
    }
    dirs.push(ext);
    return dirs.join("/")+"/"+name+'.'+ext;

}


//---less-----

const LessPluginAutoprefix=require('less-plugin-autoprefix');
const lessPluginAutoprefix=new LessPluginAutoprefix({browsers: ["last 6 versions"]});
const lessPluginLists=new (require('less-plugin-lists'));

var lessOptions=function(){
    return{
        compress: true,
        yuicompress: false,
        optimization: 2,
        cleancss: false,
        paths: ["dist"],
        relativeUrls: true,
        syncImport: false,
        strictUnits: false,
        strictMath: true,
        strictImports: true,
        ieCompat: false,
        sourceMap: true,
        url:false,
        plugins: [lessPluginAutoprefix,lessPluginLists],
        modifyVars: {
            webpackTime: "'"+webpackTime()+"'",
            build: "'"+build+"'"
        }
    }
};

var cssOptions={
    sourceMap: true,
    minimize: true
};




module.exports = {
    entry: {
        "classiq.boot":     "./Classiq/_src/classiq.boot.js", //le site pour monsieur tout le monde
        wysiwyg:            "./Classiq/_src/classiq-wysiwyg.js", //l'ui wysiwyg
        //login:      "./Classiq/_src/classiq-login.js", // (pour le login uniquement)
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'dist'),
    },
    // Tell Webpack which directories to look in to resolve import statements.
    // Normally Webpack will look in node_modules by default but since we’re overriding
    // the property we’ll need to tell it to look there in addition to the
    // bower_components folder.
    resolve: {
        modules: [
            path.resolve(__dirname, 'node_modules'),
        ]
    },
    plugins: [
        function() {
            this.plugin('watch-run', function(watching, callback) {
                console.log("\x1b[42m",'waiting...',"\x1b[0m");
                t = setTimeout(
                    function(){
                        console.log("\x1b[42m",'Go ' + new Date().getHours()+"h "+new Date().getMinutes()+":"+new Date().getSeconds(),"\x1b[0m");
                        callback();
                    }
                    , 0); //petit delais pour laisser à phpstorm le temps de faire ses trucs...ou pas :)
            });
        },
        new CleanWebpackPlugin(
            [
            'dist/css',
            "dist/js",
            "dist/assets"
            ]
        ),
        new webpack.DefinePlugin({
            'webpackVersion': JSON.stringify(
                {
                    "webpackTime":webpackTime()
                }
            ),
            MYVARIABLE:JSON.stringify("test-value")
        }),
        extractLess,
    ],
    module: {
        rules: [
            //fonts
            {
                test: /fonts.*\.(eot|ttf|otf|woff|woff2|svg)$/,
                use: [
                    {loader: "file-loader",
                        options: {
                            name (file) {
                                return getAssetPath(file,false,"font");
                            }
                        }
                    },
                ],
            },
            //inline svg
            {
                test: /\.inline\.svg$/,
                loader: 'svg-inline-loader'
            },
            {
            test: /\.(html)$/,
                use: {
                    loader: 'html-loader',
                    options: {

                    }
                }
            },
            //images svg etc...
            {
                test: /\.(jpg|gif|txt|png|svg)$/,
                exclude: /inline|fonts.*\.(svg)$/,
                use: [
                    {loader: "file-loader",
                        options: {
                            name (file) {

                                let outputFileName=getAssetPath(file);
                                //écrit le nom du fichier dans un fichier texte pour qu'on puisse l'exploiter par la suite
                                require('mkdirp')('dist/assets', function(err) {
                                    // path exists unless there was an error
                                });
                                var file = 'dist/assets/recap.txt';
                                require('fs').appendFile(file, outputFileName, function (err) {
                                });
                                return outputFileName;
                            }
                        }
                    },
                ],
            },
            //css
            {
                test: /\.css$/,
                use: extractLess.extract({
                    use:
                        [
                            {loader: "css-loader",options: cssOptions},
                        ],
                })

            },
            //less
            {
                test: /\.less$/,
                use: extractLess.extract({
                    use:
                        [
                            //{loader: "style-loader"},
                            {loader: "css-loader",options: cssOptions},
                            {loader: "less-loader",options: lessOptions()}
                        ],
                })
            },
            //scss
            {
                test: /\.scss$/,
                use: extractLess.extract({
                    use:
                        [
                            //{loader: "style-loader"},
                            {loader: "css-loader",options: cssOptions},
                            {loader: "sass-loader",options: {}}
                        ],
                })
            },
            //mustache templates
            {
                test: /\.mst/,
                loader: 'mustache-loader'
            }
        ]
    }
};