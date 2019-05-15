import DisplayObject from "../DisplayObject";
import CqLocalStorage from "../CqLocalStorage";
require("../cq-display-if/cq-display-if.less");

export default class CqDisplayControl extends DisplayObject{
    constructor($main){
        super($main,"CqDisplayControl");

        let $chk=$main.find("input[type='checkbox']");
        let storage=new CqLocalStorage("cq-is-display");

        $chk.each(function(){
            let prop=$(this).attr("value");
            if(storage.getValue(prop)){
                setIsDisplay(prop,true);
                $(this).prop("checked", true);
            }else{
                setIsDisplay(prop,false);
            }
        })

        $chk.on("change",function(){
            let prop=$(this).attr("value");
            setIsDisplay(prop,$(this).is(":checked"));
        });

        function setIsDisplay(prop,flag){
            if(flag){
                $body.attr("cq-display-"+prop,"1");
                storage.setValue(prop,true);
            }else{
                $body.removeAttr("cq-display-"+prop);
                storage.setValue(prop,false);
            }
        }


    }
}