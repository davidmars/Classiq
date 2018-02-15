import CqDb from "./cq-db/CqDb";

export default class CqAdmin{
    constructor(){


        let me=this;
        /**
         * Pour parler avec la base de données
         * @type {CqDb}
         */
        this.db=new CqDb();
        /**
         * Permet d'accéder à des élément d'interface d'admin courrants
         */
        this.ui={
            bigMenu:wysiwyg.bigMenu,
            recordEditor:wysiwyg.recordEditor
        };

        //pour que les cq-on-etc puissent appeler des methodes de cet objet
        window.cqEventsListener.addObject(this);
        window.cqEventsListener.addListener(EVENTS.SSE_DB_CHANGE,"cq-on-model-saved");
        window.cqEventsListener.addListener(EVENTS.SSE_DB_TRASH,"cq-on-model-trash"); //todo opti pas utilisé mais devrait l'être ;)


        /**
         * Pour afficher facilement des notifications
         * @type {{apiResponse: CqAdmin.notify.apiResponse}}
         */
        this.notify={
            /**
             * Affiche (ou pas) une notification appropriée en fonction de ApiResponse
             * @param {ApiResponse} apiResponse
             */
            apiResponse:function(apiResponse){
                let theme="white";
                let icon="cq-circle-info";
                let content="";
                if(!apiResponse.success){
                    theme="danger";
                    icon="cq-circle-error";
                    content=apiResponse.errors.join("<br>");
                }
                if(content){
                    wysiwyg.notifier.notify(content,10,theme,icon);
                }
            }
        };

        this._initSSEEvents();
    }

    editRecord(uid){
        this.ui.recordEditor.displayUid(uid);
    }

    _initSSEEvents(){

        /**
         * Quand une élément de la DB est modifié
         */
        window.povSSE.on(EVENTS.SSE_DB_CHANGE,
            /**
             * @param {PovSSEevent} sseEvent
             * - notification
             * - renvoie l'event sur les éléments modifiés
             */
            function(sseEvent){
                window.wysiwyg.notifier.notify(sseEvent.humanMessage,3,"white","cq-edit",sseEvent.vars.uid);
                if(sseEvent.vars.uid){
                    let $relatedEls=$("[data-pov-vv-uid='"+sseEvent.vars.uid+"']");
                    $relatedEls.trigger(EVENTS.SSE_DB_CHANGE);
                }
            }
        );
        /**
         * Quand on met un element de la DB à la poubelle
         * - notification
         * - renvoie l'event sur les éléments supprimés
         */
        window.povSSE.on(EVENTS.SSE_DB_TRASH,
            /**
             * @param {PovSSEevent} sseEvent
             */
            function(sseEvent){
                window.wysiwyg.notifier.notify(sseEvent.humanMessage,4,"danger","cq-trash");
                if(sseEvent.vars.uid){
                    let $relatedEls=$("[data-pov-vv-uid='"+sseEvent.vars.uid+"']");
                    $relatedEls.css("opacity","0.2");
                    $relatedEls.trigger(EVENTS.SSE_DB_TRASH);
                    if(PovHistory.currentPageInfo.uid){
                        if(PovHistory.currentPageInfo.uid===sseEvent.vars.uid){
                            PovHistory.goToHomePage();
                        }
                    }
                }
            }
        );
    }


    /**
     * Fait un pov refresh de l'objet qui appelle la fonction
     */
    refresh(){
        $(this).povRefresh();
    }
}