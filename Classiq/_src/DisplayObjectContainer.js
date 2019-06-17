import DisplayObject from "./DisplayObject";

export default class DisplayObjectContainer extends DisplayObject{
    constructor($main,CLASS_NAME){
        super($main,CLASS_NAME);
    }

    /**
     *
     * @param {DisplayObject|JQuery} element
     */
    addChild(element){
        if(element.isPrototypeOf(DisplayObject)){
            this.$main.append(element.$main);
        }else if(element instanceof jQuery){
            this.$main.append(element);
        }else{
            console.error("addChild problem",element);
        }
    }

}