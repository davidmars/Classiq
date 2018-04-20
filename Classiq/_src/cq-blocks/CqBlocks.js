/**
 * Une liste d'éléments réorganisables
 */
import WysiwygField from "../cq-fields/WysiwygField";
import Wysiwyg from "../Wysiwyg";
import CqSortable from "../cq-sortable/CqSortable";
import CqBlockPicker from "../cq-block-picker/CqBlockPicker"
import CqBlock from "./CqBlock";
import CqBlockFile from "./CqBlockFile";
const Sortable=require("sortablejs");
require("../cq-drag-btn/cq-drag-btn.less");

//todo opti virer tous les attributs et mettre ça dans options

export default class CqBlocks extends CqSortable{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main);
        let me=this;
        this.$main.on(Pov.events.DOM_CHANGE,function(){
            me._initItems();
        });

        /**
         *
         * @type {object}
         */
        this.options=JSON.parse($main.attr("cq-field-options"));

        /**
         * Faut il faire en sorte que la liste ne soit jamais vide?
         * @type {bool}
         */
        this.preventEmpty=this.options.preventEmpty;

        /**
         * Position x des popins (multiplicateur)
         * @type {number}
         */
        this.popinXpos=this.options.popinXpos;
        /**
         *
         * Position y des popins (multiplicateur)
         * @type {number}
         */
        this.popinYpos=this.options.popinYpos;

        /**
         * Action a réaliser qaund la touche ENTER est pressée dans un des items
         * @type {string}
         */
        this.keyEnterAction=this.options.keyEnterAction;

        /**
         * Message à afficher dans le block picker
         * @type {string}
         */
        this.blockPickerMessage=this.options.blockPickerMessage;
        /**
         * Message à afficher dans le block picker quand la liste est vide
         * @type {string}
         */
        this.blockPickerEmptyMessage=this.options.blockPickerEmptyMessage;

        /**
         * Si true, la liste n'est composée que de records. Le bouton + ouvre la popin de sélection
         * @type {boolean}
         */
        this.onlyRecords=this.options.onlyRecords;
        /**
         * Si true, la liste n'est composée que de records file image. Le bouton + ouvre un selecteur de fichiers
         * @type {boolean}
         */
        this.onlyImages=this.options.onlyImages;
        /**
         * La liste des types de records possibles à insérer
         * @type {string}
         */
        this.onlyRecordsTypes=this.options.onlyRecordsTypes;






        /**
         * Les templates possibles dans cette liste
         * @type {String[]} Chemins des templates qu'il est possible d'intégret dans cette liste
         */
        this.templates=$main.attr("wysiwyg-item-templates").split(",");
        /**
         * Avant que ceci ne soit true pas grand chose ne sera possible
         * @type {boolean}
         */
        me.templatesLoaded=false;
        me.$main.css("opacity",0.2);

        /**
         * L'ui qui permet d'ajouter des templates
         * @type {CqBlockPicker}
         */
        this.blockPicker=new CqBlockPicker(
            null,
            this.blockPickerMessage,
            this.templates
        );
        this.blockPicker.loadTemplates(this.templates,function(){
            me.templatesLoaded=true;
            me.$main.css("opacity","");
        });

        //clicks dans le block picker

        this.$main.on("click",">[cq-block-picker] [path]",function(e){
            let $btn=$(this);
            let template=$btn.attr("path");
            if($btn.find("input[type='file']").length){
                //return;
            }else{
                e.preventDefault();
                if(me.onlyRecords){
                    me.addRecordsItems(null,false);

                }else{
                    let $newItem=me._$getNewItem(template);
                    //0 remplace le block picker par un nouveau block
                    me.blockPicker.$main.replaceWith($newItem);
                    //1 save la liste avec le nouvel élément
                    me.field.doSave(true,function(){
                        //2 rafraichit le template de l'item
                        $newItem.povRefresh(function($el){
                            //3 initialise l'item
                            me._initItem($el,true);
                        });
                    });
                    me.checkIfEmpty();
                }


            }
        });

        this.$main.on("change",">[cq-block-picker] [path] input[type='file']",function(e){
            let $input=$(this);
            /**
             *
             * @type {string}
             */
            let template=$input.closest("[path]").attr("path");
            /**
             * @type {File[]}
             */
            let files=e.target.files;

            if($input.attr("inject-files-in-block")){
                //crée un block normalement et lui tranmettra les fichiers ensuite
                console.log("créer le block "+template+"et ensuite lui transmetre les fichiers");
                let $newItem=me._$getNewItem(template);
                //0 remplace le block picker par un nouveau block
                me.blockPicker.$main.replaceWith($newItem);
                //1 save la liste avec le nouvel élément
                me.field.doSave(true,function(){
                    //2 rafraichit le template de l'item
                    $newItem.povRefresh(function($el){
                        //3 initialise l'item
                        me._initItem($el,true,files);

                    });
                });
                me.checkIfEmpty();

            }else{
                console.log("créer un block "+template+" pour chaque fichier");
                console.log("addBlocksFiles 2");
                me.addBlocksFiles(null,false,files,template);
                me.blockPicker.remove()
            }
        });

        if(this.keyEnterAction){

            /**
             * Permet de gérer ce qui se passe quand on presse la touche RETURN et que le focus de saisie est au sein de cette liste.
             *
             */
            $main.on("keypress","*:focus",
                /**
                 *
                 * @param {JQuery.Event} e
                 */
                function(e){
                if(e.which === 13 && !e.shiftKey) {
                    console.log('You pressed enter!',e);
                    e.preventDefault();
                    e.stopPropagation();
                    switch (me.keyEnterAction){
                        case "addItem":
                            me.addItem(me.templates[0],$(this).closest("[list-item-path]"),false);
                            return;
                        default:
                            console.error("keyEnterAction non gérée",me.keyEnterAction)
                    }
                }
            })
        }

        /**
         * Pour enregister le champ on passera par là
         * @type {WysiwygField}
         */
        this.field=new WysiwygField($main);

        Sortable.utils.on($main[0],"update",function(evt/**Event*/){
            //var item = evt.item; // the current dragged HTMLElement
            me.dispatchChange();
        });


        this._initItems();

    }

    /**
     * Génére un element html avec un nouveau item-key et tous les attributs qui vont bien
     * @param {string} templatePath Chemin vers le template
     * @returns {JQuery}
     * @private
     */
    _$getNewItem(templatePath){
        let block=new CqBlock(templatePath,this );
        return block.$main;
    }

    /**
     * Ajoute un template après l'item spécifié
     * @param {string} templatePath Chemin vers le template de l'item
     * @param {JQuery} $item
     * @param {boolean} insertBefore
     */
    addItem(templatePath,$item,insertBefore=false){

        let me=this;
        let $newItem=me._$getNewItem(templatePath);
        if($item && $item.length){
            if(insertBefore){
                $newItem.insertBefore($item);
            }else{
                $newItem.insertAfter($item);
            }

        }else{
            me.$main.append($newItem);
        }

        //1 save la liste avec le nouvel élément
        me.field.doSave(true,function(){
            //2 rafraichit le template de l'item
            $newItem.povRefresh(function($el){
                //3 initialise l'item
                me._initItem($el,true);

            });
        });
        me.checkIfEmpty();


    }

    /**
     *
     * @param {string} listItemKey
     * @returns {JQuery}
     */
    $itemByKey(listItemKey){
        return this.$items().filter("[list-item-key='"+listItemKey+"']")
    }


    /**
     * Ouvre la librairie de templates de choix ou injecte directement le template si il n'y a pas de choix.
     * @param {JQuery} $item
     * @param {boolean} insertBefore
     * @private
     */
    _startAddingItem($item,insertBefore=false){
        let me=this;

        /**
         * 3 possibilités:
         * -insère des records
         * -ouvre la liste de templates
         * -injecte directement un template (car il n'y a qu'une seule possibilité)
         */

        if(me.onlyRecords){
            me.addRecordsItems($item,insertBefore);
        }else{
            if(this.templates.length>1){
                if(insertBefore){
                    me.blockPicker.$main.insertBefore($item);
                }else{
                    me.blockPicker.$main.insertAfter($item);
                }
                me.blockPicker.initListeners();
            }else{
                this.addItem(this.templates[0],$item,insertBefore)
            }
        }
    }

    /**
     * Insère des blocks à partir de fichiers du poste client.
     * @param {JQuery|null} $item Le block qui sert de référence pour savoir où on insère les blocks qui seront créés
     * @param {boolean} insertBefore si true insèrera les nouveaux blocks avant $item
     * @param {File[]} files Les fichiers à uploader
     * @param {string} template Le template à utiliser pour insérer ces images (si non défini prend le premier).
     * @param {function} cb action à effectuer une fois que tout a été uploadé
     */
    addBlocksFiles($item, insertBefore=false, files=[], template="", cb=null){
        let me = this;
        /**
         *
         * @type {CqBlockFile[]}
         */
        let newBlocks=[];
        template=template?template:me.templates[0];

        //1 commence par créer les blocks sans rien (juste nouvel id, position dans la liste et template).
        for(let file of files){
            let block=new CqBlockFile(template,this,file);
            newBlocks.push(block);
            if($item && $item.length){
                if(insertBefore){
                    block.$main.insertBefore($item);
                }else{
                    block.$main.insertAfter($item);
                }
            }else{
                me.$main.append(block.$main);
            }
        }


        function uploadFilesAndSave(blocks) {
            return blocks.reduce(function(promise, block) {
                return promise.then(function() {
                    return  block.refresh() //premier refresh pour afficher le vrai block
                        .then(
                            function(){
                                console.log("2/ le block a été rafraichit...on va uploader");
                                return block.upload();
                            }
                        )
                        .then(
                            function(){
                                console.log("3/ upload ok");
                                return block.refresh(); //refresh pour afficher le block avec la photo uploadée
                            }
                        )
                        .then(
                            function(){
                                console.log("4/ refresh après upload ok");
                                //rafraichit les wysiwyg-on-saved-action-selector :\
                                let $toRefresh=block.$main.closest('[wysiwyg-on-saved-action="refresh"][wysiwyg-on-saved-action-selector]');
                                if($toRefresh.length){
                                    $toRefresh.evalHere($toRefresh.attr("wysiwyg-on-saved-action-selector")).povRefresh();
                                }
                            }
                        )
                });
            }, Promise.resolve());
        }

        //enregistre les nouveaux blocks et uploade les fichiers ensuite
        me.field.doSave(true,
            function(){
                uploadFilesAndSave(newBlocks)
                .then(function() {
                    if(cb){
                        cb();
                    }
                });
            }
        );

    }

    /**
     * Ouvre le record selector pour ensuite injecter des items qui représentent les records
     * @param {JQuery} $item
     * @param {boolean} insertBefore si false les elements seront ajoutés après l'item, sinon avant
     */
    addRecordsItems($item,insertBefore=false){
        let me=this;
        wysiwyg.recordSelector.open(me.popinXpos,me.popinYpos);
        wysiwyg.recordSelector.getUids(true,me.onlyRecordsTypes).then(
            function(uids){
                let $newItems=[];
                $.each(uids,function(k,uid){
                    let $newItem=me._$getNewItem(me.templates[0]);
                    $newItems.push($newItem);
                    $newItem.setMoreVars("targetUid",uid);
                    if($item && $item.length){
                        if(insertBefore){
                            $newItem.insertBefore($item);
                        }else{
                            $newItem.insertAfter($item);
                        }
                    }else{
                        me.$main.append($newItem);
                    }

                });
                me.blockPicker.$main.remove();

                //1 save la liste avec les nouveaux éléments
                me.field.doSave(true,function(){
                    //2 rafraichit les templates des items
                    $.each($newItems,function(k,$item){
                        $item.povRefresh(function($el){
                            //3 initialise l'item
                            me._initItem($el,true);

                        });
                    })
                });
                me.checkIfEmpty();
            }
        )
    }


    /**
     * Envoie un event pour dire que la liste a changé
     */
    dispatchChange(){
        Pov.events.dispatchDom(this.$main,Wysiwyg.events.CHANGED);
        this.checkIfEmpty();
    }



    /**
     * Initilise tous les items qui ne l'ont pas encore été
     * @private
     */
    _initItems(){
        let me=this;
        if(this.templatesLoaded){
            this.$items().each(function(){
                me._initItem($(this))
            });
            me.checkIfEmpty();
        }else{
            setTimeout(function(){
                me._initItems();
            },200);
        }


    }

    /**
     * En fonction de l'état de la liste remettra a jour certains parametres ergonomiques.
     */
    checkIfEmpty(){
        let me=this;
        if(this.$items().length===0){
            //la liste est vide
            if(me.templates.length>0){

                if(me.preventEmpty){
                    //insere automatiquement le primier template et basta
                    me._startAddingItem(null,false);
                }else{
                    //insere le block picker
                    me.$main.append(me.blockPicker.$main);
                    me.blockPicker.setMessage(me.blockPickerEmptyMessage);
                    me.blockPicker.initListeners();
                }
            }else{
                //si il n'y a pas de choix de template
                me.$main.on("mouseenter",function(){
                    wysiwyg.contextMenu.show().setAnchor(me.$main).btns.reset();
                    if(me.onlyImages){
                        //si on ne peut insérer que des images
                        wysiwyg.contextMenu.btns.plusUploadImages(
                            function(isBefore,files){
                                console.log("addBlocksFiles 1");
                                me.addBlocksFiles(null,isBefore,files,me.templates[0],function(){me.$main.povRefresh()});
                            }
                        );
                    }else{
                        //si il s'agit d'un template classique
                        wysiwyg.contextMenu.btns.plus(
                            function(e){
                                me._startAddingItem(null,false);
                            }
                        );
                    }
                });
            }
        }else{
            me.$main.off("mouseenter");
        }
    }



    /**
     *
     * @param {JQuery} $item
     * @param {boolean} isNewItem doit être true si l'item vient juste d'êtres créé
     * @param {File[]|null} files
     * @private
     */
    _initItem($item,isNewItem=false,files=null){
        let me=this;
        if(!me.templatesLoaded){
            console.error("Les templates ne sont pas encore chargés")
        }
        /**
         * L'uid de l'item
         * @type {string}
         */
        let itemUid=$item.attr("data-pov-vv-uid");
        if(!itemUid){
            console.error("oups pas d'itemUid",$item);
        }
        /**
         * Le template et ses parametres
         * @type {TemplateItem}
         */
        let template=me.blockPicker.templateItemByPath($item.attr("list-item-path"));
        if(!template){
            console.error("oups pas de template");
        }

        /**
         * Ouvre la fenêtre de config d'un item avec les bons paramètres
         */
        function openConfig(){
                wysiwyg.contextMenu.showConfig(template.config(),itemUid);
                wysiwyg.contextMenu.configBox.open(me.popinXpos,me.popinYpos);
        }

        // Ouvre la fenêtre de config automatiquement ...
        // ...si c'est nouvel item
        // ...et si besoin d'ouvrir la fenetre de config
        if(isNewItem===true){
            if($item.is("[wysiwyg-open-config='true']")){
                //config
                if(template.config()) {
                    openConfig();
                }
            }
        }
        //uploade des fichiers directement dans le block (le block est lui même un CqBlocks en fait)
        if(files){
            let $blocks=$item.find("[cq-blocks]");
            if($blocks.length !== 1){
                console.error("êtes vous certain que ce block soit lui-même une liste de blocks?",$item);
                return;
            }
            let blocks=$blocks.CqBlocks();
            console.log("addBlocksFiles 4");
            blocks.addBlocksFiles(null,true,files,null,function(){
                $item.povRefresh();
            });
            blocks.blockPicker.$main.remove();

        }
        //vérifie qu'il n'a pas été init déjà
        if($item.data("itemInit")){
            return;
        }else{
            $item.data("itemInit",true);
        }
        $item.on("mouseenter",function(e){
            e.stopPropagation();
            let $item=$(this);
            wysiwyg.contextMenu.show().setAnchor($item).btns.reset();
            wysiwyg.contextMenu.btns.trash(
                function(){
                    $item.remove();
                    me.dispatchChange();
                }
            );
            if(me.onlyImages){
                //ajout d'images
                wysiwyg.contextMenu.btns.plusUploadImages(
                    function(insertBefore,files){
                        console.log("addBlocksFiles 3");
                        me.addBlocksFiles($item,insertBefore,files,me.templates[0]);
                    }
                );
            }else{
                //ajout de records
                wysiwyg.contextMenu.btns.plus(
                    function(insertBefore){
                        me._startAddingItem($item,insertBefore);
                    }
                );
            }
            //avant / après
            if(!$(this).is(":first-child")){
                wysiwyg.contextMenu.btns.before(function(){
                    $item.insertBefore($item.prev());
                    me.dispatchChange();
                });
            }
            if(!$(this).is(":last-child")) {
                wysiwyg.contextMenu.btns.after(function(){
                    $item.insertAfter($item.next());
                    me.dispatchChange();
                });
            }
            //config ou pas?
            if(template.config()){
                wysiwyg.contextMenu.btns.cog(function(){
                    openConfig();
                })
            }



        }); //fin du  roll over

    }

    destroy(){
        super.destroy();
    }


}



/**
 * Pour invoquer un CqBlocks depuis son objet JQuery
 * @returns {CqBlocks}
 * @constructor
 */
$.fn.CqBlocks = function() {
    "use strict";
    if(!$(this).is("[cq-blocks='init']")){
        return new CqBlocks($(this));
    }else{
        return $(this).data("CqBlocks")
    }
};