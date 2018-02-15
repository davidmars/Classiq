export default class CqLocalStorage{
    /**
     *
     * @param {string} name
     */
    constructor (name){
        /**
         * Les données décodées
         * @type {{}}
         */
        this.data={};
        /**
         * Nom du stockage
         * @type {string}
         */
        this.name=name;

        let local=localStorage.getItem(name);
        if(local){
            this.data=JSON.parse(localStorage.getItem(name));
        }

    }

    setValue(varName,value){
        this.data[varName]=value;
        localStorage.setItem(this.name,JSON.stringify(this.data));
    }

    /**
     *
     * @param {string} varName
     * @param defaultValue
     * @returns {*}
     */
    getValue(varName,defaultValue=null){
        let val=this.data[varName];
        if(val!==undefined){
            return val;
        }else{
            return defaultValue;
        }

    }
}