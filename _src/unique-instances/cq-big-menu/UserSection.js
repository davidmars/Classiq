import CqBrowseRecordsList from "../../cq-browse-records/CqBrowseRecordsList";
import CqNewRecord from "../../cq-new-record/CqNewRecord";

export default class UserSection{
    /**
     *
     * @param {JQuery} $main
     * @param {CqBigMenu} bigMenu
     */
    constructor($main,bigMenu){

        let me=this;
        this.$main=$main;
        this.bigMenu=bigMenu;
        this.recordsList=new CqBrowseRecordsList($main.find("[cq-browse-records-list]"));
        this.recordsList.setTypes(["user"]);
        this.recordsList.on(EVENTS.CHANGED,function(){
            me.updateTips()
        });
        this.recordCreator=new CqNewRecord(this.$main.find("[cq-new-record]"));

        //recharge quand il y a une modification sur les utilisateurs
        window.povSSE.on(EVENTS.SSE_USER_CHANGE,
            /**
             * @param {PovSSEevent} e
             */
            function(e){
                me.recordsList.refreshList();
            }
        );



    }

    /**
     * Met à jour le compte des tips du panel user
     */
    updateTips(){
        //tips
        let me=this;
        setTimeout(function(){
            let count=0;
            let $tips=me.recordsList.$main.find("[cq-tip].cq-th-danger");
            $tips.each(function(){
                if($(this).attr("data-count")>0){
                   count++;
                }
            });
            me.bigMenu.panels.$sectionBtn("user").find("[cq-tip]").each(function(){
                $(this).CqTip().count=count;
            });
        },100)

    }

    /**
     * Appelé quand le panneau est actif
     * injecte la liste des user dans l'extension du big menu
     */
    active(){
        this.bigMenu.setExtensionContent(this.recordsList.$main);
    }
}