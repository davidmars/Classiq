<?php

namespace Classiq\Models;

/**
 * Pour obtenir des infos sur un type de modèle
 * @package Classiq\Models
 */
class ClassicModelSchema
{
    public $type;
    /**
     * @var Classiqmodel
     */
    private static $insts=[];



    /**
     * @param string $type Le type de record
     * @return Classiqmodel
     */
    private static function inst($type){
        if(!is_string($type)){
            if($type instanceof Classiqmodel){
                $type=$type::_type();
            }
        }
        $type=strtolower($type);
        if(!isset(self::$insts[$type])){
            self::$insts[$type]=db()->dispense($type)->box();
        }
        return self::$insts[$type];
    }

    /**
     * @param string $type Le type de record
     * @var string identifiant SVG de l'icone associée à ce type de record
     */
    public static function icon($type){
        return self::inst($type)::$icon;
    }

    /**
     * Pour obtenir le nom du type de record lisible par les humains
     * @param string $type Le type de record
     * @param bool $plural si true renverra le nom au pluriel
     * @return string
     */
    public static function humanType($type,$plural=false){
        return self::inst($type)::humanType($plural);
    }

    /**
     * Pour obtenir une description du type de record
     * @param string $type Le type de record
     * @return string
     */
    public static function humanDescription($type)
    {
        return self::inst($type)::humanDescription();
    }

    /**
     * Nombre de records de ce type
     * @param string $type Le type de record
     * @return int
     */
    public static function count($type){
        $type=strtolower($type);
        return db()->count($type);
    }



}