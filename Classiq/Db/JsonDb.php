<?php

namespace Pov\Db;


use Pov\PovException;

/**
 * Une JsonDb est simplement une petite base de données qui permet de mapper des json dans un répertoire avec des classes
 *
 * @package Pov\Db
 */
class JsonDb  {
    /**
     * @var string Chemin vers le dossier de stockage des fichiers json
     */
    private $rootDir="";
    private static $_cacheDbs=[];

    /**
     * @param string $dbName Nom de la base de données dont résultera le répertoire de stockage
     * @param bool   $create si true et que le répertoire n'existe pas, ça le créera
     *
     * @throws \Exception
     */
    private function __construct($dbName,$create=false,$dir=null){
        if($dir){
            $this->rootDir=$dir;
        }else{
            $this->rootDir=the()->fileSystem->dbPath."/json-db/$dbName";
        }

        if(!is_dir($this->rootDir)){
            if($create){
                mkdir($this->rootDir,0777,true);
            }else{
                throw new \Exception("le répertoire ".$this->rootDir." n'existe pas");
            }
        }
        self::$_cacheDbs[$dbName]=$this;
    }

    /**
     * Pour obtenir une bdd par son nom.
     * @param string $dbName Nom de la base de données dont résultera le répertoire de stockage
     * @param bool   $create si true et que le répertoire n'existe pas, ça le créera
     *
     * @return \Pov\Db\JsonDb
     */
    public static function getDb($dbName,$create=false,$dir=null)
    {
        if(isset(self::$_cacheDbs[$dbName])){
            return self::$_cacheDbs[$dbName];
        }else{
            return new JsonDb($dbName,$create,$dir);
        }
    }

    /**
     * Renvoie le contenu json à partir d'un uid
     * @param string $uid
     *
     * @return mixed|null
     * @throws \Pov\PovException
     */
    private function getJsonByUid($uid){
        $file=$this->getFilePathByUid($uid);
        if(is_file($file)){
            return json_decode(file_get_contents($file));
        }else{
            return null;
        }
    }

    /**
     * Renvoie le chemin vers le json correspondant à l'uid
     * @param $uid
     *
     * @return string
     */
    private function getFilePathByUid($uid){
        return $this->rootDir."/$uid.json";
    }


    /**
     * Enregistre un modele
     *
     * @param      $object
     * @param null $uid
     *
     * @throws \Pov\PovException
     */
    public function saveModel($object,$uid=null)
    {
        $obj=json_decode(json_encode($object));
        $obj->jsonDbMappedClass=get_class($object);
        if($uid){
            $obj->uid=$uid;
        }
        if(!isset($obj->uid)){
            throw new PovException("Pas d'uid défini, on ne peut pas enregistrer");
        }
        $json=json_encode($obj,JSON_PRETTY_PRINT);
        $file=$this->getFilePathByUid($obj->uid);
        file_put_contents($file,$json);
    }

    /**
     * @param string $uid
     *
     * @return null|mixed
     * @throws \Pov\PovException
     */
    public function getModelByUid($uid){
        $json=$this->getJsonByUid($uid);
        if(!$json){
            return null;
        }
        if(!isset($json->jsonDbMappedClass)){
            throw new PovException("Le json '$uid' n'a pas de nom de classe (jsonDbMappedClass) on ne peut pas le deviner");
        }else{
            $modelClass=$json->jsonDbMappedClass;
            if(!class_exists($modelClass)){
                throw new PovException("Le json ('$uid') a une classe invalide ('$modelClass')");
            }else{
                $obj=new $modelClass();
                $this->fillModelData($uid,$obj);
                return $obj;
            }
        }
    }

    /**
     * A partir d'un modèle de classe vide et d'un uid, remplis le modèle.
     * C'est une méthode alternative à getModelByUid
     * @see getModelByUid
     *
     * @param $uid
     * @param $emptyModelToPopulate
     *
     * @throws \Pov\PovException
     */
    public function fillModelData($uid,$emptyModelToPopulate){
        $mapper = new \JsonMapper();
        $mapper->bExceptionOnUndefinedProperty=false;//erreur si il y a des propriétés dans le json qui ne sont pas déclarées dans la classe
        $mapper->bExceptionOnMissingData=true; //erreur pour les champs commentés avec @required qui ne sont pas définis dans le json
        $json=$this->getJsonByUid($uid);
        $emptyModelClass=get_class($emptyModelToPopulate);
        $jsonClass=$json->jsonDbMappedClass;
        if($emptyModelClass != $jsonClass){
            throw new PovException("Les classes entre le json et la classe demandée ne correspondent pas ( $emptyModelClass != $jsonClass ) ");
        }
        $mapper->map($json,$emptyModelToPopulate);
    }
}

