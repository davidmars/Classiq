<?php

namespace Classiq\Models;
use Classiq\Models\JsonModels\ListItem;

/**
 * Représente un fichier
 * @package Classiq\Models
 *
 * @method static Filerecord getByName($name, $create=false)
 * @method Filerecord box()
 *
 * @property string $mime
 * @property string private $path Chemin vers le fichier dont la racine est relative au répertoire files/mon-projet/uploads
 * @property string $bytesize Poids du fichier en octets
 * @property string $fileidentifier md5 du fichier
 * @property int $image_width Largeur (si c'est une image)
 * @property int $image_height Hauteur (si c'est une image)
 *
 */
class Filerecord extends Classiqmodel
{

    public $items=[];

    public static $icon="cq-tests-file-empty";

    /**
     * @param bool $plural Si true retournera le nom du modèle au pluriel
     * @return string nom du type de record lisible par les humains
     */
    static function humanType($plural=false){
        if($plural){
            return "Fichiers";
        }
        return "Fichier";
    }


    /**
     * à l'enregistrement
     */
    public function update() {
        pov()->log->debug("update",[$this->_items]);
        if($this->unbox()->hasChanged("path")){
            $this->setFilePath($this->path);
        }
        parent::update();
    }

    /**
     * @return bool true si le fichier existe
     */
    public function isOk(){
        return file_exists($this->localPath());
    }

    /**
     * définit le path du fichier en relatif et met à jour les infos relatives (poids, mime etc..)
     * @param string $absoluteFilePath Le chemin absolut vers le fichier
     */
    public function setFilePath($absoluteFilePath){
        $this->path=str_replace(
            the()->fileSystem->uploadsPath,"",
            $absoluteFilePath
        );
        $this->path=trim($this->path,"/");
        $f=$this->localPath();
        if(is_file($f)){
            $this->mime=mime_content_type($f);
            $this->bytesize=filesize($f);
            $this->fileidentifier=md5_file($f);
            if($this->isImage()){
                list($width, $height) = getimagesize($f);
                if($width && $height){
                    $this->image_width=$width;
                    $this->image_height=$height;
                }
            }
        }

    }

    /**
     * Renvoie (et créé au besoin) un Filerecord à partir d'un fichier
     * @param string $file Chemin vers le fichier
     * @return Filerecord|null
     */
    public static function fromFile($file){
        if(!is_file($file)){
            return null;
        }
        $existing=self::getExistingByFile($file);
        if($existing){
            return $existing;
        }
        /** @var Filerecord $record */
        $record = self::getNew();
        $record->name = basename($file);
        $record->setFilePath($file);
        db()->store($record->unbox());
        return $record;

    }

    /**
     * Regarde si un fichier identique existe. Si oui le renvoie
     * @param string $file chemin vers un fichier
     * @return Filerecord|null
     */
    public static function getExistingByFile($file)
    {
        if(is_file($file)){
            $fileidentifier=md5_file($file);
            return self::getExistingByFileIdentifier($fileidentifier);
        }
        return null;
    }
    /**
     * Regarde si un fichier identique existe à partir de son fileidentifier. Si oui le renvoie
     * @param string $fileIdentifier le md5file
     * @return Filerecord|null
     */
    public static function getExistingByFileIdentifier($fileIdentifier)
    {
        /** @var Filerecord $existing */
        $existing=db()->findOne(self::modelTypeStatic(),"fileidentifier='$fileIdentifier'");

        if($existing){
            if( $existing->isOk()){
                return $existing->box();
            }else{
                db()->trash($existing->unbox());
            }
        }
        return null;
    }


    /**
     * Renvoie l'url http du fichier
     * @param bool $absolute si true renverra l'url avec http://etc...
     * @return string chemin à utiliser via http pour accéder au fichier
     */
    public function httpPath($absolute=false){
        return the()->fileSystem->uploadHttpPath($this->path,$absolute);
    }
    /**
     * Renvoie l'url dans le systèle de fichier
     * @return string chemin à utiliser via php pour accéder au fichier
     */
    public function localPath(){
        return trim(the()->fileSystem->uploadLocalPath($this->path),"/");
    }

    /**
     * @return string Le poids du fichier lisible par un humain sous la forme 10Mo 25ko 15Go...
     */
    public function humanFileSize($decimals=false)
    {
        return pov()->utils->files->humanFileSize($this->bytesize,$decimals);
    }

    /**
     * @return bool true si c'est une image
     */
    public function isImage()
    {
        return preg_match("/image/",$this->mime);
    }
    /**
     * @return bool true si c'est une video
     */
    public function isVideo()
    {
        return preg_match("/video/",$this->mime);
    }

    /**
     * Retourne de quoi jouer avec le fichier sous forme d'image
     * @return \Pov\Image\ImgUrlHtml
     */
    public function image()
    {
        return pov()->img($this->localPath());
    }


    /**
     * @return array
     */
    public function apiData()
    {
        $r=parent::apiData();
        $r["httpPathAbsolute"]=$this->httpPath(true);
        if($this->isImage()){
            $r["httpPathAbsolute200"]=$this->image()->sizeMax(200,200)->jpg()->href(true);
            $r["httpPathAbsolute400"]=$this->image()->sizeMax(400,400)->jpg()->href(true);
            $r["httpPathAbsolute800"]=$this->image()->sizeMax(800,800)->jpg()->href(true);
        }
        $r["httpPath"]=$this->httpPath();
        $r["localPath"]=$this->localPath();


        return $r;
    }




}