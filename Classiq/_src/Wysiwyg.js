import DisplayObject from "./DisplayObject";
import CqBigMenu from "./unique-instances/cq-big-menu/CqBigMenu";
import WysiwygField from "./cq-fields/WysiwygField";
import CqRecordPicker from "./unique-instances/cq-record-picker/CqRecordPicker";
import CqContextMenu from "./unique-instances/cq-context-menu/CqContextMenu";
import CqBlocks from "./cq-blocks/CqBlocks";
import WysiwygImage from "./cq-fields/WysiwygImage";
import WysiwygRichText from "./cq-fields/cq-field-rich-text/CqFieldRichText";
import CqNotifier from "./unique-instances/cq-notifier/CqNotifier";
import CqSortable from "./cq-sortable/CqSortable";
import CqFieldRecords from "./cq-field-records/CqFieldRecords";
import CqFieldGoogleMap from "./cq-fields/cq-field-google-map/CqFieldGoogleMap";
import CqFieldCrop from "./cq-fields/cq-field-crop/CqFieldCrop";
import CqEditRecordBox from "./unique-instances/cq-edit-record-box/CqEditRecordBox";
import CqAdmin from "./CqAdmin";
import {CqFieldUpload} from "./cq-fields/CqFieldUpload";
require("./cq-visible-in-viewport/cq-visible-in-viewport");

require("./cq-base/cq-typography/cq-typography.less");
require("./cq-list-item-class/cq-list-item.less");
require("./cq-box/cq-box.less");
require("./cq-btn/cq-btn.less");


export default class Wysiwyg{

    constructor(){

        let me=this;
        setTimeout(function(){
            me._boot();
            me._initListeners();
            window.cqAdmin = new CqAdmin();
        },10)

    }

    _boot(){
        console.log("boot Wysiwyg",this)
        /**
         * Le bon gros menu à gauche
         * @type {CqBigMenu}
         */
        this.bigMenu=new CqBigMenu($("#the-cq-big-menu"));
        /**
         * Le menu contextuel
         * @type {CqContextMenu}
         */
        this.contextMenu=new CqContextMenu($("#the-cq-context-menu"));
        /**
         * L'ui qui permet de selectionner des records
         * @type {CqRecordPicker}
         */
        this.recordSelector=new CqRecordPicker($("#the-cq-record-picker"));
        /**
         * L'objet qui permet d'afficher des notifications en bas à droite
         * @type {CqNotifier}
         */
        this.notifier=new CqNotifier($("#the-cq-notifier"));
        /**
         * Le layer qui permet d'etiter un record en grand
         * @type {CqEditRecordBox}
         */
        this.recordEditor=new CqEditRecordBox($("#the-cq-record-editor"));
    }

    /**
     *
     * @private
     */
    _initListeners(){
        let me=this;

        function refreshAfterLogout(){
            window.povSSE.close();
            document.location.reload(true);
        }
        //logout externe
        window.povSSE.on(EVENTS.SSE_USER_LOGOUT,function(e){
            refreshAfterLogout();
        });
        //logout externe (si il y a eu un plantage entre temps)
        window.povSSE.on(EVENTS.SSE_USER_IS_WYSIWYG,function(e){
            if(e.vars.wysiwyg===false){
                refreshAfterLogout();
            }
        });


        //quand le dom change
        $body.on(Pov.events.DOM_CHANGE_OR_READY,function(){
            //initialise des composants wysiwyg au besoin
            DisplayObject.__fromDom(CqBlocks,"cq-blocks");
            DisplayObject.__fromDom(WysiwygImage,"wysiwyg-image");
            DisplayObject.__fromDom(WysiwygRichText,"cq-field-rich-text");
            DisplayObject.__fromDom(CqSortable,"cq-sortable");
            DisplayObject.__fromDom(CqFieldRecords,"cq-field-records");
            DisplayObject.__fromDom(CqFieldGoogleMap,"cq-field-google-map");
            DisplayObject.__fromDom(CqFieldCrop,"cq-field-crop");

            //nettoie les display objects innutiles
            DisplayObject.cleanfromDom();
            window.pov.Xdebug.fromDom();
        });





        //Event changed....
        $body.on("input "+Wysiwyg.events.CHANGED,"[wysiwyg-var]",function(e){
            e.stopPropagation();
            if($(e.target).is("[wysiwyg-var]")){
                console.log("Wysiwyg.events.CHANGED")
                let field=new WysiwygField($(this));
                field.mirror();
                field.doSave();
            }
        });

        // click sur un uploader de fichier
        $body.on("change","[wysiwyg-var][wysiwyg-data-type='file'] input[type='file']",function(){
            new CqFieldUpload($(this).closest("[wysiwyg-var][wysiwyg-data-type='file']"));
        });

        // changement d'un checkbox
        $body.on("change","[wysiwyg-var][wysiwyg-data-type='list-string'] input[type='checkbox']",function(){
            let $f=$(this).closest("[wysiwyg-var][wysiwyg-data-type='list-string']");
            let field=new WysiwygField($f);
            field.doSave(true);
        });
        // changement d'une geoloc
        $body.on("input change","[wysiwyg-var][wysiwyg-data-type='geoloc'] input[latlng]",function(){
            let $f=$(this).closest("[wysiwyg-var][wysiwyg-data-type='geoloc']");
            let field=new WysiwygField($f);
            field.doSave(true);
        });


    }
}


Wysiwyg.events={
    CHANGED:"WYSIWYG_EVENT_CHANGED"
};



