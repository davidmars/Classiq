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
        new PerfectScrollbar($main.find(".records").get(0),
            {
                wheelSpeed: 1,
                wheelPropagation: true,
                minScrollbarLength: 20,
                suppressScrollX:true,
                swipeEasing:true
            }
        );
        setTimeout(function(){
            window.cqAdmin.ui.recordEditor.on(EVENTS.OPEN,function(){
                me.highlightEdited();
            });
        },100);


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
        this.refreshList(types);
    }

    /**
     * Rafraichit la liste depuis le serveur avec les types courrants
     */
    refreshList(){
        let me = this;
        let $records=me.$main.find(".records");
        $records.empty().append(new CqLoadingDots().$main);
        window.pov.api.getView(
            "cq-browse-records/records",
            $records,
            {
                types:this.types.join(","),
                function(r){
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