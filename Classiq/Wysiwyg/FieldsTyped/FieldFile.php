<?php
namespace Classiq\Wysiwyg\FieldsTyped;

use Classiq\Models\Filerecord;
use Pov\MVC\View;

/**
 * Class FieldImage
 * @package Classiq\Wysiwyg
 */
class FieldFile extends FieldTyped
{
    use TraitUploadOptions;

    /**
     * Permet d'obtenir un tag html INPUT type = file accompagné d'une preview du recordfile
     * @return View
     */
    public function button(){
        return View::get("cq-fields/input-file",$this);
    }

    /**
     * Le record du fichier
     * @return Filerecord
     */
    public function fileRecord()
    {
        return $this->field->valueAsRecord();
    }



}