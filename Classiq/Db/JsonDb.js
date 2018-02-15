var JsonDb={
    jsonDbApi:function(method,db,uid,datas,cb){

        $.ajax({
            dataType: "json",
            url: LayoutVars.rootUrl+"/jsonDbApi/"+method+"/"+db+"/"+uid,
            data: datas,
            method:"post",
            success: function(data){
                if(cb){
                    cb(data)
                }
            }
        });
    }
    ,
    saveLego:function(legoData,cb){
        console.log(legoData);
        EditApi.jsonDbApi(
            "save",
            "legos",
            legoData.uid,
            {
                recordDatas:EditApi.utils.fixRecordDatas(legoData)
            }
            ,function(data){
                console.log("Lego enregistrée",data);
                if(cb){
                    cb(data);
                }
            }
        );
    }
    ,
    utils:{
        /**
         * Permet de filtrer les données avant de les envoyer.
         * Permet entre autre choses d'envoyer des arrays vides
         * @param {*} recordDatas
         * @returns {*}
         */
        fixRecordDatas:function(recordDatas){
            for (var k in recordDatas){
                if (recordDatas.hasOwnProperty(k) ) {
                    if(typeof recordDatas[k]=="object" && recordDatas[k].length==0)
                        recordDatas[k]=[""];
                }
            }
            return recordDatas;
        }
    },
    forms:{
        initListeners:function(){
            var $body=$("body");
            $body.on("click","form[data-json-db-api-form] [href='#submit']",function(e){
                $(this).closest("form").submit();
            });
            $body.on("submit","form[data-json-db-api-form]",function(e){
                e.preventDefault();
                JsonDb.forms.submit($(this));
            });
        },
        submit:function($form){
            $form.addClass("form-sending");
            setTimeout(function(){
                JsonDb.forms.sendForm(

                    PovForm.getDataObject($form),
                    function(jsonReturn){
                        var $message=$form.find(".form-message");
                        $form.removeClass("form-sending");
                        if(jsonReturn.success){
                            $message.html(jsonReturn.messages.join("<br>"));
                        }else{
                            $message.html(jsonReturn.errors.join("<br>"));
                        }
                        setTimeout(function(){
                            $message.text("");
                        },10 *1000);
                    }
                );
            },1*1000);


        },
        sendForm:function(legoUid,formDatas,cb){
            $.ajax({
                dataType: "json",
                url: LayoutVars.rootUrl+"/forms/send/"+legoUid,
                data: {
                    formDatas:formDatas
                },
                method:"post",
                success: function(data){
                    if(cb){
                        cb(data)
                    }
                }
            });
        },
        utils:{
            /**
             * Renvoie les données d'un FORM sous forme d'objet
             * @param $form
             * @returns {*}
             */
            getDataObject:function($form){
                var data = $form.serializeArray();
                function objectifyForm(formArray) {//serialize data function
                    var returnArray = {};
                    for (var i = 0; i < formArray.length; i++){
                        returnArray[formArray[i]['name']] = formArray[i]['value'];
                    }
                    return returnArray;
                }
                return objectifyForm(data);
            }
        }
    }
};


