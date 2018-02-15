<?php


namespace Classiq\Wysiwyg\FieldsTyped;


use Pov\Image\ImgUrlHtml;

class ImgUrlHtmlWysiwyg extends ImgUrlHtml
{
    /**
     * Méthode statique pour obtenir une nouvelle Image
     * @param null|string $src url de l'image à traiter
     * @return ImgUrlHtmlWysiwyg
     */
    public static function inst($src=null){
        $i= new ImgUrlHtmlWysiwyg();
        if($src){
            return $i->fromImage($src);
        }
        return $i;
    }

    public function htmlTag($class = "", $alt = "",$widthHeightAttributes=false)
    {
        $tag= parent::htmlTag($class, $alt);
        $tag->setAttribute("wysiwyg-image-format",$this->getFormat());
        if($this->_preserveGif){
            $tag->setAttribute("wysiwyg-image-preserve-gif","true");
        }
        if($widthHeightAttributes){
            $tag->setAttribute("width",$this->_width."px");
            $tag->setAttribute("height",$this->_height."px");
        }

        return $tag;
    }

    private function getFormat(){
        $im=clone $this;
        $im->_preserveGif=false;
        $im->_source="";
        return $im->__toString();
    }
}