import CqPopinBox from "../../cq-popin-box/CqPopinBox";
import CqBtnGroup from "../../cq-btn-group/CqBtnGroup"



//todo dissocier les popins des boutons

export default class CqContextMenu{

    //todo opti séparer en plusieurs classes: cq-context-menu cq-top-layer

    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        let me=this;
        /**
         *
         * @type {JQuery}
         */
        this.$main=$main;
        /**
         *
         * @type {JQuery} élément courrant dans le dom principal
         */
        this.$anchor=null;

        /**
         * Selon si le bouton d'ajout est placé avant ou après l'item
         * @type {boolean}
         */
        this.addIsBeforeAnchor=false;

        /**
         *
         * @type {JQuery} élément qui délimite une boite pour les selections (intérieur de fenetre)
         */
        this.$anchorBox=null;
        /**
         * Le cadre à la photoshop
         * @type {JQuery}
         */
        this.$selection=$main.find("#the-cq-selection");
        /**
         * Menu de déplacement/config/suppression
         * @type {JQuery}
         */
        this.$menu=$main.find(".is-menu");
        /**
         * Menu d'ajout/upload d'éléments
         * @type {JQuery}
         */
        this.$menuAdd=$main.find(".is-menu-add");
        this.menuAdd=this.$menuAdd.CqBtnGroup();

        this.menu=this.$menu.CqBtnGroup();


        this.$menuAndSelection=$main.find("#menu-and-selection");

        /**
         * Position du menu contextuel par rapport à l'ancre
         * @type {string}
         */
        this.contextMenuPosition="tr";
        /**
         * Selon si le context menu s'inscrit dans une liste horizontale ou non
         * @type {boolean}
         */
        this.horizontal=false;
        /**
         * Selon si le context menu s'inscrit dans une liste ou non
         * @type {boolean}
         */
        this.isInList=false;
        /**
         * La boucle de positionnement des objets
         * @type {number}
         */
        this.loopInterval=setInterval(function(){me._loop();},20);

        /**
         * Calque de fond qui bloque l'accès au reste du dom
         * @type {JQuery}
         */
        this.$disabler=this.$main.find(".disabler");

        /**
         * Boite où on place éventuellement une boite de config
         * @type {JQuery}
         */
        this.$configBox=this.$main.find("#config-box");
        this.configBox=new CqPopinBox(this.$configBox);
        /**
         * Les popins pox
         * @type {JQuery}
         */
        this.$popinBoxes=this.$main.find("[cq-popin-box]");




        /**
         * au click ferme le disabler et la config
         */
        this.$main.on("click","[href='#hide-stuff']",function(e){
            e.preventDefault();
           me._hideStuff();
        });



        function buttonAction($btn,cb){
            $btn.css("display","").on("click",function(e){
                e.preventDefault();
                cb();
            });
            /*
            $btn.onmouseleave(function(){
                if(!me.somethingIsOpen() && !me.isHover()){
                    me.hide().setAnchor(null);
                }
            });
            */

        }


