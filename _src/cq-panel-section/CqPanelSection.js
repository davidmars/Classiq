import CqLocalStorage from "../CqLocalStorage";

require("./cq-panel-section.less");

import DisplayObject from "../DisplayObject";

/**
 * Permet de gérer des des panels (ou aurait pu dire tabs)
 */
export default class CqPanelSection extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     * @param {string} storageName nom du localstorage qui permettra d'enregistrer l'état et le réapliquer quand on recharge le navigateur.
     */
    constructor($main,storageName){
        super($main);
        let me=this;

        $main.on("click","[cq-panel-section-toggle]",function(e){
            e.preventDefault();
            me.show($(this).attr("cq-panel-section-toggle"));
        });
        /**
         *
         * @type {CqLocalStorage}
         * @private
         */
        this._storage=null;
        /**
         * la section active (utilisé uniquement si aucun local storage n'est défini)
         * @type {JQuery|*|string | undefined}
         * @private
         */
        me._tmpActiveName=me.$sections().filter(".section-active").attr("cq-panel-is-section");
        if(storageName){
            this._storage=new CqLocalStorage(storageName);
        }
        this.applyToUi();
    }

    /**
     * Pour obtenir une section par son nom
     * @param {string} sectionName
     * @returns {JQuery}
     */
    $section(sectionName){
        return this.$main.find("[cq-panel-is-section='"+sectionName+"']");
    }

    /**
     * Pour obtenir le(s) bouton(s) d'une section donnée
     * @param sectionName
     * @returns {JQuery|*}
     */
    $sectionBtn(sectionName){
        return this.$main.find("[cq-panel-section-toggle='"+sectionName+"']")
    }

    /**
     * Pour obtenir tous les boutons de section
     * @returns {JQuery|*}
     */
    $sectionBtns(){
        return this.$main.find("[cq-panel-section-toggle]")
    }

    /**
     * Pour obtenir toutes les sections
     * @returns {JQuery|*}
     */
    $sections(){
        return this.$main.find("[cq-panel-is-section]")
    }

    /**
     * Pour afficher une section (et donc masquer les autres)
     * @param {string} sectionName
     */
    show(sectionName){
        this._setActiveName(sectionName);
        let $toActive=this.$section(sectionName);
        if(!$toActive.hasClass("section-active")){
            $toActive.addClass("section-active");
            $toActive.parent().scrollTop(0);
        }
        this.hideAll();
        $toActive.addClass("section-active");
        this.$sectionBtn(sectionName).addClass("section-active");
        if(this._storage){
            this._storage.setValue("active",sectionName);
        }
        this.emit(EVENTS.SELECT,sectionName);
    }

    /**
     * Pour masquer toutes les sections
     */
    hideAll() {
        this.$sections().removeClass("section-active");
        this.$sectionBtns().removeClass("section-active");
        if(this._storage){
            this._storage.setValue("active",null);
        }
    }

    /**
     * Affiche la section qui a été précédemment définie comme active.
     */
    applyToUi(){
        this.show(this.getActiveName());
    }

    /**
     * Retourne le nom de la section active
     * @returns {string}
     */
    getActiveName(){
        if(this._storage){
            return this._storage.getValue("active");
        }else{
            return this._tmpActiveName;
        }
    }

    /**
     * Définit le nom de la section active.
     * @param name
     * @private
     */
    _setActiveName(name){
        if(this.storage){
            this._storage.setValue("active",name);
        }else{
            this._tmpActiveName=name;
        }
    }

    destroy(){
        //on s'en branle
    }


}