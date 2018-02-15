/**
 * Permet de gérer les évènements via des attributs cq-on.
 * Cette classe peut aussi bien être utilisée sur le front que sur le wysiwyg
 */
export default class CqEventsListener{
    constructor(){
        let me=this;
        /**
         * Les objet dans lesquels on peut tester des méthodes
         * @type {Array}
         */
        this._possibleObjects=[];
        /**
         * Attributs enregistrés pour être écouté
         * @type {{}}
         * @private
         */
        this._listenerAttributtes={};

        this.addListener("click","cq-on-click")
        this.addListener("submit","cq-on-submit")

    }

    /**
     *
     * @param {*} root l'objet où chercher des methodes/propriétés
     * @param {string} methodDots monObjet.montruc.maFontion par exemple
     * @param {int} removes permet de supprimer x elements à la fin pratique pour obtenir l'objet qui appelle une fonction par exmeple
     * @private
     * @returns {*}
     */
    _getInst(root,methodDots,removes=0){
        let dots=methodDots.split(".");
        for(let remove=0;remove<removes;remove++){
            dots.pop();
        }
        for(let i=0;i<dots.length;i++){
            if(root[dots[i]]){
                //console.log("_getInst ok "+i,root[dots[i]],typeof root[dots[i]]);
                root=root[dots[i]];
            }else{
                //console.log("_getInst nok "+i,root[dots[i]],typeof root[dots[i]]);
                root=null;
                break;
            }
        }
        return root;
    }

    /**
     * Tente d'exéuter la fonction
     * @param {string} attribute
     * @param {object} mainObject
     * @private
     */
    _evaluate(attribute,mainObject=null){
        let me=this;
        let reg=/(.*)\((.*)\)/;
        let matches=attribute.match(reg);
        if(!matches){
            console.error("evaluate = "+attribute+" marche pas (1)",this);
            return;
        }
        let method=matches[1];
        let args=matches[2];
        args=args.split(",");

        let inst,parent;
        for(let obj of me._possibleObjects){
            inst=me._getInst(obj,method);
            parent=me._getInst(obj,method,1);
            if(inst){
                break;
            }
        }
        if(inst){
            //console.log("okkkkkkk",inst,typeof inst);
            if(typeof inst==="function"){
                if(args[0]==="this"){
                    //appelle la fonction en utilisant mainObject pour $(this)
                    inst.apply(mainObject, args);
                }else{
                    //appelle la fonction en utilisant l'objet parent de la fonction pour this
                    inst.apply(parent, args);
                }
            }
        }else{
            console.error("CqEventsListener "+attribute+" ne marche pas (pas d'instance)",this,inst);
        }
    }

    /**
     * Permet d'ajouter des méthodes aux écouteurs
     * @param {object} object
     */
    addObject(object){
        this._possibleObjects.push(object);
    }

    /**
     * Ajoute un écouteur d'évènement sur $body pour un type donné.
     * cq-on-click et cq-on-submit sont déclarés d'office.
     * @param {string} eventName Exemple click,submit,load
     * @param {string} eventAttribute Exemple cq-on-click, cq-on-submit, cq-on-load
     */
    addListener(eventName,eventAttribute){

        let me=this;

        if(me._listenerAttributtes[eventAttribute]){
            let err=eventAttribute+" a déjà été déclaré ";
            alert(err);
            console.error(err);
        }
        me._listenerAttributtes[eventAttribute]=eventName;

        $body.on(eventName,"["+eventAttribute+"]",function(e){
            console.log("cq-on",e.type)
            e.preventDefault();
            e.stopPropagation();
            me._evaluate($(this).attr(eventAttribute),$(this));
        });
    }
}