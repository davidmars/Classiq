<?php
namespace Classiq\Wysiwyg;

use Classiq\Models\JsonModels\ListItem;
use Pov\Html\Trace\Attributes;
use Pov\Html\Trace\HtmlTag;

/**
 * Point de départ pour éditer en Wysiwyg le modèle
 */
class WysiwygListitem
{
    use WysiwygTrait;

    /**
     * @var ListItem
     */
    public $listItem;

    /**
     * @var Attributes
     */
    private $_attr;

    /**
     * Wysiwyg constructor.
     * @param $active
     * @param ListItem $listItem
     */
    public function __construct($listItem, $active=true){
        $this->active=$active && cq()->wysiwyg();
        $this->listItem=$listItem;
    }

    /**
     * Pour éditer un champ
     *
     * @param $varName
     *
     * @return Field
     */
    public function field($varName){
        $varName=preg_replace("/_lang$/","_".the()->project->langCode,$varName);
        return new JsonModelField($this,$varName);
    }

    /**
     * Les attribts à placer sur la balise racine d'un listItem
     * @return Attributes
     */
    public function attr(){
        if(!$this->_attr){
            $this->_attr=new Attributes();
            if($this->active) {
                $this->_attr["list-item-path"] = $this->listItem->path();
                $this->_attr["list-item-key"] = $this->listItem->key;
                $this->_attr["data-pov-v-path"] = $this->listItem->path();
                if($this->listItem->uid()){
                    $this->_attr["data-pov-vv-uid"] = $this->listItem->uid();
                }
            }
        }
        return $this->_attr;
    }

    /**
     * @return $this
     */
    public function openConfigOnCreate(){
        $this->attr()["wysiwyg-open-config"]="true";
        return $this;
    }

    /**
     * Permet d'obtenir un tag complet
     * @param string $tag
     * @return HtmlTag|string
     */
    public function htmlTag($tag="div"){
        $tag=new HtmlTag($tag,"");
        $tag->setAttributes($this->attr());
        return $tag;
    }



}