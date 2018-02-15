
export default class CqNewRecord{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){

        this.$main=$main;
        this._$nameField=$main.find("input[type='text'][name='name']");
        this._$step2=$main.find(".js-step-2");
        this._$link=$main.find(".js-link");
        this._$createBtns=$main.find("[href='#create-record']");

        this.showStep2(false);
        this._initListeners();


    }

    _initListeners(){
        let me=this;
        me._$nameField.on("input",function(){
            if($(this).val()){
                me.showStep2(true);
            } else{
                me.showStep2(false);
            }
        });
        $(me._$nameField).on('keyup', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                me.create(me._$nameField.val(),$(this).attr("record-type"));
            }
        });
        me._$createBtns.on("click",function(e){
            e.preventDefault();
            me.create(me._$nameField.val(),$(this).attr("record-type"));
        });
    }

    /**
     * Affiche ou cache la seconde partie
     * @param show
     */
    showStep2(show=true){
        if(show){
            this._$step2.css("display","");
        }else{
            this._$step2.css("display","none");
        }
    }

    /**
     * Crée la page et (si ça a marché) affiche une preview de la page
     * @param modelName
     * @param modelType
     */
    create(modelName,modelType){

        let me=this;
        console.log("va créer un "+modelType+" qui se nomme "+modelName);

        let obj={
            modelType:modelType,
        };
        obj.modelVars={};
        obj.modelVars["name"]=modelName;

        this.loading(true);
        window.pov.api.create(
            obj,
            function(result){
                me.loading(false);
                console.log(result);


                if(result.success){

                    if(result.json.recordCreated.hrefRelative){
                        let $link=$("<a>Aller sur la page</a>");
                        $link.attr("href",result.json.recordCreated.hrefRelative);
                        setTimeout(function(){
                            $link.remove()
                        },1000*15);
                        me._$link.prepend($link);
                    }
                    //let url=result.json.recordCreated.hrefRelative;
                    //PovHistory.goUrl(url);
                    //wysiwyg.bigMenu.open("page");
                    me.reset();
                }else{
                    for(let err of result.errors){
                        wysiwyg.notifier.notify(err,5,"danger");
                    }
                }
            }
        )
    }

    /**
     * vide le champ name et cache step 2
     */
    reset(){
        this.showStep2(false);
        this._$nameField.val("");
    }

    loading(state){
        if(state){
            this.$main.addClass("wysiwyg-loading");
        }else{
            this.$main.removeClass("wysiwyg-loading");
        }
    }
}