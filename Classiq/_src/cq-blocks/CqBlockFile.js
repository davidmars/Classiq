import CqBlock from "./CqBlock";
import WysiwygProgressCircle from "../cq-progress-circle/CqProgressCircle";

export default class CqBlockFile extends CqBlock{

    /**
     *
     * @param {string} templatePath Chemin du template
     * @param {CqBlocks} parentBlocksList
     * @param {File} file
     */
    constructor(templatePath,parentBlocksList,file){
        super(templatePath,parentBlocksList);
        let me=this;
        /**
         * Le fichier qui sera uploadé
         * @type {File}
         */
        this.file = file;
    }

    /**
     * Uploade le fichier, l'associe à tartgetUid
     * @returns {Promise<any>}
     */
    upload(){
        let me=this;
        let progress=new WysiwygProgressCircle(this.$main);
        return new Promise(function(resolve, reject) {
            window.pov.api.upload(me.file,function(pct){
                    progress.setPercent(pct);
                }).then(
                    function(uploadJson){
                        if(uploadJson.json.record){
                            me.setTargetUid(uploadJson.json.record.uid ).then(
                                function() {
                                    resolve()
                                }
                            )
                        }else{
                            console.error("oups sur l'upload d'image",uploadJson);
                            reject();
                        }
                    }
                );
        });
    }


}