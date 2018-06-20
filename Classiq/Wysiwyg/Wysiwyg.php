<?php
namespace Classiq\Wysiwyg;

use Classiq\Models\Classiqmodel;


/**
 * Point de départ pour éditer en Wysiwyg le modèle
 */
class Wysiwyg
{
    use WysiwygTrait;


    /**
     * @var Classiqmodel Le modèle relatif (le record ou le bean en d'autres termes)
     */
    public $model;

    /**
     * Le Wysiwyg est activé globalement ou non?
     * @var bool
     */
    public static $enabled;


    /**
     * Wysiwyg constructor.
     * @param $active
     * @param Classiqmodel $model
     */
    public function __construct($model,$active=true){

        $this->active=$active && cq()->wysiwyg();
        $this->model=$model;
    }

    /**
     * Pour éditer un champ
     * @param $varName
     * @return Field
     */
    public function field($varName){
        $varName=preg_replace("/_lang$/",the()->project->langCode,$varName);
        return new Field($this,$varName);
    }






}

