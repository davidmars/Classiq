
/**
 * Ce fichier est à inclure dans votre front.
 *
 * Il ajoute les librairies suivantes au projet...
 * - GSAP TweenMax est installé
 * - GSAP ScrollToPlugin est installé
 * - GSAP Draggable est installé
 * - perfect-scrollbar est installé
 *
 * Ce script fait les choses suivantes :
 * - initialise la navigation ajax de pov-2018
 * - initialise les cqEventsListener (qui permettent de faire fonctionner les attributs cq-on="monAction()"
 * - initialise un listener window.povSSE
 * - SSE : si l'utilisateur fait un login rechergera la page.
 * - SSE : fait un console.log si on reçoit un EVENTS.SSE_DEBUG_LOG.
 */
import CqEventsListener from "./CqEventsListener";
require("gsap/TweenMax");
require("gsap/ScrollToPlugin");
require("gsap/Draggable");
window.PerfectScrollbar=require("perfect-scrollbar/dist/perfect-scrollbar.min");

Pov.onBodyReady(function(){
    //TweenMax config
    CSSPlugin.defaultTransformPerspective = 800;
    //initialise les liens ajax
    window.pov.history.init();
    //écoute le serveur
    window.povSSE=new window.pov.PovSSE(window.pov.api.listenSSE());
    //qd user login recharge
    window.povSSE.on(EVENTS.SSE_USER_LOGIN,function(e){
        window.povSSE.close();
        document.location.reload(true);
    });
    window.povSSE.on(EVENTS.SSE_DEBUG_LOG,function(e){
        console.log(EVENTS.SSE_DEBUG_LOG,e);
    });
    //initialise les events cq-on-etc="action()"
    window.cqEventsListener=new CqEventsListener();
    window.cqEventsListener.addObject(window);
});