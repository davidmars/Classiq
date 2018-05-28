<?php

namespace Classiq\Wysiwyg\FieldsTyped;

use Pov\Html\Trace\HtmlTag;

/**
 * En pratique ça donnera des champs checkbox
 * @package Classiq\Wysiwyg
 */
class FieldListString extends FieldTyped
{

    /**
     * Permet d'obtenir un div avec des checkboxes dedans
     * @param array $options liste des options possibles
     * @return HtmlTag|string
     */
    public function checkboxes($options=[]){
        /** @var HtmlTag $tag le tag principale*/
        $tag=new HtmlTag("div");
        $tag->setAttributes($this->attr());
        if(!pov()->utils->array->isAssociative($options)){
            //convertit en associatif
            $new=[];
            foreach ($options as $o){
                $new[$o]=$o;
            }
            $options=$new;
        }

        $optionTags=[];

        foreach ($options as $label=>$v){
            $uid=uniqid("labelid");

            $div=new HtmlTag("div");
            $div->addClass("fld-chk");

            $labelTag=new HtmlTag("label",$label);
            $labelTag->setAttribute("for",$uid);

            $input=new HtmlTag("input");
            $input->setAttribute("id",$uid);
            $input->setAttribute("type","checkbox");
            $input->setAttribute("value",$v);
            if($this->hasValue($v)){
                $input->setAttribute("checked","checked");
            }
            $div->setInnerHTML($labelTag."\n".$input);

            $optionTags[]=$div;
        }

        $inner="";
        foreach ($optionTags as $o){
            $inner.=$o."\n";
        }
        $tag->setInnerHTML($inner);
        return $tag;
    }

    /**
     * Teste si la valeur donnée est active
     * @param string $valueToTest la valeur à tester
     * @return bool true si la valeur donnée existe
     */
    private function hasValue($valueToTest){
        return in_array($valueToTest,$this->getValueAsArray());
    }

    /**
     * @return array Renvoie la valeur du champ sous forme de tableau
     */
    private function getValueAsArray(){
        return $this->field->valueAsStringArray();
    }




    /**
     * Permet d'obtenir un tag Html
     * @param string $tag
     * @return HtmlTag|string
     */
    public function htmlTag($tag="span"){
        $tag=parent::htmlTag($tag);
        $tag->setInnerHTML($this->field->value(true,$this->defaultValue));
        return $tag;
    }

    private $defaultValue="";

    /**
     * Permet de définir une valeur par défaut.
     * ça ne va pas enregistrer la valeur (sauf si le champ est enregistré par la suite), mais ça l'affichera dans le champ texte.
     * @param string $value
     * @return $this
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue=$value;
        return $this;
    }

}