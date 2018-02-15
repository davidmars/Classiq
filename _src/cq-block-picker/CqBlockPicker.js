import DisplayObject from "../DisplayObject";

require("./cq-block-picker.less");
require("../cq-ico-txt/cq-ico-txt.less");
/**
 * Au click sur un des boutons emet un EVENT.SELECT avec comme paramètre le template path relatif
 */
export default class CqBlockPicker extends DisplayObject{

    /**
     *
     * @param {JQuery} $main
     * @param {string} message
     * @param {string[]} templates liste des templates à afficher
     */
    constructor($main,message="Choisissez",templates){
        //crée à partir du template
        if(!$main){
            var template = require('./cq-block-picker.mst');
            var html = template(
                {
                    message: window.pov.utils.decodeHtml(message),
                }
            );
            $main=$(html);
        }
        super($main);
        let me=this;
        /**
         *
         * @type {JQuery}
         */
        this.$buttons = $main.find(".buttons-container");
        /**
         *
         * @type {JQuery}
         */
        this.$closeBtn = $main.find(".close");
        /**
         *
         * @type {JQuery}
         */
        this.$message = $main.find(".message");
        /**
         *
         * @type {Array}
         * @private
         */
        this._templateItemByPath=[];
        /**
         *
         */
        this.loadTemplates(templates,function(){
            for(let template of templates){
                let $btn=CqBlockPicker.templatesLoaded[template];
                me.$buttons.append($btn.clone());
            }

        });

        this.initListeners();


    }
    /**
     * Définit le message à afficher
     * @param message
     */
    setMessage(message=""){
        this.$message.text(message);
    }
    /**
     * initialise les listeners sur les clicks
     */
    initListeners(){
        let me=this;
        me.$closeBtn.on("click",function(e){
            e.preventDefault();
            me.remove();
        });
    }

    remove(){
        this.$main.remove();
    }





    destroy(){
        //evite de suprimer l'objet même s'il n'est pas dans le dom
        //todo opti penser à le supprimer un jour quand même
        return true;
    }

    /**
     * Charge (en ajax) les templates demandés
     * @param {String[]} templates Liste des templates à charger
     * @param {function} cb callback quand tous les templates sont chargés
     */
    loadTemplates(templates,cb){

        let me=this;
        let loading=templates.length;

        function isFinished(){
            if(loading===0){
                if(cb){
                    cb();
                }
            }
        }

        for(let i=0;i<templates.length;i++){
            let template=templates[i];
            let $tmp=$("<div></div>");
            if(CqBlockPicker.templatesLoaded[template]){
                loading--;
            }else{
                //todo important charger les templates tous d'un coup
                //console.warn("Le template "+template+" va être chargé");
                window.pov.api.getView("cq-block-picker/block-picker-loader",$tmp,{"vv":templates[i]},function($btn){
                    loading--;
                    CqBlockPicker.templatesLoaded[template]=$btn;
                    isFinished();
                });
            }
        }
        //peut être que tout était déjà chargé
        isFinished();
    }

    /**
     * Retourne un élément de la bibliothèque depuis son path
     * @param path
     * @returns {TemplateItem}
     */
    templateItemByPath(path){
        if(!this._templateItemByPath[path]){
            this._templateItemByPath[path] = new TemplateItem(
                this.$main.find("[path='"+path+"']")
            );
        }
        return this._templateItemByPath[path]
    }

}

CqBlockPicker.templatesLoaded={};

class TemplateItem{
    /**
     *
     * @param {JQuery} $item
     */
    constructor($item){
        /**
         *
         * @type {JQuery}
         */
        this.$item=$item;
        /**
         * @type {String} Chemin vers le template
         */
        this.path=$item.attr("path");

    }

    /**
     *
     * @returns {string} Chemin vers le template de config
     */
    config(){
        return this.$item.attr("config");
    }
}