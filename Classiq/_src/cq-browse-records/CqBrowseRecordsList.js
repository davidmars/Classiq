import DisplayObject from "../DisplayObject";
import CqLoadingDots from "../cq-loading-dots/CqLoadingDots";
require("./cq-browse-records-list.less");

export default class CqBrowseRecordsList extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main);
        let me = this;
        this.xhr=null;
        this.keywords="";
        setTimeout(function(){
            window.cqAdmin.ui.recordEditor.on(EVENTS.OPEN,function(){
                me.highlightEdited();
            });
        },100);

        $body.on("input",".js-is-search",function(){
            let k=$(this).val();
            console.log("keywords",k);
            me.setKeywords(k);
        })
    }

    injected(){
        let me=this;
        setTimeout(function(){
            me.$main.find(".records").scroll(function(){
                me.$main.trigger("cq-scroll-event");
            });
        },100)

    }

    /**
     * Tous les records
     * @returns {JQuery}
     */
    $all(){
        return this.$main.find("[record-type]");
    }

    /**
     * Définit les types de records à afficher dans la liste et les charge
     * @param {string[]} types
     */
    setTypes(types){
        this.types=types;
        this.refreshList();
    }

    setKeywords(keywords){
        this.keywords=keywords;
        this.refreshList();
    }

    /**
     * Rafraichit la liste depuis le serveur avec les types courrants
     */
    refreshList(){
        let me = this;
        let $records=me.$main.find(".records");
        $records.empty().append(new CqLoadingDots().$main);
        if(me.xhr){
            me.xhr.abort();
        }
        me.xhr=window.pov.api.getView(
            "cq-browse-records/records",
            $records,
            {
                types:this.types.join(","),
                keywords:this.keywords,
                function(r){
                    me.xhr=null;
                    me.emit(EVENTS.CHANGED);
                    setTimeout(function(){
                        me.highlightEdited();
                    },100)

                }
            }
        );
    }
    highlightEdited(){
        let uid=window.cqAdmin.ui.recordEditor.currentUid;
        this.$all().removeClass("active");
        this.$all().filter("[data-pov-vv-uid='"+uid+"']").addClass("active");
    }
    destroy(){
        //nada
    }

}