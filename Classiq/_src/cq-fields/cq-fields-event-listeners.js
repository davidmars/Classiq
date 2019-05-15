


//Event changed....
import WysiwygField from "./WysiwygField";
import WysiwygString from "./WysiwygString";
import {CqFieldUpload} from "./CqFieldUpload";


// un changement de type input (textes, select etc...)
$body.on("input WYSIWYG_EVENT_CHANGED","[wysiwyg-var]",function(e){
    e.stopPropagation();
    if($(e.target).is("[wysiwyg-var]")){
        console.log("Wysiwyg.events.CHANGED")
        let field=new WysiwygField($(this));
        field.mirror();
        field.doSave();
    }
});
$body.on("click WYSIWYG_EVENT_CHANGED","button[wysiwyg-var]",function(e){
    e.stopPropagation();
    if($(e.target).is("[wysiwyg-var]")){
        console.log("Wysiwyg.events.CHANGED")
        let field=new WysiwygField($(this));
        field.mirror();
        field.doSave();
    }
});

// on paste sur des champs texte sans formatage de texte
$body.on("paste","[wysiwyg-var][contenteditable='true'][wysiwyg-data-type-format='STRING_FORMAT_NO_HTML_SINGLE_LINE'],[wysiwyg-var][contenteditable='true'][wysiwyg-data-type-format='STRING_FORMAT_NO_HTML_MULTI_LINE']",function(e){
    let $field=$(this);
    let caret=$field[0].selectionStart;
    setTimeout(function(){
        $field.text(WysiwygString.format($field.text(),$field.attr("wysiwyg-data-type-format")));
    },10);
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