<?php

namespace Classiq\Models;
use Classiq\Models\JsonModels\ListItem;

/**
 * Représente une liste d'éléments de navigation, des liens internes, externes
 * @package Classiq\Models
 *
 * @method static Nav getByName($name, $create=false)
 * @method Nav box()
 * @property string jsonitems
 * @property string jsonvars
 *
 */
class Nav extends Classiqmodel
{

    static $icon="cq-list";

    public $items=[];
    /**
     * Moyen le plus simple d'obtenir les items avec quelques outils en plus pour que ce soit pratique
     * @return ListItem[]
     */
    public function items()
    {
        return ListItem::getList("items",$this);
    }

    /**
     * à l'enregistrement
     */
    public function update() {
        parent::update();
    }





}