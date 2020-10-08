<?php

namespace Classiq\Wysiwyg\FieldsTyped;

use Classiq\Wysiwyg\JsonModelField;
use Pov\Html\Trace\HtmlTag;

/**
 * Class FieldString
 * @package Classiq\Wysiwyg
 */
class FieldString extends FieldTyped
{

    public function mandatory(){
        return $this;
    }



    /**
     * Dit que le champ est traduit dans la langue donnée
     * @param string $langCode L'identifiant de langue
     */
    public function isTranslated($langCode){
        $this->attr()["data-lang"]=$langCode;
        return $this;
    }

    /**
     * Définit un placeholder
     * @param $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        if($this->field->wysiwyg->active){
            $this->attr()["placeholder"]=$placeholder;
        }
        return $this;

    }/**
     * Définit les boutons pour Medium editor
     * @param string[] $buttons exemple ["h1","h2","bold","italic","anchor","select-record","removeFormat"]
     * @return $this
     */
    public function setMediumButtons($buttons)
    {
        if($this->field->wysiwyg->active){
            $this->options["mediumButtons"]=pov()->utils->array->fromString($buttons);
        }
        return $this;

    }

    /**
     * Permet de définir quel type de records il est possible d'ajouter en href (n'a d'incidence que sur les champs RichText)
     * @param string $recordsTypes Les types de records séparé par une virgune page,custommodel,etc
     * @return $this
     */
    public function setSelectableRecordTypes($recordsTypes){
        if($this->field->wysiwyg->active){
            $this->options["selectableRecordsTypes"]=strtolower(implode(",",pov()->utils->array->fromString($recordsTypes)));
        }
        return $this;
    }

