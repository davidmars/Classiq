import DisplayObject from "../DisplayObject";

export default class CqTip extends DisplayObject{
    constructor($main,_theme="danger",_count="1",_message=""){

        //crée à partir du template
        if(!$main){
            var template = require('./cq-tip.mst');
            var html = template(
                {
                    theme: _theme,
                    count: _count,
                    message: _message,
                }
            );
            $main=$(html);
        }
        super($main,"CqTip");

        /**
         *
         * @type {string}
         * @private
         */
        this._count=_count;
        /**
         *
         * @type {string}
         * @private
         */
        this._theme=_theme;

    }

    set count(val){
        this._count=val;
        this.$main.attr('data-count',this._count);
    }
    get count(){
        return this._count;
    }

    set theme(val){
        this._theme=val;
        this.$main.removeClassPrefix("th-");
        this.$main.addClass("th-"+this.theme);
    }
    get theme(){
        return this._theme;
    }

    destroy(){}
}

/**
 * Pour invoquer un CqTip depuis son objet JQuery
 * @returns {CqTip}
 * @constructor
 */
$.fn.CqTip = function() {
    "use strict";
    if(!$(this).is("[cq-tip='init']")){
        return new CqTip($(this));
    }else{
        return $(this).data("CqTip")
    }
};