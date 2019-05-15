import DisplayObject from "../DisplayObject";



require("./cq-popin-box.less");
/**
 * une popin draggable
 */
export default class CqPopinBox extends DisplayObject{

    constructor($main,CLASS_NAME="CqPopinBox"){
        super($main,CLASS_NAME);
        let me=this;
        /**
         * La barre qui permet de dragger la fenêtre
         * @type {JQuery|*}
         */
        this.$nav=this.$main.find(">nav");
        this.$content=this.$main.find(">main");
        this.$footer=this.$main.find(">footer");


        this._daggable=Draggable.create($main, {
            edgeResistance:0.15,
            type:"x,y",
            throwProps:true,
            trigger:me.$nav
        });

        this.$main.on("click","[href='#hide-closest-box']",function(e){
            e.preventDefault();
            me.close();
        });

    }

    /**
     * Ferme la fenêtre
     */
    close(){
        if(this.$main.hasClass("open")){
            this.$main.removeClass("open");
            this.emit(EVENTS.CLOSE);
        }
    }

    /**
     * Affiche la fenêtre
     */
    open(xpos=0.5,ypos=0.5){

        wysiwyg.contextMenu.show();
        this.$main.addClass("open");
        this.$main.css("z-index",Draggable.zIndex++);
        TweenMax.fromTo(
            this.$main,0.2,
            {scale:1.2,opacity:0},
            {scale:1,opacity:1}
        );

        //position centrées par défaut
        let x,y;

        if(xpos){
            x=Math.round(
                window.pov.utils.ratio(
                    xpos,
                    1,
                    $( window ).width()-this.width()-10,
                    0,
                    10
                )
            );
        }
        if(ypos){
            y=Math.round(
                window.pov.utils.ratio(
                    ypos,
                    1,
                    $( window ).height()-this.height()-10,
                    0,
                    10
                )
            );
        }

        TweenMax.set(this.$main,{x:x,y:y});
        this.emit(EVENTS.OPEN);

    }

}