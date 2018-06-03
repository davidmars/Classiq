<?php

namespace Classiq\Wysiwyg\FieldsTyped;
use Pov\Html\Trace\HtmlTag;
use Pov\MVC\View;

/**
 * La Classe FieldGeoloc permet d'afficher des champs dont la valeur sous la forme lattitude;longitude
 * @package Classiq\Wysiwyg
 */
class FieldGeoloc extends FieldTyped
{

    /**
     * Permet d'obtenir une google map
     * @return HtmlTag|string
     */
    public function googleMap(){

        $div=new HtmlTag("div");
        $div->setAttributes($this->attr());
        $div->addClass("focus-prevent-refresh");

        $latlng=explode(";",$this->field->value(true));
        if(count($latlng)!=2){
            $latlng=["0","0"];
        }

        $content=View::get("cq-fields/cq-field-google-map/cq-field-google-map",$latlng);
        $div->setInnerHTML($content->render());

        return $div;
    }
}