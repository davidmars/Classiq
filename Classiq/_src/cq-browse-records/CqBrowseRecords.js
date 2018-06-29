import DisplayObject from "../DisplayObject";
import CqLocalStorage from "../CqLocalStorage";
import CqBrowseRecordsList from "./CqBrowseRecordsList";
require("./cq-browse-records.less");

export default class BrowseRecords extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     * @param {CqBigMenu} bigMenu
     */
    constructor($main,bigMenu){
        super($main);
        let me = this;

        /**
         *
         * @type {CqBigMenu}
         */
        this.bigMenu=bigMenu;

        /**
         *
         * Conserve ce qui est coché ou non
         * @type {CqLocalStorage}
         */
        this.storage=new CqLocalStorage("browseRecords");
        /**
         *
         * @type {string[]} Types selectionnés
         */
        this.typesSelected=[];

        //reloade la liste qd les records changement
        povSSE.on(EVENTS.SSE_DB_COUNT_CHANGE,function(){
            me.refreshContent();
        });

        /**
         *
         * @type {JQuery}  l'objet DOM de la liste
         */
        this.$list=null;
        /**
         * L'objet de liste de records affichés
         * @type {CqBrowseRecordsList}
         */
        this.recordsList=null;

        this.__init($main);



    }

    __init($main){
        let me=this;
        //eléments jquery
        this.$main=$main;
        this.$list=this.$main.find("[cq-browse-records-list]");
        //objets qui en découlent
        this.recordsList=new CqBrowseRecordsList(this.$list);

        //listeners de clicks
        this.$types().on("change",function(){
            let $selecteds=me.$types().filter(":checked");
            let selectedTypes=[];
            $selecteds.each(function(){
                selectedTypes.push($(this).val());
            });
            me.setTypes(selectedTypes);
        });

        //applique l'état courrant à partir du storage
        if(me.storage.getValue("typesSelected")){
            let selectedTypes=[];
            for(let type of me.storage.getValue("typesSelected")){
                //coche le record type
                this.$types().filter("[value='"+type+"']").prop("checked", true);
                selectedTypes.push(type);
            }
            this.setTypes(selectedTypes);
        }



    }


    refreshContent(){
        let me=this;
        me.$main.povRefresh(function($newMain){
            me.__init($newMain);
            me.bigMenu.panels.applyToUi(); //réactivera l'onglet s'il faut
        });
    }

    /**
     * Tous les chackboes de types
     * @returns {JQuery|*}
     */
    $types(){
        return this.$main.find(".js-is-record-type");
    }

    setTypes(types){
        this.typesSelected=window.pov.utils.arrayUnique(types);
        this.storage.setValue("typesSelected",this.typesSelected);
        this.updateUi();
    }

    updateUi(){
        let me=this;
        //tampon pour eviter de fair l'appel trop souvent
        if(this.timeOutUpdate){
            clearTimeout(this.timeOutUpdate);
        }
        this.timeOutUpdate=setTimeout(function(){
            me.recordsList.setTypes(me.typesSelected);
        },10)
    }
    /**
     * Appelé quand le panneau est actif
     * injecte la liste des records dans l'extension du big menu
     */
    active(){
        this.bigMenu.setExtensionContent(this.$list);
    }


}