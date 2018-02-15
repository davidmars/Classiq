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
    /**
     * @var string attribut accept placé sur le champ d'upload
     */
    public $mimeTypeAccept="";
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

    /**
     * permet de n'uploader QUE des images
     * @return $this
     */
    public function setMimeAcceptImagesOnly()
    {
        $this->mimeTypeAccept="accept='image/*'";
        return $this;
    }
    /**
     * permet de n'uploader QUE des vidéos
     * @return $this
     */
    public function setMimeAcceptVideoOnly()
    {
        $this->mimeTypeAccept="accept='video/*'";
        return $this;
    }
}