    /**
     * Dit que le champ texte peut être rechargé même si le focus de la souris est toutjours dessus
     */
    public function dontCareFocus(){
        $this->attr()["refresh-dont-care-focus"]="true";
        return $this;
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


    //--------------------------final methods------------------------------------------------

    /**
     * Permet d'obtenir un tag Html
     * @param string $tag
     * @param bool $displayIfEmpty Si false et qu'on est pas en wysiwyg et que le texte est vide ne renverra pas de tag
     * @param bool $fixHtml
     * @return HtmlTag|string
     * @throws \Pov\PovException
     */
    public function htmlTag($tag="span",$displayIfEmpty=true,$fixHtml=false){
        $tag=parent::htmlTag($tag);
        $v=$this->field->value(true,$this->defaultValue);
        if($fixHtml){
            $v=pov()->utils->string->fixHtml($v);
        }
        $tag->setInnerHTML($v);
        $tag->isRenderable=$displayIfEmpty || $tag->getInnerHTML() || $this->field->wysiwyg->active;
        return $tag;
    }

    /**
     * Permet d'obtenir un tag html INPUT
     * @param string $type L'attribut type qui va avec la balise input (text, number, date etc...)
     * @param string $placeholder
     * @param string $class Classe css (fld par défaut)
     * @return HtmlTag|string
     */
    public function input($type="text", $placeholder="...", $class="fld"){
        $tag=new HtmlTag("input");
        $this->attr()["type"]=$type;
        $this->attr()["placeholder"]=$placeholder;
        if(in_array($type,["date","datetime"])){
            $this->attr()["placeholder"]="";
        }
        $tag->setAttributes($this->attr());
        $tag->addClass($class);
        $this->attr()["value"]=$this->field->value(true,$this->defaultValue);
        return $tag;
    }


    /**
     * Permet d'obtenir un tag html SELECT
     * @param array $options liste des options possibles
     * @param string $placeholder
     * @param string $class Classe css (.fld par défaut)
     * @return HtmlTag|string
     */
    public function select($options=[], $placeholder="...", $class="fld"){
        $tag=new HtmlTag("select");
        $this->attr()["placeholder"]=$placeholder;
        $tag->setAttributes($this->attr());
        $tag->addClass($class);
        if(!pov()->utils->array->isAssociative($options)){
            //convertit en associatif
            $new=[];
            foreach ($options as $o){
                $new[$o]=$o;
            }
            $options=$new;
        }

        $optionTags=[];
        $placeholder=new HtmlTag("option",$placeholder);
        //$placeholder->setAttribute("disabled","disabled");
        $optionTags[]=$placeholder;

        foreach ($options as $k=>$v){
            $tagO=new HtmlTag("option",$k);
            $tagO->setAttribute("value",$v);
            if($this->field->value(true,$this->defaultValue)==$v){
                $tagO->setAttribute("selected","selected");
            }
            $optionTags[]=$tagO;
        }

        $inner="";
        foreach ($optionTags as $o){
            $inner.=$o."\n";
        }
        $tag->setInnerHTML($inner);
        return $tag;
    }
    /**
     * Permet d'obtenir un tag html SELECT
     * @param array $options liste des options possibles
     * @param string $placeholder
     * @param string $class Classe css (.fld par défaut)
     * @return HtmlTag|string
     */
    public function suggest($options=[], $placeholder="...", $class="fld"){
        $listName=uniqid();
        $tag=new HtmlTag("div");

        $input=new HtmlTag("input");
        $this->attr()["placeholder"]=$placeholder;
        $this->attr()["type"]="text";
        $this->attr()["list"]=$listName;
        $this->attr()["value"]=$this->field->value(true,$this->defaultValue);
        $input->setAttributes($this->attr());
        $input->addClass($class);
        if(!pov()->utils->array->isAssociative($options)){
            //convertit en associatif
            $new=[];
            foreach ($options as $o){
                $new[$o]=$o;
            }
            $options=$new;
        }
        $list=new HtmlTag("datalist");
        $list->setAttribute("id",$listName);

        $optionTags=[];
        $placeholder=new HtmlTag("option",$placeholder);
        //$placeholder->setAttribute("disabled","disabled");
        $optionTags[]=$placeholder;

        foreach ($options as $k=>$v){
            $tagO=new HtmlTag("option",$k);
            $tagO->setAttribute("value",$v);
            if($this->field->value(true,$this->defaultValue)==$v){
                $tagO->setAttribute("selected","selected");
            }
            $optionTags[]=$tagO;
        }

        $inner="";
        foreach ($optionTags as $o){
            $inner.=$o."\n";
        }
        $list->setInnerHTML($inner);
        $tag->setInnerHTML($input."".$list);


        return $tag;
    }
    /**
     * Permet d'obtenir un bouton pour définir une valeur
     * @param array $options liste des options possibles
     * @param string $placeholder
     * @param string $class Classe css (.fld par défaut)
     * @return HtmlTag|string
     */
    public function buttonValueSetter($value){
        $tag=new HtmlTag("button",$value);
        $this->attr()["contenteditable"]=null;
        $this->attr()["value"]=$value;

        $tag->setAttributes($this->attr());
        if($this->field->value(true,$this->defaultValue)===$value){
            $this->attr()["selected"]="selected";
        }
        return $tag;
    }

    /**
    Permet d'obtenir un tag html TEXTAREA
     * @param string $placeholder
     * @param string $class
     * @return HtmlTag|string
     */
    public function textarea($placeholder="...",$class="fld"){
        $this->attr()["placeholder"]=$placeholder;
        $tag=$this->htmlTag("textarea");
        $tag->addClass($class);
        return $tag;
    }
    /**
    Permet d'obtenir un tag html div.fld.textarea Autrement dit un champ texte enrichi qui ressemble à un textarea
     * @param string $placeholder
     * @param string $class
     * @return HtmlTag|string
     */
    public function textareaRichText($placeholder="...",$class="fld fake-textarea"){
        $this->attr()["placeholder"]=$placeholder;
        $tag=$this->htmlTag("div");
        $tag->addClass($class);
        return $tag;
    }

}