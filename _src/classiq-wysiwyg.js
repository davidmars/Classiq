require("./wysiwyg.less");
require("./cq-tip/cq-tip.less");
import  Wysiwyg from "./Wysiwyg";
Pov.onBodyReady(function(){
    console.log("ready (wysiwyg)");
    /**
     *
     * @type {Wysiwyg}
     */
    window.wysiwyg=new Wysiwyg();
});







