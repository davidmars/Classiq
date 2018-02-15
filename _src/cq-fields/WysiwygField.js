import WysiwygString from "./WysiwygString";

export default class WysiwygField{

    constructor($field){

        if($field.data("WysiwygField")){
            return $field.data("WysiwygField");
        }else{
            $field.data("WysiwygField",this);
        }
        /**
         * {jQuery} élément jQuery du champ
         */
        this.$field=$field;
        /**
         * {string} Type de record
         */
        this.type=$field.attr("wysiwyg-type");
        /**
         * {Number} id du record
         */
        this.id=$field.attr("wysiwyg-id");
        /**
         * {string} La variable qu'on va modifier
         */
        this.var=$field.attr("wysiwyg-var");
        /**
         * {string} le type de variable (string|
         */
        this.dataType=$field.attr("wysiwyg-data-type");
        /**
         * {string} le type de variable (string | todo autres types de champs)
         */
        this.dataTypeFormat=$field.attr("wysiwyg-data-type-format");
        /**
         * {string} Action à effectuer quand ça a été enregistré (refresh | todo autres types d'actions)
         */
        this.onSavedAction=$field.attr("wysiwyg-on-saved-action");


        /***
         *
         * @type {null}
         * @private
         */
        this.__onInputTimeout=null;
    }

    /**
     * sélecteur dom a utiliser pour rafraichir une fois enregistré
     */
    onSavedActionSelector(){
        return this.$field.attr("wysiwyg-on-saved-action-selector");
    }

    /**
     * Enregistre le champ
     * @param {Boolean} preventDelay Mettre sur true pour que le champ s'enregistre tout de suite, sinon un délais de 2 seondes est appliqué pour eviter de faire trop d'appels
     * @param {Function} cb Callback après l'enregistrement du champ
     * @param {object} options pour passer des directives lors de l'enregistrement :
     * returnListItems = variableQuiEstUneListe renvera le contenu html de la liste
     */
    doSave(preventDelay=false,cb=null,options={}){

        //console.log("doSave field",this.var,preventDelay,this.__onInputTimeout);
        this.loading(true);
        let me=this;
        //vire le delay
        if(me.__onInputTimeout){
            clearTimeout(me.__onInputTimeout);
            me.__onInputTimeout=null;
        }
        if(!preventDelay){
            //fera l'action mais plus tard
            me.__onInputTimeout=setTimeout(
                function(){
                    me.doSave(true,cb);
                },
                2000
            );
        }else{
            //fait l'action
            let obj={
                modelType:this.type,
                modelId:this.id
            };
            obj.options=options;
            obj.modelVars={};
            obj.modelVars[this.var]=this.value();
            this.loading(true);
            window.pov.api.save(
                obj,
                function(result){
                    me.loading(false);
                    if(me.onSavedAction){
                        switch (me.onSavedAction){
                            case "refresh":
                                me.$field.evalHere(me.onSavedActionSelector()).povRefresh();
                                break;
                            case "reload":
                                location.reload(true);
                                break;
                            default:
                                console.error("on saved non géré",me.onSavedAction);
                        }
                    }
                    me.$field.trigger(EVENTS.SAVED);
                    if(result.errors){
                        for(let err of result.errors){
                            wysiwyg.notifier.notify(err,10,"danger");
                        }
                    }
                    if(cb){
                        cb(result);
                    }
                }
            )
        }


    }

    /**
     * Retourne la valeur du champ
     * @returns {*} La valeur du champ peut différer en fonction du dataType et du dataTypeFormat
     */
    value(){
        switch (this.dataType){

            case "string":
                if(this.$field.is("input,textarea,select")){
                    return this.$field.val();
                }else if(this.$field.is("[contenteditable]")){
                    this.$field.find("a,span,div").filter(
                        function() {
                            return $.trim($(this).text()) === "";
                        }
                        ).remove();
                    let str=this.$field.html();
                    str=WysiwygString.format(str,this.dataTypeFormat);
                    return str;
                }else{
                    console.error("type de champ string non géré",this.$field);
                }
                break;

            case "file":
            case "image":
                if(this.$field.is("[wysiwyg-value]")){
                    return this.$field.attr("wysiwyg-value")
                }else{
                    console.error("type de champ file non géré",this.$field);
                }
                break;

            case "records":
                //liste d'uids
                if(this.$field.is("input,textarea,button")){
                    return this.$field.val();
                }else{
                    console.error("type de champ records non géré, on ne sait pas comment récupérer les uids",this.$field);
                }
                break;

            case "list":
                //alert("liste...");
                let items={};
                let isEmpty=true;
                this.$field.children(["list-item-path"]).each(function(){
                    //pour chaque item dans la liste...
                    let itemData;
                    let itemKey;
                    if($(this).attr("list-item-key")) {
                        itemKey = $(this).attr("list-item-key");
                        isEmpty=false;
                    }else if($(this).attr("cq-block-picker")){
                        //c'est ok lui on laisse passer
                    }else{
                        isEmpty=false;
                        let m="Vous avez probalement oublié de mettre les attributs ListItem->attr() sur votre balise d'item";
                        console.error(m);
                        alert(m);
                        return;
                    }
                    itemData={};
                    itemData.path=$(this).attr("list-item-path");
                    let moreVars=$(this).getMoreVars();
                    $.each(moreVars,function(k,v){
                        itemData[k]=v;
                    });
                    items[itemKey]=itemData;
                });
                if(isEmpty || items.length===0 || !items || $.isEmptyObject(items)){
                    items=[""];
                }

                return items;
            default:
                console.error("type de champ non géré ("+this.dataType+")")
        }
    }

    /**
     * Affiche ou pas l'état loading sur le champ (et ses templates refresh)
     * @param show
     */
    loading(show=false){
        if(show){
            //loading sur le champ lui même
            this.$field.addClass("wysiwyg-loading");
            if(this.onSavedActionSelector()){
                //loading sur les templates qui seront refresh
                this.$field.evalHere(this.onSavedActionSelector()).addClass("wysiwyg-loading");
            }
        }else{
            this.$field.removeClass("wysiwyg-loading");
            if(this.onSavedActionSelector()){
                //loading sur les templates qui seront refresh
                this.$field.evalHere(this.onSavedActionSelector()).removeClass("wysiwyg-loading");
            }

        }
    }

    /**
     * met à jour les autres champs qui correspondent à la même variable
     */
    mirror(){

        let me=this;

        /**
         * Tous les champs avec les même variables
         * @type {JQuery}
         */
        let $mirrors=   $("[wysiwyg-var='"+this.var+"']")
                        .filter("[wysiwyg-id='"+this.id+"']")
                        .filter("[wysiwyg-type='"+this.type+"']")
                        .filter("[wysiwyg-data-type='"+this.dataType+"']")
                        .not(this.$field);

        if(this.dataType==="string"){
            $mirrors.each(function(){
                if($(this).is("input,textarea,select")){
                    $(this).val(me.value())
                }else if($(this).is("[contenteditable]")){
                    $(this).html(me.value())
                }else{
                    console.error("type de champ string non géré (mirror)",me.$field,$(this));
                }
            })
        }

    }
}