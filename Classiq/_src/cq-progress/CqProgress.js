import DisplayObject from "../DisplayObject";

export default class CqProgress extends DisplayObject{
    /**
     *
     * @param $main
     */
    constructor($main,CLASS_NAME="CqProgress"){

        super($main,CLASS_NAME);
        this.max=Number($main.attr("max"));
        this.min=Number($main.attr("min"));
        this._value=Number($main.attr("progress"));

        this.min=this.min?this.min:0;
        this.max=this.max?this.max:1;
        this._value=this._value?this._value:0;

    }

    set progress (value) {
        this._value=value;
    }
    get progress (){
        return this._value;
    }

    destroy(){}

}