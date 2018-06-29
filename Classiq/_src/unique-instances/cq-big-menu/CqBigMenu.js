import WysiwygCreateUi from "../../cq-new-record/CqNewRecord";
import CqPanelSection from "../../cq-panel-section/CqPanelSection";
import CqTip from "../../cq-tip/CqTip";
import UserSection from "./UserSection";
import CqBrowseRecords from "../../cq-browse-records/CqBrowseRecords";
import CqEditRecordBox from "../cq-edit-record-box/CqEditRecordBox";
import CqDisplayControl from "../../cq-display-if/CqDisplayControl";
import DisplayObject from "../../DisplayObject";
import CqLoadingDots from "../../cq-loading-dots/CqLoadingDots";
require("../../cq-base/cq-cols.less");


export default class CqBigMenu{

    /**
     * 
     * @param {JQuery} $main
     */
    constructor($main){
        let me=this;
        this.$main=$main;
        /**
         * Le volet d'extension (utilisé que par browse pour le moment)
         * @type {JQuery|*}
         */
        this.$extension=this.$main.find(">.extension");
        /**
         * Là où sont chargés les éléments propres à la page en cours
         * @type {JQuery|*}
         */
        this.$pageSection=this.$main.find("[cq-panel-is-section='page']");
        /**
         * La boite où on saisit le nom et le type d'une nouvelle page
         * @type {JQuery|*}
         */
        this.$createSection=this.$main.find("[cq-panel-is-section='create']");
        /**
         * La boite où on saisit le nom et le type d'une nouvelle page
         * @type {JQuery|*}
         */
        this.$browseSection=this.$main.find("[cq-panel-is-section='browse']");

        //gère les boutons / tabs du big menu
        /**
         *
         * @type {CqPanelSection}
         */
        this.panels=new CqPanelSection($main,"bigMenuMainSections");
        if(this.panels.getActiveName()){
            this.open(this.panels.getActiveName(),true);
        }
        this.panels.on(EVENTS.SELECT,function(sectionName){
            switch (sectionName){
                case "browse":
                    me.browse.active();
                    me.showExtension();
                    break;
                case "user":
                    me.usersSection.active()
                    me.showExtension();
                    break;
                default:
                    me.hideExtension();
                    window.wysiwyg.recordEditor.hide();
            }
        });

        /*
        new PerfectScrollbar($main.find("main").get(0),
            {
                wheelSpeed: 1,
                wheelPropagation: true,
                minScrollbarLength: 20,
                suppressScrollX:true,
                swipeEasing:true
            }
        );
        */

        this.usersSection=new UserSection(me.$userSection(),me);
        this.browse= new CqBrowseRecords(this.$browseSection,this);
        new WysiwygCreateUi(this.$createSection);
        DisplayObject.__fromDom(CqDisplayControl,"cq-display-control");
        me.injectPageModules();
        this.initListeners();
    }


    initListeners(){
        let me=this;
        //met à jour la section page quand un champ a été enregistré
        this.$pageSection.on(EVENTS.SSE_DB_CHANGE,function(){
            me.refreshPageSection();
        });
        this.$pageSection.on(Pov.events.DOM_CHANGE,function(){
            me.manageErrorsAndTips();
        });
        //quand on quitte la page en cours
        $body.on(EVENTS.HISTORY_CHANGE_URL_LOADING,function(){
            //enlève les éléments propres à la page en cours
            me.clearPageModule();
        });
        //quand on vient de charger une nouvelle page
        $body.on(EVENTS.HISTORY_CHANGE_URL_LOADED_INJECTED,function(){
            //injecte ou met à jour les éléments de la page en cours
            me.injectPageModules();
        });
        //...ou tout simplement quand on charge le site
        $body.on(Pov.events.READY,function(){
            //injecte ou met à jour les éléments de la page en cours
            me.injectPageModules();
        });
    }

    /**
     *
     * @returns {JQuery|*}
     */
    $userSection(){
        return this.$main.find("[cq-panel-is-section='user']");
    }

    manageErrorsAndTips(){
        let me=this;

        //page section
        me.$pageSection.find(".js-tip-injected").remove();
        let $errors=me.$pageSection.find("[wysiwyg-field-error]");
        $errors.each(function(){
            let tip=new CqTip(null,"danger",1,$(this).attr("wysiwyg-field-error"));
            tip.$main.addClass("js-tip-injected");
            $(this).closest("fieldset").addClass("has-cq-tip").append(tip.$main);
        });

        me.panels.$sectionBtn("page").find("[cq-tip]").each(function(){
            $(this).CqTip().count=$errors.length;
        });
    }

    /**
     * L'élément page section que peut être rechargé
     * @returns {JQuery}
     */
    $pageSectionRefreshable(){
        return this.$pageSection.find("[data-pov-v-path]").first();
    }

    /**
     * Rafraichit l'admin de la page en cours
     */
    refreshPageSection(){
        this.$pageSectionRefreshable().povRefresh();
    }
    /**
     * ouvre ou ferme le menu
     */
    toggle(){
        console.log("toggle menu")
        this.$main.toggleClass("open");
        if(!this.$main.hasClass("open")){
            this.close();
        }else{
            this.open(false)
        }
    }
    /**
     * Ouvre le menu
     * @param {String} sectionId page-section|create-section|config-section|user-section
     * @param {bool} preventTransition
     */
    open(sectionId="page",preventTransition=false){
        let me=this;
        console.log("open menu")
        if(window.wysiwyg.contextMenu){
            window.wysiwyg.contextMenu.setAnchor(null)
        }
        if(preventTransition){
            this.$main.addClass("cq-no-transition");
            setTimeout(function(){
                me.$main.removeClass("cq-no-transition");
            },1000);
        }
        this.$main.addClass("open");
        if(sectionId){
            this.panels.show(sectionId)
        }
    }
    /**
     * Ferme le menu
     */
    close(){
        console.log("close menu");
        this.$main.removeClass("open");
        this.panels.hideAll();
        this.hideExtension();
        wysiwyg.recordEditor.hide();
    }



    /**
     * Injecte le formulaire d'admin de la page en cours
     */
    injectPageModules(){
        console.log("inject page module",PovHistory.currentPageInfo.uid);
        let me=this;
        CqEditRecordBox.loadUid(PovHistory.currentPageInfo.uid,me.$pageSection).then(
            function(){
                me.manageErrorsAndTips();
            }
        );

    }
    clearPageModule(){
        let me=this;
        me.$pageSection.empty().append(new CqLoadingDots().$main);
    }

    /**
     * Affiche le volet d'extension
     */
    showExtension(){
        this.$extension.addClass("open");
    }
    /**
     * Masque le volet d'extension
     */
    hideExtension(){
        this.$extension.removeClass("open");
    }

    /**
     * Définit ce qu'on affiche dans l'extension
     * @param $content
     */
    setExtensionContent($content){
        this.$extension.empty();
        this.$extension.append($content);
        let displayObject=$content.data("displayObject");
        if(displayObject){
            displayObject.injected();
        }

    }

    
}