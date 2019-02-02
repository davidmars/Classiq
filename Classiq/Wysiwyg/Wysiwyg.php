<?php
namespace Classiq\Wysiwyg;

use Classiq\Models\Classiqmodel;
use Pov\Html\Trace\HtmlTag;


/**
 * Point de départ pour éditer en Wysiwyg le modèle
 */
class Wysiwyg
{
    use WysiwygTrait;


    /**
     * @var Classiqmodel Le modèle relatif (le record ou le bean en d'autres termes)
     */
    public $model;

    /**
     * Le Wysiwyg est activé globalement ou non?
     * @var bool
     */
    public static $enabled;


    /**
     * Wysiwyg constructor.
     * @param $active
     * @param Classiqmodel $model
     */
    public function __construct($model,$active=true){

        $this->active=$active && cq()->wysiwyg();
        $this->model=$model;
    }

    /**
     * Pour éditer un champ
     * @param $varName
     * @return Field
     */
    public function field($varName){
        $varName=preg_replace("/_lang$/","_".the()->project->langCode,$varName);
        return new Field($this,$varName);
    }

    /**
     * Renvoie un bouton visible en wysiwyg qui permet d'ouvrir une popin de config
     * @param string $template path vers la view qui sera affichée dans la fenêtre de config
     * @param string $svgIcon icone svg à utiliser
     * @return HtmlTag
     */
    public function buttonConfigWindow($template,$svgIcon="cq-edit"){
        $tag=new HtmlTag("button");
        $tag->isRenderable=$this->active;
        $tag->addClass("cq-btn circle small cq-th-white cq-hover-color-th");
        //$tag->setAttribute("href","#cq-show-config-popin");
        $uid=$this->model->uid();
        $tag->setAttribute("cq-on-click","wysiwyg.contextMenu.showConfig($template,$uid)");
        $tag->setAttribute("cq-config-path",$template);
        $tag->setAttribute("cq-config-uid",$this->model->uid());
        $tag->setInnerHTML(pov()->svg->use($svgIcon));
        return $tag;
    }






}

