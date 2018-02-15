import DisplayObject from "../DisplayObject";
import CqSortable from "../cq-sortable/CqSortable";
import WysiwygField from "../cq-fields/WysiwygField";

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
         * Le bouton (qui est aussi le champ)
         */
        this.$btn=$main.find("button[wysiwyg-var][wysiwyg-data-type='records']");

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