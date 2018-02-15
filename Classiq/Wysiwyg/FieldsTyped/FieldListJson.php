<?php

namespace Classiq\Wysiwyg\FieldsTyped;

use Classiq\Models\JsonModels\ListItem;

//todo opti virer tous les attributs et mettre ça dans options

/**
 * Class FieldListJson
 * @package Classiq\Wysiwyg\FieldsTyped
 */
class FieldListJson extends FieldTyped
{

    use FieldContextMenuOptionsTrait;


    public function __construct($field)
    {
        parent::__construct($field);
        if(cq()->wysiwyg()){
            //ATTENTION utilisé par le context menu donc on le laisse en attribut
            $this->attr()["context-menu-is-list"]="true";
        }

    }

    /**
     *  Définit le message à afficher quand la liste est vide
     * @param string $message
     * @return FieldListJson
     */
    function blockPickerEmptyMessage($message="Commencez à insérer du contenu"){
        $this->options["blockPickerEmptyMessage"]=$message;
        return $this;
    }

    /**
     *  Définit le message à afficher quand on ajoute du contenu
     * @param string $message
     * @return FieldListJson
     */
    function blockPickerMessage($message="Cliquez pour ajouter du contenu"){
        $this->options["blockPickerMessage"]=$message;
        return $this;
    }


    /**
     * Permet de positionner les popins de ce champ sur l'axe X
     * @param string $positionX 0.5 pour le centre de l'écran 1 pour la droite
     * @return $this
     */
    public function popinXpos($positionX="0.25"){
        $this->options["popinXpos"]=$positionX;
        return $this;
    }
    /**
     * Permet de positionner les popins de ce champ sur l'axe Y
     * @param string $positionY
     * @see FieldListJson::popinXpos()
     * @return $this
     */
    public function popinYpos($positionY="0.25"){
        $this->options["popinYpos"]=$positionY;
        return $this;
    }

    /**
     * Renvoie la liste sous forme de tag html
     * @param string $tag Le type de tag html à utiliser ( div par defaut )
     * @return \Pov\Html\Trace\HtmlTag|string
     */
    public function htmlTag($tag = "div")
    {
        $htmlTag=parent::htmlTag($tag);
        $htmlTag->setInnerHTML($this->_htmlItems());
        return $htmlTag;
    }

    /**
     * Très peu utilisé. Retourne le code html des items (sans le container)
     * @return string le code html des items
     */
    public function _htmlItems(){
        $list=ListItem::getList($this->field->varName,$this->mainRecord());
        $content="";
        foreach ($list as $item){
            $content.=$item->view()->render();
        }
        return $content;
    }

    /**
     * Dit que la liste est horizontale (affecte les fleches mais pas plus en fait)
     * ATTENTION on utilise cet attribut dans le context menu aussi donc ne pas le mettre dans les option
     * @return $this
     */
    public function horizontal()
    {
        $this->attr()["list-horizontal"]="true";
        return $this;
    }



    /**
     * Dit que la liste ne contient que des records.
     * - Quand on appuie sur ça ouvre le selecteur de records en multiple et ça insère
     * - les records choisis
     * - les items sont toujours des ListItem mais ils ont tous un seul paramètre qui est targetUid
     * @param string $recordsTypes Liste des types de records possibles
     * @return $this
     */
    public function onlyRecords($recordsTypes=""){
        $this->options["onlyRecords"]="true";
        $this->options["onlyRecordsTypes"]=$recordsTypes;
        return $this;
    }
    /**
     * Dit que la liste ne contient que des records file image.
     * - Quand on appuie sur ça ouvre le selecteur de fichiers en multiple et ça insère
     * - les records choisis
     * - les items sont toujours des ListItem mais ils ont tous un seul paramètre qui est targetUid
     * @return $this
     */
    public function onlyImages(){
        $this->options["onlyIages"]="true";
        return $this;
    }

    /**
     * @param string $action seul "addItem" est pris en charge pour le moment
     * @return $this
     */
    public function onKeyEnter($action="addItem")
    {
        $this->options["keyEnterAction"]=$action;
        return $this;
    }

    /**
     * Fait en sorte que la liste ne soit jamais vide
     * @return $this
     */
    public function preventEmpty()
    {
        $this->options["preventEmpty"]=true;
        return $this;
    }


}