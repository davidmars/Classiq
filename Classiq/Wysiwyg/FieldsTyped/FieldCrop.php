<?php

namespace Classiq\Wysiwyg\FieldsTyped;
use Pov\Html\Trace\HtmlTag;
use Pov\MVC\View;

/**
 * La Classe FieldCrop permet d'enregistrer une région de crop sur une image
 * @package Classiq\Wysiwyg
 */
class FieldCrop extends FieldTyped
{

    private $imgSrc;
    private $ratio;
    private $ratioDomSelector;

    public function __construct($field,$imgSrc)
    {
        parent::__construct($field);

        $this->imgSrc=$imgSrc;
    }

    /**
     * Permet de définir un ratio fixe
     * @param $height
     * @param $width
     * @return FieldCrop
     */
    public function setRatio($width,$height){
        $this->ratioDomSelector=null;
        $this->ratio=$height/$width;
        return $this;
    }

    /**
     * Permet de définir un ratio fixe à partir d'un selecteur DOM (Attention au responsive)
     * @param string $domSelector
     * @return FieldCrop
     */
    public function setRatioDomSelector($domSelector){
        $this->ratio=null;
        $this->ratioDomSelector=$domSelector;
        return $this;
    }


    /**
     * Permet d'obtenir une interface utilisateur pour cropper
     * @return HtmlTag|string
     */
    public function cropper(){

        $div=new HtmlTag("div");

        $div->setAttributes($this->attr());
        $div->setAttribute("value",$this->field->value(true,""));
        $div->setAttribute("wysiwyg-data-type","crop");
        $vv=[];
        $vv["imgUrl"]=$this->imgSrc;
        $vv["ratio"]=$this->ratio;
        $vv["domRatioSelector"]=$this->ratioDomSelector;
        $vv["value"]=$this->field->value(true,"");


        $content=View::get("cq-fields/cq-field-crop/cq-field-crop",$vv);
        $div->setInnerHTML($content->render());


        return $div;
    }
}