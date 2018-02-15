import DisplayObject from "../DisplayObject";

export default class CqProgressCircle extends DisplayObject{

    /**
     *
     * @param {JQuery} $main L'objet auquel on rattache le loading
     */
    constructor($main){

        super($main);

        let me=this;

        /**
         *
         * @type {JQuery}
         */
        this.$svg=$('' +
            '<svg class="wysiwyg-progress-circle" width="60" height="60" viewPort="0 0 60 60">\n' +
            '  <circle r="25" cx="30" cy="30" fill="transparent" stroke-dasharray="0" stroke-dashoffset="0"></circle>\n' +
            '  <circle class="progress" r="25" cx="30" cy="30" fill="transparent" stroke-dasharray="0" stroke-dashoffset="0"></circle>\n' +
            '</svg>'
        );
        /**
         *
         * @type {JQuery}
         */
        this.$progress=this.$svg.find(".progress");
        /**
         *
         * @type {JQuery}
         */
        this.$circles=this.$svg.find("circle");

        this._initAccordingRadius();


        this.w=60;
        this.h=60;

        if($main){
            wysiwyg.contextMenu.$main.append(this.$svg);
            this.$svg.css("position","absolute");
            this.$svg.css("z-index","100000");
            this.setPercent(0);
            this.loop=setInterval(function(){
                if(!me.isInDom()){
                    me.destroy();

                }else{
                    let rect=me.$main.get(0).getBoundingClientRect();
                    TweenMax.to(me.$svg,0,
                        {
                            x:rect.x+me.$main.width()-me.w - 30,
                            y:rect.y+30
                        }
                    );
                }

            },100);

        }

        this.loop=null;

    }

    /**
     * En fonction de la taille du rayon le dasharray svg doit mesurer le périmètre
     * @private
     */
    _initAccordingRadius(){
        var radius=this.$circles.attr("r");
        var circ=Math.PI*2*radius;
        this.$circles.attr("stroke-dasharray",circ);
    }

    /**
     * Définit le purcentage à afficher
     * @param {Number} percent
     */
    setPercent(percent){
        //pour que visuellement on voit le truc tourner
        percent=Math.min(percent,98);
        percent=Math.max(percent,2);
        var r = this.$progress.attr('r');
        var c = Math.PI*(r*2);
        var pct = ((100-percent)/100)*c;
        this.$progress.css({ strokeDashoffset: pct});
    }

    destroy(){
        if(this.$svg && this.$svg.length) {
            this.$svg.remove();
        }
        if(this.loop){
            clearInterval(this.loop);
        }

    }

}