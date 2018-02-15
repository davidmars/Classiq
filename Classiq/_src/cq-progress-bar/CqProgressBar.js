import CqProgress from "../cq-progress/CqProgress";

require("./cq-progress-bar.less");

export default class CqProgressBar extends CqProgress{
    constructor($main){
        super($main);
        this.$bar=$main.find(".bar");
        this._updateDisplay();
    }

    set progress (val) {
        this._value=val;
        this._updateDisplay();
    }
    get progress (){
        return this._value;
    }

    /**
     * Met à jour la progression
     * @private
     */
    _updateDisplay(){
        this.$bar.width(
            window.pov.utils.ratio(
                this._value,this.max,100,this.min,0
            )+"%"
        );
    }

}