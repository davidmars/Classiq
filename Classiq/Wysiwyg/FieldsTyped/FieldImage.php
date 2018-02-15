<?php

namespace Classiq\Wysiwyg\FieldsTyped;
use Pov\Image\ImgUrlHtml;

/**
 * Class FieldImage
 * @package Classiq\Wysiwyg
 */
class FieldImage extends FieldFile
{

    use FieldContextMenuOptionsTrait;


    protected $displayIfEmpty=false;

    /**
     * Pour redimentionner l'image, la compresser etc...
     * @return ImgUrlHtmlWysiwyg
     */
    public function format()
    {
        //force l'affichage si on est en wysiwyg
        if($this->field->wysiwyg->active){
            $this->displayIfEmpty=true;
        }

        $record=$this->fileRecord();

        $src="";
        if($record){
            $src=$record->localPath();
        }
        $img=ImgUrlHtmlWysiwyg::inst($src);
        $img->displayIfEmpty($this->displayIfEmpty);
        foreach ($this->attr() as $k=>$v){
            $img->attr()[$k]=$v;
        }
        return $img;
    }

    /**
     * Permet d'afficher une image vide même si l'image n'est pas définie (n'impacte que le mode non Wysiwyg)
     * @param bool $display si true affichera une image vide si l'image n'est pas définie, si false ne renverra rien
     * @return $this
     */
    public function displayIfEmpty($display)
    {
        $this->displayIfEmpty=$display;
        return $this;
    }
}