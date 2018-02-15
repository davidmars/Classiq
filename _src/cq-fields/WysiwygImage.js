/**
 * Une image éditable
 */
import DisplayObject from "../DisplayObject";
import WysiwygProgressCircle from "../cq-progress-circle/CqProgressCircle";
import WysiwygField from "./WysiwygField";

export default class WysiwygImage extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){

        super($main);

        let me=this;

        /**
         *
         * @type {JQuery}
         */
        this.$main=$main;

        /**
         *
         * @type {WysiwygField}
         */
        this.field=new WysiwygField($main);

        /**
         * @type {string} url de l'image telle qu'utilisée par le navigateur.
         */
        this.src=$main.attr("src");

        /**
         * Format de l'image
         * @type {string}
         */
        this.imageFormat=this.$main.attr("wysiwyg-image-format");
        /**
         * Faut il preserver les gifs ou non?
         * @type {boolean}
         */
        this.preserveGif=this.$main.attr("wysiwyg-image-preserve-gif") === "true" ;

        /**
         *
         * @type {CqProgressCircle}
         */
        this.loading=null;


        $main.on("mouseenter",function(){

            wysiwyg.contextMenu.show().setAnchor($main).btns.reset();

            wysiwyg.contextMenu.btns.trash(function(){
                me.field.$field.attr("wysiwyg-value", "");
                me.loading=new WysiwygProgressCircle(me.$main);
                me.field.doSave(true,function(){
                    //4 recharger l'image formatée
                    window.pov.api.imageFormat("", me.imageFormat, me.preserveGif).then(
                        function (imgUrl) {
                            me.setSrc(imgUrl);
                            me.loading.destroy();
                        },
                        function (error) {
                            console.error(error);
                        }
                    );
                });
                me.dispatchChange();
            });

            wysiwyg.contextMenu.btns.plusUploadImages(
                function(insertBefore,files) {
                    /**
                     * @type {File}
                     */
                    let file = files[0];
                    if (file) {
                        me.loading=new WysiwygProgressCircle(me.$main);
                        //1 uploader le fichier
                        window.pov.api.upload(
                            file,
                            function (progress) {
                                console.log("zzz upload file " + progress)
                                me.loading.setPercent(progress)
                            }
                        ).then(
                            function (apiResponse) {
                                //2 receptionner l'uid du Filerecord
                                console.log("upload ok json", apiResponse);
                                //3 l'enregistrer dans le champ
                                me.field.$field.attr("wysiwyg-value", apiResponse.json.record.uid);
                                me.field.doSave(true,function(){
                                    //4 recharger l'image formatée
                                    window.pov.api.imageFormat(apiResponse.json.record.localPath, me.imageFormat, me.preserveGif).then(
                                        function (imgUrl) {
                                            me.setSrc(imgUrl);
                                            me.loading.destroy();
                                        },
                                        function (error) {
                                            console.error(error);
                                        }
                                    );
                                });

                            },
                            function () {
                                //failed
                                console.log("erreur upload")
                            }
                        );
                    }
                }
            );

        });
        $main.on("mouseleave",function(){
            if(!wysiwyg.contextMenu.somethingIsOpen() && !wysiwyg.contextMenu.isHover()){
                wysiwyg.contextMenu.hide().setAnchor(null);
            }
        });


    }


    /**
     * Change l'attribut src pour afficher une nouvelle image
     * @param {String} src url de l'image
     */
    setSrc(src){
        this.$main.attr("src",src);
    }


    /**
     * Envoie un event pour dire que le champ a changé
     */
    dispatchChange(){
        Pov.events.dispatchDom(this.$main,Wysiwyg.events.CHANGED);
    }

    /**
     *
     */
    destroy(){
        //this=null;
    }



}