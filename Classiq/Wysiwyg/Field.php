<?php

namespace Classiq\Wysiwyg;


use Classiq\Models\Classiqmodel;
use Classiq\Wysiwyg\FieldsTyped\FieldBoolean;
use Classiq\Wysiwyg\FieldsTyped\FieldFile;
use Classiq\Wysiwyg\FieldsTyped\FieldImage;
use Classiq\Wysiwyg\FieldsTyped\FieldListJson;
use Classiq\Wysiwyg\FieldsTyped\FieldListString;
use Classiq\Wysiwyg\FieldsTyped\FieldRecordPicker;
use Classiq\Wysiwyg\FieldsTyped\FieldString;
use Pov\Html\Trace\Attributes;
use Pov\Html\Trace\HtmlTag;
use Pov\MVC\View;
use Pov\PovException;
use Pov\Utils\StringUtils;

/**
 * Un champ éditable en Wysiwyg
 * @package Classiq\Wysiwyg
 */
class Field
{
    /**
     * @var string La variable à traiter
     */
    public $varName="";
    /**
     * @var Wysiwyg|WysiwygListitem
     */
    public $wysiwyg;

    /**
     * Field constructor.
     * @param Wysiwyg $wysiwyg
     * @param string $varName
     */
    public function __construct($wysiwyg, $varName){
        $this->wysiwyg=$wysiwyg;
        $this->varName=$varName;

    }

    /**
     * Pour rendre le champ editable sous forme de texte
     * @param string $format
     * @return FieldString
     */
    public function string($format=StringUtils::FORMAT_NO_HTML_SINGLE_LINE){

        $f= new FieldString($this);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        if($this->wysiwyg->active){
            $f->attr()["contenteditable"]="true";
            $f->attr()["wysiwyg-data-type"]="string";
            $f->attr()["wysiwyg-data-type-format"]=$format;
            if($format===pov()->utils->string::FORMAT_HTML){
                $f->attr()["cq-field-rich-text"]=".";
            }
        }

        return $f;
    }
    /**
     * Pour rendre le champ editable sous forme de liste de choix texte
     * @return FieldListString
     */
    public function listString()
    {
        $f= new FieldListString($this);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        if($this->wysiwyg->active){
            $f->attr()["contenteditable"]="true";
            $f->attr()["wysiwyg-data-type"]="list-string";
        }

        return $f;
    }
    /**
     * Pour rendre le champ editable sous forme de booléain
     * @param string $format
     * @return FieldBoolean
     */
    public function bool(){
        $f= new FieldBoolean($this);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        if($this->wysiwyg->active){
            $f->attr()["wysiwyg-data-type"]="boolean";
        }
        return $f;
    }

    /**
     * Renvoie les erreur sur le champ (si il y en a)
     * @return null
     * @throws \Exception
     */
    public function getError(){
        $this->varName;
        if(!$this instanceof JsonModelField){
            if(isset($this->wysiwyg->model->getErrors()[$this->varName])){
                return $this->wysiwyg->model->getErrors()[$this->varName];
            }
        }
        return null;
    }

    /**
     * Pour rendre le champ editable sous forme de selecteur de record
     * @param string $recordsTypes types de records possibles séparés par une virgule
     * @param bool $multiple definir sur false pour qu'on ne puissse selectionner qu'un seul record
     * @return FieldRecordPicker
     */
    public function recordPicker($recordsTypes, $multiple=true){
        $f= new FieldRecordPicker($this,$recordsTypes,$multiple);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        $f->multiple=$multiple;
        if($this->wysiwyg->active){
            $f->attr()["wysiwyg-data-type"]="records";
            //définit le type de record possibles
        }
        return $f;
    }

    /**
     * Pour rendre un champ éditable sous forme de liste réorganisable
     * @param string $itemTemplates Liste des templates path possibles séparés par une virgule
     * @return FieldListJson
     */
    public function listJson($itemTemplates="")
    {
        $testTemplates=pov()->utils->array->fromString($itemTemplates);
        foreach ($testTemplates as $template){
            if(!View::isValid($template)){
                throw new PovException($template." n'est pas valide");
            }
        }

        $itemTemplates=pov()->utils->array->toString($itemTemplates);
        $f= new FieldListJson($this);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        if($this->wysiwyg->active){
            $f->attr()["wysiwyg-data-type"]="list";//peut être que "list-json" serait plus approprié
            $f->attr()["cq-blocks"]="true";
            $f->attr()["wysiwyg-item-templates"]="$itemTemplates";
        }
        return $f;
    }

    /**
     * Pour rendre le champ éditable sous forme d'image
     * @return FieldImage
     */
    public function image(){
        $f= new FieldImage($this);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        if($this->wysiwyg->active){
            $f->attr()["wysiwyg-data-type"]="image";
            $f->attr()["wysiwyg-image"]=".";
        }
        $f->contextMenuPosition(POSITION_CENTER);
        return $f;
    }
    /**
     * Pour rendre le champ editable sous forme de fichier
     * @return FieldFile
     */
    public function file(){
        $f= new FieldFile($this);
        $f->attr()["wysiwyg-field-error"]=$this->getError();
        if($this->wysiwyg->active){
            $f->attr()["wysiwyg-data-type"]="file";
        }
        return $f;
    }

    /**
     * @param bool $forceString
     * @param null $ifNull Valeur à renvoyer si null
     * @return string la valeur du champ
     * @throws PovException
     */
    public function value($forceString=false,$ifNull=null){
        $v=$this->wysiwyg->model->getValue($this->varName,$forceString);
        if(!$v){
            $v=$ifNull;
        }
        return $v;
    }

    /**
     * @return Classiqmodel[] Renvoie une liste de records en partant du principe que la valeur du champ est une liste d'uids
     */
    public function valueAsRecords(){
        return $this->wysiwyg->model->getValueAsRecords($this->varName);
    }
    /**
     * @return Classiqmodel Renvoie le record en partant du principe que la valeur du champ est un uid
     */
    public function valueAsRecord(){
        return $this->wysiwyg->model->getValueAsRecord($this->varName);
    }
    /**
     * Retourne la valeur du champ sous forme de tableau de chaines
     * partant du principe que le contenu est une chaine séparée par des points virgules
     *
     * @return array La valeur du champ sous forme de tableau.
     */
    public function valueAsStringArray(){
        $string=$this->value(true,"");
        return explode(";",$string);
    }




}