        this.btns={
            //todo opti virer les acces directs aux boutons (private)

            /**
             * @private
             */
            $up     :$main.find("[href='#up']"),
            /**
             * @private
             */
            $left   :$main.find("[href='#left']"),

            /**
             * @private
             */
            $down   :$main.find("[href='#down']"),
            /**
             * @private
             */
            $right  :$main.find("[href='#right']"),
            /**
             * @private
             */
            $trash  :$main.find("[href='#trash']"),
            /**
             * @private
             */
            $cog    :$main.find("[href='#cog']"),
            /**
             * @private
             */
            $previewIcon    :$main.find(".js-preview-icon"),
            /**
             * @private
             */
            $plus   :$main.find("[href='#plus']"),
            /**
             * @private
             */
            $plusUpload   :$main.find("[href='#plus-upload']"),
            /**
             * @private
             */
            $upload :$main.find("[href='#upload']"),



            /**
             * Active le bouton before (fleche left ou up selon si horizontal ou pas) et definit l'action à effectuer
             * @param {function} cb Fonction à effectuer au click
             */
            before:function(cb){
                if(me.horizontal){
                    buttonAction(this.$left,cb);
                }else{
                    buttonAction(this.$up,cb);
                }

            },
            /**
             * Active le bouton after (fleche right ou down selon si horizontal ou pas) et definit l'action à effectuer
             * @param {function} cb Fonction à effectuer au click
             */
            after:function(cb){
                if(me.horizontal){
                    buttonAction(this.$right,cb);
                }else{
                    buttonAction(this.$down,cb);
                }
            },
            /**
             * Active le bouton poubelle et definit l'action à effectuer au click
             * @param {function} cb Fonction à effectuer au click
             */
            trash:function(cb){
                buttonAction(this.$trash,cb);
            },
            /**
             * Active le bouton config et definit l'action à effectuer au click
             * @param cb
             */
            cog:function(cb){
                buttonAction(this.$cog,cb);
            },
            /**
             * Affiche l'icone svg en preview
             * @param svgIdentifier
             */
            setPreviewIcon:function(svgIdentifier,title){
                if(svgIdentifier){
                    this.$previewIcon.css("display","flex");
                    this.$previewIcon.attr("title",title);
                    this.$previewIcon.find("svg use").attr("xlink:href",svgIdentifier);
                }else{
                    this.$previewIcon.css("display","none")
                }

            },

            /**
             * Active le bouton plus et definit l'action à effectuer au click
             * @param {function} cb le callback renvoie isBefore:boolean
             */
            plus:function(cb){
                this.$plus.css("display","").on("click",function(e){
                    e.preventDefault();
                    cb(me.addIsBeforeAnchor);
                });
            },
            /**
             *
             * @param {function} cb le callback renvoie isBefore:boolean et les File[] selectionnés
             */
            plusUpload:function(cb){
                this.$plusUpload().css("display","").on("change",function(e){
                    e.preventDefault();
                    cb(me.addIsBeforeAnchor,e.target.files);
                });
            },
            /**
             * Active le bouton d'upload d'images et définit l'action à faire quand on a selectionné des fichiers
             * @param {function} cb le callback renvoie isBefore:boolean et les File[] selectionnés
             */
            plusUploadImages:function(cb){
                this.$plusUploadImagesOnly().css("display","").on("change",function(e){
                    e.preventDefault();
                    cb(me.addIsBeforeAnchor,e.target.files);
                });
            },
            /**
             * Active le bouton d'uploa et définit l'action à faire quand on a selectionné des fichiers
             * @param {function} cb le callback renvoie isBefore:boolean et les File[] selectionnés
             */
            upload:function(cb){
                buttonAction(me.addIsBeforeAnchor,this.$upload,cb);
            },
            /**
             * Définit la taille du menu contextuel
             * @param {string} size
             * @private
             * @returns {*}
             */
            _setSize(size="normal"){
                me.$menu.attr("size",size);
                me.$menuAdd.attr("size",size);
                return this;
            },

            /**
             * Tous les boutons
             * @type {JQuery}
             */
            $all:me.$menu.find("a[href*='#']").add(me.$menuAdd.find("a[href*='#']")),
            /**
             * masque et annule toutes les actions sur les boutons
             * @returns {CqContextMenu}
             */
            reset:function(){
                me.btns.$all.off("click");
                me.btns.$all.off("change");
                me.btns.$all.off("input");
                me.btns.$all.css("display","none");
                me.btns.$all.find("input[type='file']").val("");
                me.btns.$all.find("input[type='file']").removeAttr("accept");
                me.btns.$all.removeClass("first");
                me.btns.$all.removeClass("last");
                //me.$menu.attr("size","");
                //me.$menuAdd.attr("size","");
                return me;
            },
            /**
             * Le bouton $plusUpload en mode image only
             * @returns {JQuery}
             */
            $plusUploadImagesOnly(){
                me.btns.$plusUpload.find("input").attr("accept","image/*");
                return me.btns.$plusUpload;
            },

            /**
             * Le bouton $upload en mode image only
             * @returns {JQuery}
             */
            $uploadImagesOnly(){
                me.btns.$upload.find("input").attr("accept","image/*");
                return me.btns.$upload;
            },

        };

