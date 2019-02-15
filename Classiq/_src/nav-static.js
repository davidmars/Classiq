/**
 * Ce script permet de gérer la navigation ajax pour les sites exportés en statique
 */
if(LayoutVars.isExportStatic){
    console.log("Static version");
    /**
     * Charge l'url en ajax json
     * @param {string} url
     * @param {function} cb eventuellement fonction de callback qui permet d'injecter comme on veut le html
     */
    PovHistory.loadPage=function(url,cb){
        Pov.events.dispatchDom($body,EVENTS.HISTORY_CHANGE_URL_LOADING);
        console.log("Static page load");
        $.ajax({
            dataType: "json",
            url: url+".pov.json",
            data: {
                povHistory:true
            },
            success: function(e){
                //console.log("loadED page",url);
                //console.log("loadED page result",e);
                Pov.events.dispatchDom($body,EVENTS.HISTORY_CHANGE_URL_LOADED);
                if(cb){
                    cb(e);
                }else{
                    if(e.json.meta){
                        PovHistory.setMeta(e.json.meta);
                        PovHistory.currentPageInfo=e.json.pageInfo;
                    }
                    if(e.html){
                        PovHistory.injectHtml(e.html);
                    }

                }
            },
            error:function(e,t,err){
                Xdebug.fromString(e.responseText);
            }
        });
    }
}