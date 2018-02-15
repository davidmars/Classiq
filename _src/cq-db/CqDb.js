/**
 * Permet de communiquer avec la base de données
 */
export default class CqDb{

    constructor(){
        this.toto="rigolo";
    }
    /**
     * Efface un uid (demande confirmation à l'utilisateur avant)
     * @param uid
     * @returns {Promise<any>}
     */
    trash(uid){
        return new Promise(function (resolve,reject) {
            if(confirm("Êtes vous certain?")){
                window.pov.api.delete({uid:uid},function(r){
                    console.log(r);
                    if(r.success){
                        resolve(r)
                    }else{
                        cqAdmin.notify.apiResponse(r);
                        reject(r);
                    }
                });
            }
        })
    }
}