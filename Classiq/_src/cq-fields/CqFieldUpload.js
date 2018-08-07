import DisplayObject from "../DisplayObject";
import WysiwygField from "./WysiwygField";
import CqProgressBar from "../cq-progress-bar/CqProgressBar";

require("./cq-field-upload.less");
export class CqFieldUpload extends DisplayObject{
    constructor($main){
        super($main);
        let me=this;

        if(!$main.is("[wysiwyg-var][wysiwyg-data-type='file']")){
            let m="wrong CqFieldUpload";
            alert(m);
            console.error(m,$main);
            return;
        }
        this.field=new WysiwygField($main);
        this.$inputFile=$main.find("input[type='file']");
        this.$progressText=$main.find("[data-progress-text]");
        let $progressBar=$main.find("[cq-progress-bar]");
        this.progressbar=new CqProgressBar($progressBar);

        this._startUpload();
    }

    _startUpload(){
        let me=this;
        me.$main.attr("state","uploading")
        //1 uploader le fichier
        window.pov.api.uploadChuncked(
            me.$inputFile.get(0).files[0],
            function(progress){ //cbProgress
                //console.log("uploading file "+progress)
                me.$progressText.text(String(progress)+"%");
                me.progressbar.progress=progress;
            },
            function(apiResponse){ //cbComplete
                //receptionner l'uid du Filerecord
                //console.log("upload okkk json",apiResponse);
                //3 l'enregistrer dans le champ
                me.field.$field.attr("wysiwyg-value",apiResponse.json.record.uid);
                me.field.doSave(true);
                me.$main.attr("state","")
            },
            function(apiResponse){ //cbError
                //console.error("erreur uploaddddd",apiResponse);
                me.$main.attr("state","error")
            }
        );
        /*
        //ancienne méthode (pas chunk)
        window.pov.api.upload(
            me.$inputFile.get(0).files[0],
            function(progress){
                console.log("uploading file "+progress)
                me.$progressText.text(String(progress)+"%");
                me.progressbar.progress=progress;
            }
        ).then(
            function(apiResponse){
                //2 receptionner l'uid du Filerecord
                console.log("upload ok json",apiResponse);
                //3 l'enregistrer dans le champ
                me.field.$field.attr("wysiwyg-value",apiResponse.json.record.uid);
                me.field.doSave(true);
                me.$main.attr("state","")
            },
            function(){
                //failed
                console.error("erreur upload");
                me.$main.attr("state","error")
            }
        );
        */
    }
}