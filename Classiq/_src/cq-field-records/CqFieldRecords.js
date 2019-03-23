import DisplayObject from "../DisplayObject";
import CqSortable from "../cq-sortable/CqSortable";
import WysiwygField from "../cq-fields/WysiwygField";
import Wysiwyg from "../Wysiwyg"
import CqProgressBar from "../cq-progress-bar/CqProgressBar";


/**
 * Un champ qui permet de sélectionner des records
 */
export default class CqFieldRecords extends DisplayObject{

    constructor($main){

        super($main);

        let me=this;


        /**
         * La liste
         * @type {CqSortable|*}
         */
        this.list=$main.find("[cq-sortable]").CqSortable();
        /**
         * Le bouton de selection de records (qui est aussi le champ)
         */
        this.$btn=$main.find("button[wysiwyg-var][wysiwyg-data-type='records']");
        /**
         * Le bouton d'upload de fichiers
         */
        this.$btnUpload=$main.find(".input-file-wrap input[type='file']");

        /**
         * plusieurs ou un seul record possible?
         * @type {boolean}
         */
        this.multiple=this.$btn.attr("wysiwyg-multiple")==="true";
        /**
         *
         * @type {WysiwygField}
         */
        this.field=new WysiwygField(this.$btn);


        function triggerChange(){
            me.list.$main.trigger("change")
        }


        //change records
        this.$btn.on("click",function(){
            wysiwyg.recordSelector.getUids(
                me.multiple,
                $(this).attr("wysiwyg-records-types"),
                me.field.value()
            ).then(
                function(uids){
                    if(!me.multiple){
                        me.list.$main.empty();
                    }
                    me.list.$main.append(wysiwyg.recordSelector.$recordsPreviews(uids));
                    triggerChange(); //la liste change est donc est enregistrée
                },
                function(){
                    //console.log("action annulée")
                }
            )
        });
        //change files
        this.$btnUpload.on("input "+Wysiwyg.events.CHANGED,function(e){
            console.log("uploader des fichiers...",e);
            e.stopPropagation();
            let toUpload=0;
            for(let file of $(this).get(0).files){
                toUpload++;
                console.log(file.name);
                let $preview=$(require("./upload-preview.html"));
                /**
                 *
                 * @type {CqProgressBar}
                 */
                let progressbar=new CqProgressBar($preview.find("[cq-progress-bar]"));
                $preview.find(".title").text(file.name);
                me.list.$main.append($preview);
                window.pov.api.uploadChuncked(
                    file,
                    function(progress){ //cbProgress
                        let txt=progress+"%";
                        console.log("uploading file "+txt)
                        $preview.find(".type").text(txt);
                        progressbar.progress=progress;
                    },
                    function(apiResponse){ //cbComplete
                        //receptionner l'uid du Filerecord
                        console.log("upload okkk json",apiResponse);
                        $preview.find(".title").text(apiResponse.json.record.name);
                        $preview.find(".type").text("100% ok")
                        $preview.attr("data-pov-vv-uid",apiResponse.json.record.uid);
                        toUpload--;
                        if(toUpload===0){
                            //3 l'enregistrer dans le champ
                            triggerChange();
                        }
                    },
                    function(apiResponse){ //cbError
                        console.error("erreur uploaddddd",apiResponse);
                        //me.$main.attr("state","error")
                    }
                );
            }

        });

        //quand la liste change
        this.list.$main.on("change",function(){
            let uids=[];
            let $uids=me.list.$main.find("[data-pov-vv-uid]");
            $uids.each(function(){
                uids.push($(this).attr("data-pov-vv-uid"));
            });
            me.field.$field.val(uids);
            me.field.doSave(true);
        });


        this.list.$main.on("mouseenter",".preview-record",function(){
            let $item=$(this);
            wysiwyg.contextMenu.show().setAnchor($item).btns.reset();

            if(!$item.is(":first-child")){
                wysiwyg.contextMenu.btns.before(function(){
                    $item.insertBefore($item.prev());
                    triggerChange()
                });
            }
            if(!$item.is(":last-child")) {
                wysiwyg.contextMenu.btns.after(function () {
                    $item.insertAfter($item.next());
                    triggerChange()
                });
            }
            wysiwyg.contextMenu.btns.trash(function(){
                $item.remove();
                triggerChange()
            });

        });




    }

    destroy(){

    }

}