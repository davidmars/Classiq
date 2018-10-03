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
 * @property Filerecord $thumbnail Objet de fichier
 */
trait WithFieldThumbnailTrait
{
    /**
     * Pour jouer avec la thubnail
     * @return ImgUrl|Filerecord
     */
    public function thumbnail($asRecord=false)
    {
        $url="";
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