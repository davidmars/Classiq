/**
 * La fenêtre qui permet de sélectionner des records
 */
import WysiwygPopinBox from "../../cq-popin-box/CqPopinBox";



export default class CqRecordPicker extends WysiwygPopinBox{
    /**
     * @param {JQuery} $main
     */
    constructor($main){
        super($main,"CqRecordPicker");
        let me=this;

        this.multiple=false;
        this.recordsTypes=[];


        this.$ok=this.$main.find(".js-btn-ok");
        this.$cancel=this.$main.find(".js-btn-cancel");
        this.$ok.on("click",function(){
            me._onOk();
        });
        this.$cancel.on("click",function(){
            me.close();
        });

        //reloade la liste
        povSSE.on(EVENTS.SSE_DB_COUNT_CHANGE,function(){
            me.$list().povRefresh(function(){
                //réaplique l'état courrant
                me.setMultiple(me.multiple);
                me.setRecordsTypes(me.recordsTypes);
            },true);
        });

        this.on(EVENTS.CLOSE,function(){
            me._onCancel();
        });

    }

    $list(){
        return this.$main.find(".js-list");
    }

    /**
     * Désélectionne tous les records
     */
    resetSelection(){
        this._$allrecords().prop('checked', false);
    }
    reset(){
        this.resetSelection();
    }

    /**
     * évênement appelé quand on clique sur cancel
     * @private
     */
    _onCancel(){
        console.log("onCancel original")
    }

    /**
     * évênement appelé quand on clique sur ok
     * @private
     */
    _onOk(){
        console.log("onSelected original")
    }

    /**
     * Retourne les checkboxes des records selectionnés
     * @returns {JQuery|*}
     * @private
     */
    _$selected(){
        return this._$allrecords().filter(":checked");
    }

    /**
     * Tous les checkboes de record
     * @returns {JQuery|*}
     * @private
     */
    _$allrecords(){
        return this.$main.find("input.js-is-record-checker[value]");
    }


    /**
     * Retourne les uids sélectionnés
     * @returns {Array}
     * @private
     */
    _getSelectedUids(){
        let uids=[];
        this._$selected().each(function(){
           uids.push(
               $(this).prop("value")
           );
        });
        return uids;
    }
    /**
     * Définit dans l'ui les uids sélectionnés
     * @private
     */
    _setSelectedUids(uids){
        let me=this;
        me._$allrecords().prop("checked",false);
        for(let uid of uids){
            console.log(uid);
            me.$main.find("input.js-is-record-checker[value='"+uid+"']").prop("checked",true);
        }
    }
    /**
     * Définit dans l'ui les uids non visibles mais disabled
     * @private
     */
    _setDisabledUids(uids){
        let me=this;
        me._$allrecords().prop("disabled",false);
        for(let uid of uids){
            console.log(uid);
            me.$main.find("input.js-is-record-checker[value='"+uid+"']").prop("disabled",true);
        }
    }


    /**
     *
     * @param {boolean} multiple si false sera des boutons radio, sinon des checkboxes
     */
    setMultiple(multiple=false){
        this.multiple=multiple;
        if(multiple){
            this._$allrecords().attr("type","checkbox")
        }else{
            this._$allrecords().attr("type","radio")
        }
    }

    /**
     * Définit les types de records à afficher
     * @param types
     */
    setRecordsTypes(types){
        this.recordsTypes=types;
        let me=this;
        let $all=me.$content.find("[record-type]");
        if(types){
            $all.css("display","none");

            if(typeof types==="string"){
                types=types.split(",");
            }
            if(!Array.isArray(types)){
                types=[types];
            }
            $.each(types,function(k,v){
                $all.filter("[record-type='"+v+"']").css("display","");
            })
        }else{
            //par defaut affiche tour
            $all.css("display","");
        }
    }

    /**
     * @param {boolean} multiple Selection multiple ou simple de records
     * @param {string} recordsTypes Types de records possibles
     * @param {string[]} preselectedUids liste des records présélectionnés qui seront disabled
     * @returns {Promise} Si ça a marché le premier argument de retour sera un array d'uids, le secon la liste des record preview
     */
    getUids(multiple=false,recordsTypes,preselectedUids=[]){

        let me=this;
        me.open();

        if(typeof preselectedUids==="string"){
            preselectedUids=preselectedUids.split(",");
        }
        //me._setSelectedUids(preselectedUids);
        me._setDisabledUids(preselectedUids);
        this.setMultiple(multiple);
        this.setRecordsTypes(recordsTypes);

        var p = new Promise(
            // La fonction de résolution est appelée avec la capacité de
            // tenir ou de rompre la promesse
            function(resolve, reject) {
                me._onOk=function(){
                    me.close();
                    let uids=me._getSelectedUids();
                    let $previews=me.$recordsPreviews(me._getSelectedUids());
                    console.log(uids,$previews);
                    resolve
                    (
                        uids,
                        $previews
                    );
                    me.reset();
                };
                me._onCancel=function(){
                    me.close();
                    reject();
                };
            }
        );

        return p;

    }

    /**
     * Retounre la preview d'un record
     * @param {string} uid un uid
     * @returns {JQuery|*}
     */
    $recordPreview(uid){
        return this.$main.find("[data-pov-vv-uid='"+uid+"']").clone(false);
    }

    /**
     * Retounre les previews d'une liste de records
     * @param {string[]} uids un tableau d'uid
     * @returns {JQuery}
     */
    $recordsPreviews(uids){
        let r=[];
        for(let uid of uids){
            r.push(this.$recordPreview(uid));
        }
        return $(r).map (function () {return this.toArray(); } );
    }

    /**
     * Retourne un lien à partir d'un uid
     * @param {string} uid "type-id"
     * @returns {string}
     */
    getRecordUrl(uid){
        return LayoutVars.rootUrl+"/permalink-uid/"+uid;
    }

}