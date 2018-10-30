<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 03/10/2018
 * Time: 07:02
 */

namespace Classiq\Models;

use Pov\Image\ImgUrl;

/**
 * Trait WithFieldThumbnailTrait
 * @package Classiq\Models
 * @property string $thumbnail uid du Filerecord correspondant
 */
trait WithFieldThumbnailTrait
{
    /**
     * Pour jouer avec la thumbnail
     * @param string $defaultImage Url d'une image par default
     * @param bool $asRecord renverra le fileRecord si défini sur true, sinon un ImgUrl pour travailler l'image
     * @return ImgUrl|Filerecord
     */
    public function thumbnail($asRecord=false,$defaultImage="")
    {
        $url="";
        if($defaultImage){
            $url=$defaultImage;
        }
        /** @var Filerecord $file */
        $file=Filerecord::getByUid($this->thumbnail);
        if($asRecord){
            return $file;
        }
        if($file){
            $url=$file->localPath();
        }
        return pov()->img($url);
    }
}