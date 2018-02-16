import EventEmitter from "event-emitter-es6";

const kebabCase = string => string.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase();
/**
 * Un élément jquery en fin de compte :)
 */
export default class DisplayObject extends EventEmitter{

    /**
     *
     * @param {JQuery} $main
     */
    constructor($main=undefined){
        super();
        /**
         *
         * @type {JQuery}
         */
        this.$main=$main;



        let getClassName = obj => {
            if (obj.constructor.name) {
                return obj.constructor.name;
            }
            const regex = new RegExp(/^\s*function\s*(\S*)\s*\(/);
            getClassName = obj => obj.constructor.toString().match(regex)[1];
            return getClassName(obj);
        };
        /**
         * Nom de la classe utilisée
         * @type {string}
         */
        this.CLASS_NAME=getClassName(this);

        /**
         * Pour un composant "MonComposantToto" sera "mon-composant-toto"
         */
        this.ATTR=kebabCase(this.CLASS_NAME);


        if($main!==undefined && $main.length<1){
            console.error("$main DisplayObject introuvable "+this.CLASS_NAME+" / "+this.ATTR,$main);
            return false;
        }
        DisplayObject.allInstances.push(this);

        if(this.$main.data(this.CLASS_NAME)){
            console.error(this.CLASS_NAME+" déjà construit!!!",$main);
            return this.$main.data(this.CLASS_NAME);
        }

        this.$main.data(this.CLASS_NAME,this);
        this.$main.attr(this.ATTR,"init");



    }

    /**
     * Initialise les displays objects de la classe fournie en argument trouvés dans le DOM.
     * Les $elements doivent avoir l'attribut qui correspond à leur classe.
     *
     * @param {class} classInstance MaClasseDisplayObject La classe pour construit l'objet
     * @param {string} attrSelector ma-classe-display-object attribut utilisé pour détecter dans le Dom ces objets
     * @private
     */
    static __fromDom(classInstance,attrSelector){
        //console.log("Display Object From DOM ",classInstance,attrSelector)
        //let ATTR=kebabCase(classInstance.name);

        $("["+attrSelector+"]").not("["+attrSelector+"='init']").each(function(){
            //console.log("from dom",classInstance.name,ATTR);
            new classInstance($(this));
        });
    }


    /**
     * La largeur de $main
     * @returns {number}
     */
    width(){
        return this.$main.width();
    }

    /**
     * La hauteur de $main
     * @returns {number}
     */
    height(){
        return this.$main.height();
    }

    /**
     * Pour savoir si l'objet est toujoujours dans le dom
     * @returns {boolean}
     */
    isInDom(){
       return this.$main.closest("html").length>0;
    }


    destroy(){
        console.warn("destroy not implemented for "+this.constructor.name);
    }

    /**
     * Nettoie tous les DisplayObject(s) qui ne sont plus dans le DOM.
     * Fait appel à la méthode destroy() qui du coup doit être correctement implementée pour chaque DisplayObjetct
     */
    static cleanfromDom(){
        $.each(DisplayObject.allInstances,
            /**
             *
             * @param k
             * @param {CqFieldRichText} v
             */
            function(k,v){
                //console.log("CqFieldRichText",v);
                if(!v.isInDom()){
                    let keep=v.destroy();
                    if(!keep){ //si destroy retourne un true alors on conserve l'objet même s'il n'est pas présent dans le dow
                        DisplayObject.allInstances[k]=null;
                    }

                }
            }
        );
        //efface les entrées nulles
        let clean=[];
        $.each(DisplayObject.allInstances,
            function(k,v){
                if(v !== null){
                    clean.push(v);
                }
            }
        );
        DisplayObject.allInstances=clean;
    }

    addTempClass(cssClass,milliseconds){
        let me=this;
        me.$main.addClass(cssClass);
        setTimeout(function(){
            me.$main.removeClass(cssClass);
        },milliseconds);
    }



}


/**
 *
 * @type {DisplayObject[]}
 */
DisplayObject.allInstances=[];

(
    /**
     * @param {JQuery} $
     */
    function ($) {
        /**
         * Renvoie le display object de l'élément
         * @returns {DisplayObject}
         */
        $.fn.displayObject = function() {
            "use strict";
            if($(this).length>1){
                console.error("displayObject multiple",$(this))
                return;
            }
            if($(this).data("displayobject")){
                return $(this).data("displayobject");
            }else{
                console.error("pas de displayObject ici",$(this))
                return null;
            }
        };

})(jQuery);