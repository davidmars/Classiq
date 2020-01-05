import DisplayObject from "../DisplayObject";
import Sortable from 'sortablejs/modular/sortable.complete.esm.js';
require("./cq-sortable.less");

export default class CqSortable extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main,CLASS_NAME="CqSortable"){

        super($main,CLASS_NAME);
        let me=this;

        /**
         *
         * @type {Sortable}
         * @see http://rubaxa.github.io/Sortable/
         */
        this.sortable=null;

        if($main[0]){
            me.sortable = Sortable.create($main[0], {
                group:{
                    name:"receiver",
                    //put:true,
                    put:["lib"]
                },
                cancel:null,
                animation: 100, // ms, animation speed moving items when sorting, `0` — without animation
            });
        }else{
            console.error("sortable vide !!!");
        }

        Sortable.utils.on($main[0],"sort",function(evt/**Event*/){
            console.log("change sortable");
            $main.trigger("change");
        });

    }

    /**
     * Les éléments de la liste
     * @returns {JQuery}
     */
    $items(){
        return this.$main.children();
    }


    destroy(){
        this.sortable.destroy();
    }
}

/**
 * Pour invoquer un CqSortable depuis son objet JQuery
 * @returns {CqSortable}
 * @constructor
 */
$.fn.CqSortable = function() {
    "use strict";
    if(!$(this).is("[cq-sortable='init']")){
        return new CqSortable($(this));
    }else{
        return $(this).data("CqSortable")
    }
};