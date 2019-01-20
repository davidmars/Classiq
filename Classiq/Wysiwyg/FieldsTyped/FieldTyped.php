<?php

namespace Classiq\Wysiwyg\FieldsTyped;


use Classiq\Models\JsonModels\ListItem;
use Classiq\Wysiwyg\Field;
use Classiq\Wysiwyg\JsonModelField;
use Pov\Html\Trace\Attributes;
use Pov\Html\Trace\HtmlTag;

/**
 * Les classes héritées de celle-ci sont des champs wysiwyg dont le type de champ a été défini.
 * @package Wysiwyg\FieldsTyped
 */
abstract class FieldTyped
{
    /**
     * @var Field|JsonModelField
     */
    public $field;

    /**
     * FieldTyped constructor.
     * @param $field
     */
    public function __construct($field)
    {
        $this->field=$field;
        if($this->field->wysiwyg->active){
            $this->attr()["context-menu-is-list"]="false";
        }
    }

    /**
     * Contient les options du champs
     * @var array
     */
    public $options=[];

    /**
     * Retourne le record d'où ce champ est iss
     * @return \Classiq\Models\Classiqmodel
     */
    public function mainRecord(){
        if($this->field instanceof JsonModelField){
            return $this->field->wysiwyg->listItem->record;
        }else{
            return $this->field->wysiwyg->model;
        }
    }

    /**
     * @var Attributes
     */
    protected $_attr;

    /**
     * @return \Pov\Html\Trace\Attributes
     */
    public function attr(){
        if(!$this->_attr){
            $this->_attr=new Attributes();
            if($this->field->wysiwyg->active) {
                $this->_attr["wysiwyg-var"] = $this->field->varName;
                $this->_attr["wysiwyg-type"]=$this->mainRecord()->modelType();
                $this->_attr["wysiwyg-id"]=$this->mainRecord()->id;
            }
        }
        return $this->_attr;
    }

    /**
     * Permet d'obtenir un tag Html complet
     * @param string $tag
     * @return HtmlTag|string
     */
    public function htmlTag($tag="span"){

        $tag=new HtmlTag($tag,$this->field->value());
        if($this->field->wysiwyg->active){
            $this->attr()["cq-field-options"]=json_encode($this->options);
        }
        $tag->setAttributes($this->attr());
        return $tag;
    }

    //--------------evenements quand le champ change--------------------------


    /**
     * Une fois le champ enregistré va faire un refresh
     * @param string $selector Quoi rafraichir? code jquery selector  $('#monId') | $(this).closest('.ma-classe') etc...
     * @return $this
     */
    public function onSavedRefresh($selector=""){
        if($this->field->wysiwyg->active) {
            $this->attr()["wysiwyg-on-saved-action"] = "refresh";
            $this->attr()["wysiwyg-on-saved-action-selector"] = $selector;
        }
        return $this;
    }

    /**
     * Une fois le champ enregistré va faire un refresh sur la fenêtre de config et le(s) template(s) qui a/ont la même clé
     * @param ListItem $listItem L'objet list item à rafraichir
     * @param bool $refreshConfig mettre sur false pour ne pas rafraichir la fenetre de config
     * @return $this
     */
    public function onSavedRefreshListItem(ListItem $listItem,$refreshConfig=true){
        if($this->field->wysiwyg->active){
            $selector="$('[list-item-key=\'".$listItem->key."\']')"; //le(s) template(s)
            if($refreshConfig){
                $selector.=".add($(this).closest('#config-loader'))";//la fenêtre de config
            }
            $this->onSavedRefresh($selector);
        }
        return $this;
    }
    /**
     * Une fois le champ enregistré va faire un refresh du navigateur
     * @return $this
     */
    public function onSavedReload(){
        if($this->field->wysiwyg->active) {
            $this->attr()["wysiwyg-on-saved-action"] = "reload";
        }
        return $this;
    }

    /**
     * Ajoute un attribut focus-prevent-refresh qui dit à Pov de ne pas rafraichir la vue si l'élément est focused
     * @return $this
     */
    public function preventFocusedRefresh(){
        $this->attr()["focus-prevent-refresh"] = "1";
        return $this;
    }

}