<?php

namespace Classiq\Models;

/**
 * Représente un enregistrement super simple où on peut stocker des trucs
 * @package Classiq\Models
 *
 * @method static Data getByName($name, $create=false)
 * @method Data box()
 * @property string jsonvars
 *
 */
class Data extends Classiqmodel
{

    static $icon="cq-database";

    /**
     * à l'enregistrement
     */
    public function update() {
        parent::update();
    }





}