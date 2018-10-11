<?php

namespace Classiq\Wysiwyg\FieldsTyped;

use Classiq\Models\Classiqmodel;
use Classiq\Wysiwyg\JsonModelField;
use Pov\Html\Trace\HtmlTag;
use Pov\MVC\View;

/**
 * FieldRecordPicker est une classe qui représente un champ permettant de selectionner des records.
 * @package Classiq\Wysiwyg
 */
class FieldRecordPicker extends FieldTyped
{
    use TraitUploadOptions;

    public $recordsTypes="page";
    public $multiple=true;
    public $onlyFiles=false;

    public function __construct($field,$recordsTypes,$multiple)
    {
        parent::__construct($field);
        if(cq()->wysiwyg()){
            $this->attr()["wysiwyg-records-types"]=$recordsTypes;
            $this->attr()["wysiwyg-multiple"]=$multiple?"true":"false";
            $this->attr()["context-menu-is-list"]="true";
        }
        $this->recordsTypes=$recordsTypes;
        $this->multiple=$recordsTypes;

    }

    public function onlyFiles(){
        $this->onlyFiles=true;
        return $this;
    }

    /**
     * Permet d'obtenir un tag html BUTTON
     * @param string $placeholder
     * @param string $class Classe css (fld par défaut)
     * @return HtmlTag|string
     */
    public function button($placeholder="Choisissez",$class="cq-btn cq-th-white"){
        $tag=new HtmlTag("button");

        //texte dans le bouton
        $records=$this->getValueAsRecords();
        if($records){
            $text=[];
            foreach ($records as $r){
                $text[]=$r->name;
            }
            $text=implode(", ",$text);
        }else{
            $text=$placeholder;
        }
        $tag->setInnerHTML($text);

        $value=$this->field->value(true);

        $this->attr()["value"]=$value;
        $this->attr()["contenteditable"]=null;
        $tag->setAttributes($this->attr());
        $tag->addClass($class);
        return $tag;
    }

    /**
     * Un composant avec la liste des records et un bouton pour aller chercher de nouveaux.
     * @see cq-field-records/cq-field-records.php pour le template.
     * @return View
     */
    public function buttonRecord(){
        if($this->onlyFiles){
            return View::get("cq-field-records/cq-field-records-files",$this);
        }else{
            return View::get("cq-field-records/cq-field-records",$this);
        }

    }

    /**
     * Renvoie la valeur du champ sous forme de records
     * @return ClassiqModel[]
     */
    private function getValueAsRecords(){
        return $this->field->valueAsRecords();
    }



}