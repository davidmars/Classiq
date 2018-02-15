<?php

namespace Classiq\Wysiwyg;


use Classiq\Models\Classiqmodel;


/**
 * Un champ éditable en Wysiwyg
 * @package Classiq\Wysiwyg
 *
 * @property string $varName Le nom de la variable ressemblera à monChampMysql.maCleJson.maVariable
 *
 */
class JsonModelField extends Field
{

    /**
     * @var WysiwygListitem
     */
    public $wysiwyg;

    /**
     * @var Classiqmodel;
     */
    protected $record;


    /**
     * Field constructor.
     * @param WysiwygListitem $wysiwyg
     * @param string $varName
     */
    public function __construct($wysiwyg, $varName){
        $this->wysiwyg=$wysiwyg;
        if($this->wysiwyg->listItem->record){
            $this->record=$this->wysiwyg->listItem->record;
            $this->varName=$this->wysiwyg->listItem->fieldName.".".$this->wysiwyg->listItem->key.".".$varName;
        }
    }

    /**
     * Renvoie la valeur du champ.
     * @param bool $forceString
     * @param null $ifNull valeur à renvoyer si null
     * @return string la valeur du champ
     * @throws \Pov\PovException
     */
    public function value($forceString=false,$ifNull=null){
        $v="";
        if($this->record){
            $v= $this->record->getValue($this->varName,$forceString);
        }
        if(!$v){
            $v=$ifNull;
        }
        return $v;
    }

    /**
     * @return Classiqmodel[] Renvoie une liste de records en partant du principe que la valeur du cham est une liste d'uids
     */
    public function valueAsRecords(){
        if($this->record){
            return $this->record->getValueAsRecords($this->varName);
        }
        return [];
    }
    /**
     * @return Classiqmodel Renvoie le record en partant du principe que la valeur du champ est un uid
     */
    public function valueAsRecord(){
        return $this->record->getValueAsRecord($this->varName);
    }

}