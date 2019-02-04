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
            startSize:[100, 100, '%'],
            maxSize: [100, 100, '%']
        }

        //applique les valeurs enregistrées
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
                },10);
            }
        }
        //quand ça change
        options.onCropMove=function(value){
            //applique la valeur au champ
            $input.val(JSON.stringify(value));
            //met à jour éventuellement la preview css en bougeant le background image
            if($target){
                //background-size
                let csssize=String(1/value.width*100)+"% ";
                csssize+=String(1/value.height*100)+"% ";
                $target.css("background-size",csssize);

                //background-position
                let convert=function(position,size){
                    if(size===1){
                        size=0.9999999;
                    }
                    return pov.utils.ratio(position,1-size,100,0,0);
                }
                let csspos=String(convert(value.x,value.width))+"% ";
                csspos+=String(convert(value.y,value.height))+"% ";
                $target.css("background-position",csspos);
            }
        }
        //enregistre
        options.onCropEnd=function(data) {
            $field.trigger("input");
        }
        var croppr = new Croppr($img[0], options);

    }

    destroy(){
        //this.mediumEditor.destroy();
    }
}