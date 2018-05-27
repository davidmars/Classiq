<?php

namespace Classiq\Wysiwyg\FieldsTyped;
use Pov\Html\Trace\HtmlTag;

/**
 * La Classe FieldBoolean permet d'afficher des champs dont la valeur est 0 ou 1
 * @package Classiq\Wysiwyg
 */
class FieldBoolean extends FieldTyped
{

    /**
     * Permet d'obtenir un tag html input type checkbox
     * @param string $label Le texte à afficher
     * @return HtmlTag|string
     */
    public function checkbox($label=""){
        $uid=uniqid("labelid");
        $div=new HtmlTag("div");
        $div->addClass("fld-chk");

        $labelTag=new HtmlTag("label",$label?$label:$this->field->varName);
        $labelTag->setAttribute("for",$uid);

        $input=new HtmlTag("input");
        $input->setAttributes($this->attr());
        $input->setAttribute("id",$uid);
        $input->setAttribute("wysiwyg-data-type","boolean");
        $input->setAttribute("type","checkbox");
        $input->setAttribute("value","1");
        if($this->field->value(true,"0")=="1"){
            $input->setAttribute("checked","checked");
        }
        $div->setInnerHTML($labelTag."\n".$input);
        return $div;
    }
}