        this.btns.reset();
        this._hideStuff();
        this.hide();



    }

    /**
     * Ferme les popin-box et le disabler
     * @private
     */
    _hideStuff(){
        this.$popinBoxes.removeClass("open");
        this.$disabler.css("display","none");
    }


    /**
     * Boucle qui positionne les éléments
     * @private
     */
    _loop(){

        let me=this;

        if(me.visible && me.$anchor){
            let rect=me.$anchor.get(0).getBoundingClientRect();

            //teste si la souris est toujours en roll over (avec une tolérance)
            if( !(rect.left-30<STAGE.mouseX && rect.right+30>STAGE.mouseX && rect.top-30<STAGE.mouseY && rect.bottom+30>STAGE.mouseY)){
                this.setAnchor(null);
                return
            }

            let offset=0;

            //place les menu contextuel dans une boite ou pas selon où est l'$anchor
            if(me.$anchorBox && me.$anchorBox.get(0)){
                let pathBox=me.$anchorBox.get(0).getBoundingClientRect();
                me.$menuAndSelection.css("clip","rect("+pathBox.top+"px, "+pathBox.right+"px, "+pathBox.bottom+"px, "+pathBox.left+"px)");
            }else{
                me.$menuAndSelection.css("clip","");
            }

            TweenMax.to(me.$selection,0,
                {
                    x:rect.left-offset,
                    y:rect.top-offset,
                    width:rect.width+offset*2,
                    height:rect.height+offset*2,
                    roundProps:"x,y,width,height"
                }
            );

            let x,y,xAdd,yAdd;


            if(me.isInList){
                if(me.horizontal){
                    yAdd=rect.top+rect.height/2-me.$menuAdd.height()/2;
                    if(STAGE.mouseX<rect.left+rect.width/2){
                        me.addIsBeforeAnchor=true;
                        xAdd=rect.left-me.$menuAdd.width()/2-offset;
                    }else{
                        me.addIsBeforeAnchor=false;
                        xAdd=rect.left+rect.width-me.$menuAdd.width()/2+offset;
                    }
                }else{
                    xAdd=rect.left+rect.width/2-me.$menuAdd.width()/2;
                    if(STAGE.mouseY<rect.top+rect.height/2){
                        me.addIsBeforeAnchor=true;
                        yAdd=rect.top-me.$menuAdd.height()/2-offset;
                    }else{
                        me.addIsBeforeAnchor=false;
                        yAdd=rect.top+rect.height-me.$menuAdd.height()/2+offset;
                    }
                }
            }


            /**
             * Position du bouton d'ajout centré en X
             * @type {number}
             */
            let centerXAdd = rect.left + rect.width / 2 - me.menuAdd.width()/2;

            switch (me.contextMenuPosition){
                case "t":
                case "b":
                case "c":
                    x =  rect.left + rect.width/2 - me.$menu.width()/2;
                    break;
                case "l":
                case "tl":
                case "bl":
                    x =  rect.left;
                    if(me.isInList){
                        if(!me.horizontal){
                            Math.max(centerXAdd,xAdd = x+ me.$menu.width() + 10);
                        }
                    }

                    break;
                case "r":
                case "tr":
                case "br":
                default:
                    x =  rect.left + rect.width - me.$menu.width();
                    if(me.isInList){
                        if(!me.horizontal){
                            xAdd = Math.min(centerXAdd , x - me.$menuAdd.width() - 10);
                        }
                    }
                    x=Math.min( x, STAGE.width - me.$menu.width() - 4);
            }

            switch (me.contextMenuPosition){
                case "c":
                case "l":
                case "r":
                    y =  rect.top+rect.height/2-me.$menu.height()/2; //centré
                    break;
                case "b":
                case "bl":
                case "br":
                    y =  rect.top+rect.height+offset; //en bas
                    break;
                case "t":
                case "tr":
                case "tl":
                default:
                    y=  rect.top - me.$menu.height()/2-offset; //en haut
                    y=Math.max(4,y);
            }

            if(!me.isInList){
                switch (me.contextMenuPosition){
                    case "t":
                    case "c":
                    case "b":
                        x+=me.$menu.width()/2 + 5;
                        break;
                    default:
                        break
                }
                xAdd=x-me.$menuAdd.width() -10;
                yAdd=y;
            }

            TweenMax.to(me.$menu,0,
                {
                    x:  x,
                    y:  y,
                    roundProps:"x,y"
                }
            );
            TweenMax.to(me.$menuAdd,0,
                {
                    x:  xAdd,
                    y:  yAdd,
                    roundProps:"x,y"
                }
            );

        }else{
            //console.log("loop NO $anchor",me.$anchor);
        }
    }

    /**
     * montre le menu
     * @returns {CqContextMenu}
     */
    show(){
        this.visible=true;
        this.$main.css("display","block");
        return this;
    }

    /**
     * cache le menu
     * @returns {CqContextMenu}
     */
    hide(){
        this.visible=false;
        this.$main.css("display","none");
        return this;
    }

    /**
     * Définit un élément comme étant l'élément actif du menu contextuel
     * Le menu contextuel changera de forme en fonction de cet élément
     * @param {JQuery} $element
     * @returns {CqContextMenu}
     */
    setAnchor($element){
        let me=this;

        //console.log("setAnchor",$element);
        me.$anchor=$element;
        if(me.$anchor){

            //la position
            let position=me.$anchor.closest("[context-menu-position]").attr("context-menu-position");
            if(!position){
                position="tr";
            }
            me.contextMenuPosition=position;

            //la taille
            let size=me.$anchor.closest("[context-menu-size]").attr("context-menu-size");
            me.btns._setSize(size);
            me.hideAnchor(false);

            //le sens
            let horizontal=me.$anchor.closest("[list-horizontal]").attr("list-horizontal");
            me.horizontal=horizontal==="true";

            //dans une liste ou pas?
            let isInList=me.$anchor.closest("[context-menu-is-list]").attr("context-menu-is-list");
            me.isInList=isInList==="true";

            //le "boxing" qui influera sur le z-index et l'overflow scrolling
            if(me.$anchor.is("#the-cq-layer *")){
                me.$main.addClass("is-over");
                me.$main.addClass("anchor-is-over");
                me.$anchorBox=me.$anchor.closest("[cq-popin-box] main, .wysiwyg-big-menu main");
            }else{
                me.$main.removeClass("is-over");
                me.$main.removeClass("anchor-is-over");
                me.$anchorBox=null;
            }
        }else {
            me.hideAnchor();
            me.$anchorBox=null;

        }
        return this;
    }

    hideAnchor(hide=true){
        if(hide){
            this.$menuAndSelection.css("display","none");
        }else{
            this.$menuAndSelection.css("display","");
        }

    }

    /**
     * Teste si la souris est sur le machin
     * @returns {boolean}
     */
    isHover() {
        return this.$main.is(":hover");
    }

    /**
     * true si une pop in est ouverte
     * @returns {boolean}
     */
    somethingIsOpen(){
        return this.$popinBoxes.filter(".open").length>0;
    }

    /**
     *
     * @param {string} configPath Chemin vers la vue de la boute de config
     * @param {String} uid identifiant unique du modèle relatif
     */
    showConfig(configPath,uid){
        this.setAnchor(null);
        this.configBox.open();
        this.$disabler.css("display","");
        this.configBox.$content.empty();
        if(uid && configPath){
            let $configLoader=$("<div></div>");
            $configLoader.attr("data-pov-v-path",configPath);
            $configLoader.attr("data-pov-vv-uid",uid);
            $configLoader.attr("data-pov-refresh-method","html");
            $configLoader.attr("id","config-loader");
            this.configBox.$content.append($configLoader);
            $configLoader.povRefresh();

        }else{
            console.error("pas d'uid ou configPath pour charger la config")
        }
    }
}