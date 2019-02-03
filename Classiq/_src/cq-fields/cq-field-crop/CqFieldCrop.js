import DisplayObject from "../../DisplayObject";
//require("./cq-field-crop.less");
import Croppr from 'croppr';
require("croppr/dist/croppr.min.css");
/**
 *
 */
export default class CqFieldCrop extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main);
        let $field=$main.closest("[wysiwyg-var]");


        let me=this;

        let ratioTargetSelector=$main.attr("data-ratio-target-selector");
        let ratio=null;
        let $img=$main.find("img");
        let $input=$main.find("textarea");
        let value=null;
        if($input.val()){
            value=JSON.parse($input.val())
        }
        let $target=null;
        if(ratioTargetSelector){
            $target=$(ratioTargetSelector);
            switch($target.length){
                case 0:
                    console.error("cropper no target found "+ratioTargetSelector);
                    break;
                case 1:
                    ratio=$target.height()/$target.width();
                    console.warn("ratio "+ratio);
                    break;
                default:
                    console.error("cropper many targets found "+ratioTargetSelector);
                    break;
            }
        }

        var options={
            aspectRatio:ratio,
            returnMode:"ratio",
            //startSize:[100, 100, '%'],
            maxSize: [100, 100, '%']
        }
        if(value){
            //options.startSize=[value.width*100,value.height*100,"%"];
        }
        options.onInitialize=function(instance){
            if(value){
                setTimeout(function(){
                    instance.resizeTo(
                        value.width*instance.imageEl.width,
                        value.height*instance.imageEl.height
                    );
                    instance.moveTo(
                        value.x*instance.imageEl.width,
                        value.y*instance.imageEl.height
                    );
                },100);
            }

        }
        options.onCropMove=function(value){
            //console.log(value.x, value.y, value.width, value.height);
            $input.val(JSON.stringify(value));
            let s=""+(1/value.width*100)+"% "+(1/value.height*100)+"% ";
            function convert(x,width){
                if(width===1){
                    width=0.9999999;
                }
                return pov.utils.ratio(x,1-width,100,0,0);
            }
            let p=""+String(convert(value.x,value.width))+"% "+convert(value.y,value.height)+"% ";
            //p=""+value.x*100+"% "+"100% ";
            if($target){
                $target.css("background-size",s);
                $target.css("background-position",p);
                console.log(s,p);
            }
            console.log("change crop",me.$main);
            //me.$main.trigger(EVENTS.CHANGE);
            $field.trigger("input");
        }

        var croppr = new Croppr($img[0], options);

    }

    destroy(){
        //this.mediumEditor.destroy();
    }
}