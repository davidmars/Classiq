import EventEmitter from "event-emitter-es6";
require("./cq-block.less")

/**
 * Un CqBlock est un block temporaire avant que le vrai block généré par php n'apparaisse.
 */
export default class CqBlock extends EventEmitter{

    /**
     *
     * @param {string} templatePath Chemin du template
     * @param {CqBlocks} parentBlocksList
     */
    constructor(templatePath,parentBlocksList){
        super();
        /**
         *
         * @type {JQuery}
         */
        this.$main=$("<div cq-block>...</div>");
        /**
         * La clé du block (un identifiant généré automatiquement aui ressemble à itemKey123456789)
         * @type {string}
         */
        this.itemKey="key_"+new Date().getTime()+"_"+Math.round(Math.random()*1000000);
        /**
         * Identifiant unique du block
         * @type {string} ressemble à recordType-recordUid.recordVar.itemKey123456789
         */
        this.uid=parentBlocksList.field.type+"-"+parentBlocksList.field.id+"."+parentBlocksList.field.var+"."+this.itemKey;
        /**
         * Le champ blocks qui a généré (est dans lequel est) ce block
         * @type {CqBlocks}
         */
        this.blocks=parentBlocksList;
        //pour enregistrer
        this.$main.attr("list-item-key",this.itemKey);
        this.$main.attr("list-item-path",templatePath);
        //pour le refresh
        this.$main.attr("data-pov-vv-uid",this.uid);
        this.$main.attr("data-pov-v-path",templatePath);

    }

    /**
     * Rafraichit le template
     * @returns {Promise<any>}
     */
    refresh(){
        let me=this;
        return new Promise(
            function(resolve,reject){
                me.$main.povRefresh(
                    function($newMain){
                        me.$main=$newMain;
                        resolve();
                    }
                );
            }
        );
    }


    /**
     * Définit et enregistre la variable targetUid dans le block.
     * La variable targetUid est une variable spéciale et fréquemment utilisée dans les blocks qui permet de relier le block à un record (une image, une autre page, etc...)
     * @param {string} targetUid uid du record à associer à ce block
     * @returns {Promise<any>}
     */
    setTargetUid(targetUid){
        let me =this;
        return new Promise(function(resolve,reject){
            let saveObj={};
            saveObj.modelType=me.blocks.field.type;
            saveObj.modelId=me.blocks.field.id;
            saveObj.modelVars={};
            saveObj.modelVars[me.uid+".targetUid"]=targetUid;
            //me.$main.text(targetUid);
            window.pov.api.save(saveObj,function(){
                resolve();
            });
        });

    }

}