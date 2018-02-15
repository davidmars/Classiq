import DisplayObject from "../../DisplayObject";
import {CqNotification} from "../../cq-notification/CqNotification";
require("./cq-notifier.less");


export default class CqNotifier extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main);
        let me=this;
        window.povSSE.on(EVENTS.SSE_INFO,function(e){
            if(e.humanMessage){
                me.notify(
                    e.humanMessage,5,"white"
                );
            }
        });
        $main.on("mouseenter",function(){
            me.pauseAll();
        });
        $main.on("mouseleave",function(){
            me.resumeAll();
        });
        Pov.events.on(EVENTS.XDEBUG_DETECTED,
            /**
             *
             * @param {window.pov.Xdebug} xdebug
             */
            function(xdebug){
                me.notify(xdebug.firstLine,5,"danger","cq-bug-report");
            }
         )
    }

    $allNotifications(){
        return this.$main.find("[cq-notification]");
    }

    pauseAll(){
        this.$allNotifications().each(function(){
            $(this).CqNotification().pause();
        })
    }
    resumeAll(){
        this.$allNotifications().each(function(){
            $(this).CqNotification().resume();
        })
    }


    /**
     * Affiche une notification
     * @param {string} message
     * @param {int} duration durée d'affichage en secondes
     * @param {string} theme white|black|danger tout ce qui est prévu par la css #the-cq-layer .cq-th-etc..
     * @param {string} svgIcon identifiant d'icône SVG à afficher
     * @param {string} uid
     */
    notify(message="",duration=4,theme="white",svgIcon,uid){
        if(STAGE.visible()){
            let me=this;
            if(uid){
                this.$notificationByUid(uid).remove();
            }
            //console.log("notify",message);
            let notification=new CqNotification(null,message,theme,duration,svgIcon,uid);
            this.$main.prepend(notification.$main);
            TweenMax.from(notification.$main,0.2,{scale:0});

        }else{
            //on affiche rien si l'écran est désactivé
        }
    }

    $notificationByUid(uid){
        return this.$main.find("[cq-notification-about-uid='"+uid+"']");
    }

    destroy(){

    }
}