var fs = require('fs');
/**
 * Ce fichier grunt ne sert qu'à compiler des icones svg.
 * Recherche des répertoires nommés "svg-collection-quelque-chose" puis:
 * - optimise les fichiers svg trouvés dedans
 * - crée un fichier dist/svg-collection/quelque-chose.svg qui contien les symboles de tous les svg du répertoire
 * - crée une fichier de prévisualisation des icones dans dist/svg-collection/quelque-chose.html
 * - crée une fichier de prévisualisation des icones dans dist/svg-collection/quelque-chose.json qui contient un récapitulatif des symboles svg
 *
 * Vous pouvez créer des sous répertoires dans votre répertoire svg-collection/quelque-chose le nom du répertoire sera ajouté à l'id des symboles svg qu'il contient.
 *
 * @param grunt
 */
module.exports = function (grunt) {
    "use strict"

    grunt.log.warn = function(error) {
        grunt.fail.warn('grrrrrrrrrrrrrrrrrRRRrrrrrrrrrrrrrrrrrRRRRrrrrrrrrrrrrrrrrrrrr..... '+error); // Forced stop.
    };

    var dstDir="./dist/svg-collection";
    var tmpDir="./dist/svg-collection-tmp";


    require('load-grunt-tasks')(grunt); // npm install --save-dev load-grunt-tasks

    let config=    {
        pkg: grunt.file.readJSON('package.json'),

        clean: {
            svgDist: {
                options: {
                    'force': true
                },
                src: [dstDir]
            },
            svgTmp: {
                options: {
                    'force': true
                },
                src: [tmpDir]
            }
        }
        ,svgmin: {
            options: {
                plugins: [
                    {"removeViewBox":false}
                    ,{"removeMetadata":true}
                    ,{"removeUnknownsAndDefaults":{
                            keepDataAttrs: false
                        }}
                    ,{"removeTitle":true}
                    ,{"cleanupIDs":
                            {
                                remove: true,
                                minify: false,
                                prefix: '',
                                preserve: [],
                                force: true
                            }
                    }
                    ,{"convertStyleToAttrs":true}
                    ,{"removeStyleElement":true}

                    ,{"removeEmptyContainers":true}
                    ,{"removeUselessDefs":true}
                    ,{removeAttrs:
                            {
                                attrs: ['xmlns','data-.*','id','class']
                            }
                    }

                ]
            },
            tmp: {
                files: [
                    {
                        expand: true,     // Enable dynamic expansion.
                        cwd:"./",
                        src: ['./**/svg-collection-**/**/*.svg'], // Actual pattern(s) to match.
                        rename: function (dest, src) {          // The `dest` and `src` values can be passed into the function
                            let matches = src.match(/svg-collection-([^\/.]*)\/(.*)+\.svg/);
                            if(!matches){
                                console.log("oups..",src);
                                return null;
                            }else{
                                let collection=matches[1];
                                let id=matches[2];
                                id=id.replace("/","-");

                                let outputFileName=tmpDir+"/"+collection+"/"+collection+"-"+id+".svg";

                                //écrit le nom du fichier dans un fichier texte pour qu'on puisse l'exploiter par la suite
                                require('mkdirp')(dstDir, function(err) {
                                    if(err){
                                        console.log(err);
                                    }
                                });
                                var file = dstDir+"/"+collection+".json";
                                if (!fs.existsSync(file)) {
                                    fs.writeFileSync(file, JSON.stringify(
                                        {
                                            "symbols":[]
                                        },null,2), 'utf8', function(){});
                                }
                                var data=fs.readFileSync(file, 'utf8');
                                let obj = JSON.parse(data);

                                obj.symbols.push(collection+"-"+id); //add some data
                                let json = JSON.stringify(obj); //convert it back to json
                                fs.writeFileSync(file, json, 'utf8', function(){}); // write it back


                                return outputFileName;
                            }




                        }
                    },
                ],
            }
        }
        ,svg_symbols: {
            options: {
                precision: 3,
                "preserveViewBox":true,
                currentColor:true
            },
            dist: {

                //files: {'./dist-svg/symbols.svg': './src/**/*.svg'},
                files: [{
                    expand: true,
                    cwd: './',
                    src: [tmpDir+'/**/*.svg'],
                    rename: function (dest, src) {          // The `dest` and `src` values can be passed into the function
                        let sp=src.split("/");
                        let dir=sp[sp.length-2];
                        return dstDir+"/"+dir+".svg"
                    }
                }]

            },
        }
    };

    grunt.initConfig(config);

    grunt.registerTask('svg',[
        "clean:svgTmp",
        "clean:svgDist",
        "svgmin:tmp",
        "svg_symbols:dist",
        "clean:svgTmp"
    ]);
};