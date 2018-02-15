import DisplayObject from "../../DisplayObject";

require("./cq-edit-record-box.less");
require("../../cq-edit-record-form/cq-edit-record-form.less");

export default class CqEditRecordBox extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main);
        let me=this;
        this.$container=this.$main.find(">main>.target");
        /**
         * l'uid qui est actuallement édité
         * @type {string}
         */
        this.currentUid="";
        new PerfectScrollbar(this.$main.find(">main").get(0),
            {
                wheelSpeed: 1,
                wheelPropagation: true,
                minScrollbarLength: 20,
                suppressScrollX:true,
                swipeEasing:true
            }
        );

        //si le record est supprimé on ferme
        this.$main.on(EVENTS.SSE_DB_TRASH,function(e){
            //todo attention si un record au sein de la page dedition est supprimé (genre une photo) ça va pas targetter le bon...à voir
            me.hide();
        });
        //si le record est modifié
        this.$main.on(EVENTS.SSE_DB_CHANGE,function(e){
            $(e.target).povRefresh();
        });

    }

    /**
     * Charge l'uid dans cette instance
     * @param uid
     */
    displayUid(uid){
        CqEditRecordBox.loadUid(
            uid,
            this.$container
        );
        this.show();
        this.currentUid=uid;
        this.emit(EVENTS.OPEN,[uid]);
    }

    /**
     * Charge le formulaire d'édition dans le conteneur spécifié
     * @param uid
     * @param {JQuery} $target
     * @returns {Promise<any>}
     */
    static loadUid(uid,$target){
        return new Promise(function(resolve,reject){
            $target.empty();
            let $record=$("<div></div>");
            $target.append($record);
            let url=LayoutVars.rootUrl+"/classiq/editUid/"+uid;
            window.pov.api.getView(
                "unique-instances/cq-edit-record-box/record-content",
                $record,
                {uid:uid},
                function(r){
                    resolve(r);
                }
            );
        })

    }


    /**
     * Affiche le volet d'édition
     */
    show(){
        this.$main.addClass("open");
    }

    /**
     * Masque le volet d'édition
     */
    hide(){
        this.$main.removeClass("open");
    }
}