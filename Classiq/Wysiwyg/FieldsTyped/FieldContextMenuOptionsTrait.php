<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 02/01/2018
 * Time: 08:32
 */

namespace Classiq\Wysiwyg\FieldsTyped;

/**
 * Trait FieldContextMenuOptionsTrait Ajoute la possibilité de définir la taille du menu contextuel
 * @package Classiq\Wysiwyg\FieldsTyped
 */
trait FieldContextMenuOptionsTrait
{
    /**
     * Définit la taille des boutons du menu contextuel
     * @param string $size normal|small|big
     * @see contants SIZE_SMALL SIZE_etc...
     * @return $this
     */
    public function contextMenuSize($size="normal")
    {
        //Attention ne pas faire passer dans les options car c'est utilisé en css et en inspection du DOM
        $this->attr()["context-menu-size"]="$size";
        return $this;
    }
    /**
     * Positionne le menu contextuel au roll over
     * @param string $position voir les contantes POSITION_etc...
     * @return $this
     */
    public function contextMenuPosition($position=POSITION_TOP_RIGHT)
    {
        $this->attr()["context-menu-position"]="$position";
        return $this;
    }
}