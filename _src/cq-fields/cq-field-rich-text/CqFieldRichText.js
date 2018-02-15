import DisplayObject from "../../DisplayObject";
import MediumEditor from "medium-editor";
import MediumButton from "medium-button";
require ("medium-editor/dist/css/medium-editor.min.css");
require ("./classiq-medium-editor.less");



export default class CqFieldRichText extends DisplayObject{

    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main);
        let ph=$main.attr("placeholder");
        let me=this;

        let options={
            toolbar:{
                buttons:["bold","italic","anchor","select-record","removeFormat"]
            },
            placeholder: {
                text: ph?ph:'Texte ici...',
                hideOnClick: false
            },
            extensions: {
                // with JavaScript
                'select-record': new MediumButton({
                    label:'#page',
                    action: function(html, mark, parent){

                        /**
                         * Dans l'ordre...
                         * -insère un element temporaire #tmp-tag-medium et le renvoie
                         * -ouvre le record selector
                         *
                         * -une fois que l'utilisateur a choisi un record, remplace le tag temporaire par le lien vers le record
                         * -lance une event input qui dira d'enregister
                         */


                        function $tmpTag(){return $("#tmp-tag-medium");}

                        wysiwyg.recordSelector.getUids(false,"page,pagefilm,pageauteur,pagevideo").then(
                            function(uids){
                                //remplace par le lien ver l'uid
                                let $replace=$tmpTag();
                                //pour éviter les balises <a> imbriquées
                                while($replace.is("a #tmp-tag-medium")){
                                    $replace=$replace.closest("a");
                                }
                                let url=wysiwyg.recordSelector.getRecordUrl(uids[0]);
                                $replace.replaceWith(
                                    $("<a href='"+url+"'>"+$("<span>"+html+"</span>").text()+"</a>")
                                );
                               me.$main.trigger("input");

                            },
                            function(){
                                //remplace par l'original
                                $tmpTag().replaceWith(
                                    $("<span>"+html+"</span>").text()
                                )
                            }
                        );

                        //insere un tag que l'on remplacera une fois que le record selector nous aura répondu
                        return '<span id="tmp-tag-medium" style="background-color: yellow">'+$("<span>"+html+"</span>").text()+'<span>';

                    }
                })


            }
        };
        /**
         *
         * @type {MediumEditor}
         */
        this.mediumEditor=new MediumEditor($main[0],options);
    }

    destroy(){
        this.mediumEditor.destroy();
    }



}
