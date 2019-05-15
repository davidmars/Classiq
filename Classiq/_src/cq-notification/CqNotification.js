import DisplayObject from "../DisplayObject.js";
import CqProgressBar from "../cq-progress-bar/CqProgressBar";
import CqTip from "../cq-tip/CqTip";

require("./cq-notification.less");

export class CqNotification extends DisplayObject{

    constructor($main,message="...",theme="black",duration=5,svgIcon="cq-circle-filled-info",uid=""){

        //duration=10000;

        //crée à partir du template
        if(!$main){
            var template = require('./cq-notification.mst');
            var html = template(
                {
                    theme: theme,
                    message: window.pov.utils.decodeHtml(message),
                    uid: uid,
                    //message: message,
                }
            );
            $main=$(html);
        }
        $main.find("main").html(message);
        if(svgIcon){
           $main.find(">.icon").append("<svg class='svg'><use class='svg' href='#"+svgIcon+"'></use></svg>") ;
        }

        super($main,"CqNotification");
        let me=this;

        this.progress=new CqProgressBar(
            this.$main.find("[cq-progress-bar]")
        );
        /*
        TweenMax.from(this.$main,0.2,{
            height:0,
            marginBottom:0,marginTop:0,
            paddingTop:0,paddingBottom:0,
            onComplete:function(){

            }
        });
        */
        this.timer=TweenMax.fromTo(this.progress,duration,
            {
                progress:0
            },
            {
                progress:100,
                ease:Linear.EaseNone,
                onComplete:function(){
                    me.remove();
                }
            }
        );


    }

    pause(){
        this.timer.pause();
    }
    resume(){
        this.timer.resume();
    }

    /**
     * Fait disparaitre la notification
     * @param $notification
     */
    remove($notification){
        let me=this;
        TweenMax.to(me.$main,0.4,{
            height:0,
            paddingTop:0,
            paddingBottom:0,
            marginTop:0,
            marginBottom:0,
            ease:Sine.easeIn,
            onComplete:function () {
                me.$main.remove();
            }});

    }

    destroy(){

    }
}

/**
 * Pour invoquer un CqNotification depuis son objet JQuery
 * @returns {CqNotification}
 * @constructor
 */
$.fn.CqNotification = function() {
    "use strict";
    if(!$(this).is("[cq-notification='init']")){
        return new CqNotification($(this));
    }else{
        return $(this).data("CqNotification")
    }
};