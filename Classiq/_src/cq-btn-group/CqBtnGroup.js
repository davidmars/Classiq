import DisplayObject from "../DisplayObject";
require("./cq-btn-group.less");

export default class CqBtnGroup extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){

        if(!$main){
            $main=$("<div cq-btn-group size='normal'></div>")
        }
        
        super($main,"CqBtnGroup");
        let me=this;

        let preventRecursion=false;


        function checkVisible(){
            preventRecursion=true; //évite que les modifications de la classe ne fassent une récursion
            $main.removeClass("empty");
            let $all=me.$main.find(">*:visible");
            if($all.length===0){
                $main.addClass("empty");
            }
        }
        function checkFirstLast(){
            let $all=me.$main.find(">*:visible");
            //met des classes first et last sur les premiers et derniers boutons visibles
            $all.removeClass("first");
            $all.removeClass("last");
            $all.filter(":first").addClass("first");
            $all.filter(":last").addClass("last");

        }

        var observer = new MutationObserver(function(mutations) {
            if(!preventRecursion){
                checkVisible();
                setTimeout(function(){
                    preventRecursion=false;
                    checkFirstLast();
                },1);
            }


        });
        observer.observe($main[0], { attributes: true});

    }

}

/**
 * Pour invoquer un CqBtnGroup depuis son objet JQuery
 * @returns {CqBtnGroup}
 * @constructor
 */
$.fn.CqBtnGroup = function() {
    "use strict";
    if(!$(this).is("[cq-btn-group='init']")){
        return new CqBtnGroup($(this));
    }else{
        return $(this).data("CqBtnGroup")
    }
};