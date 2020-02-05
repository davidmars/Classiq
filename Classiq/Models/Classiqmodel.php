<?php
namespace Classiq\Models;

use Classiq\Classiq;
use Classiq\Models\JsonModels\ListItem;
use Classiq\Utils\ModelViewsSolver;
use Classiq\Wysiwyg\Wysiwyg;
use GUMP;
use Pov\PovException;

/**
 * Modèle redbean de base pour tous les modèles Classiq
 * @package Classiq\Models
 *
 * @property Int $id
 * @property String $name Nom du record
 * @property bool $conf_prevent_trash Si true il n'est pas possibe d'effacer ce modèle
 */
class Classiqmodel extends Classiqbean
{

    const EVENT_CREATE="Model.create";
    const EVENT_TRASH="Model.trash";
    const EVENT_UPDATE="Model.update";
    const EVENT_SSE_DB_COUNT_CHANGE="SSE_DB_COUNT_CHANGE";
    const EVENT_SSE_DB_TRASH="SSE_DB_TRASH";
    const EVENT_SSE_DB_CHANGE="SSE_DB_CHANGE";

    //-------------------------- validation --------------------------------

    /**
     * @var GUMP
     */
    protected static $gump;

    /**
     * @return array|bool un tableau d'erreurs ou
     * @throws \Exception
     */
    public function getErrors(){
        if(!self::$gump){
            self::$gump=new GUMP("fr");
        }
        $validation = self::$gump->validate(
            $this->apiData(),
            $this->_validators()
        );
        if($validation===true){
            return [];
        }else{
            return self::$gump->get_errors_array($validation);
        }

    }

    /**
     * Renvoie les erreurs sous forme de texte
     * @return string
     * @throws \Exception
     */
    public function getErrorsString(){
        $errs=$this->getErrors();
        $str='';
        foreach ($errs as $field=>$err){
            $str.=$err."\n";
        }
        return $str;
    }


    /**
     * Renvoie les règles de validation
     * @return array ouù chaque entrée ressemble à "champ"=>"regle"
     * @see https://github.com/Wixel/GUMP#available-validators
     */
    protected function _validators(){
        return
            [
                'name' => 'min_len,3'
            ];
    }

    //---------------------------- validation end----------------------------

    //---------------------------- définitions du modèle---------------------
    /**
     * @var string identifiant SVG de l'icone associée à ce type de record
     */
    static $icon="cq-question";

    /**
     * @param bool $plural Si true retournera le nom du modèle au pluriel
     * @return string nom du type de record lisible par les humains
     */
    static function humanType($plural=false){
        $r=ucwords(self::modelTypeStatic());
        if($plural){
            $r.="s";
        }
        return $r;
    }

    /**
     * @var string champs de tri par défaut pour ce type de record. Utilisé dans le browser de l'admin par exemple.
     */
    static public $DEFAULT_ORDER_BY="name ASC";

    /**
     * @return string nom du type de record lisible par les humains
     */
    static function humanDescription(){
        return "...";
    }
    /**
     * @var bool
     */
    static $isPage=false;









    //--------------------------------------------




    /**
     * Renvoie un tableau avec les records.
     * Les noms de champs de ralation classiques modele_id fonctionnent.
     * Les noms de champs à point monchamptableau.mavar fonctionnenent aussi.
     * @param string $varName Le champ qu'on veut (la syntaxe à point pour les tableaux fonctionne, monchamptableau.mavar par exemple)
     * @return Classiqmodel[]
     */
    public function getValueAsRecords($varName)
    {
        if(preg_match("/([a-zA-Z0-9_]+)_id$/",$varName,$m)){
            $prop=$m[1];
            $record=$this->$prop;
            if($record){
                return [$record];
            }
            return [];
        }
        return self::getByUids(
            $this->getValue($varName,true)
        );

    }
    /**
     * @param string $varName Le champ qu'on veut (la syntaxe à point pour les tableaux fonctionne, monchamptableau.mavar par exemple)
     * @return Classiqmodel
     */
    public function getValueAsRecord($varName)
    {

        return self::getByUid(
            $this->getValue($varName)
        );
    }

    /**
     * Retourne la valeur de la variable donnée
     *
     * @param string $varName Le champ qu'on veut (la syntaxe à point pour les tableaux fonctionne, monchamptableau.mavar par exemple)
     * @param bool   $forceString si true renverra toujours un string
     *
     * @return mixed
     * @throws PovException
     */
    public function getValue($varName,$forceString=false){

        if($forceString){
            //many to many relation
            if(preg_match("/^shared([a-zA-Z0-9_]+)List$/",$varName,$matches)){
                $foreignTable=strtolower($matches[1]);
                /** @var Classiqmodel[] $val **/
                $val=$this->unbox()->with("ORDER BY order_$foreignTable")->$varName;
                $arr=[];
                if(!is_array($val)){
                    var_dump($val);
                    throw new PovException($val);
                }
                if($val){
                    foreach ($val as $record){
                        $arr[]=$record->uid();
                    }
                }
                $val=implode(",",$arr);
                return $val;
            }
        }

        //syntaxe à point
        if(preg_match("/\./",$varName)){
            $val= pov()->utils->array->getValFromDotsVarName($this,$varName);
            if(is_array($val)){
                if(preg_match("/^(.+)\.(key_[^\.]+)$/",$varName,$m)){
                    $val=new ListItem($val,$m[2],$m[1],$this);
                }
            }
        }else{
            $val=$this->$varName;
        }


        return $val;
    }
    /**
     * Retourne la valeur du champ sous forme de tableau de chaines
     * partant du principe que le contenu est une chaine séparée par des points virgules
     *
     * @return array La valeur du champ sous forme de tableau.
     */
    public function getValueAsStringArray($varName){
        $string=$this->getValue($varName,true);
        return explode(";",$string);
    }

    /**
     * Définit la valeur de la variable donnée
     * @param string $varName Le champ qu'on veut (la syntaxe à point pour les tableaux fonctionne, monchamptableau.mavar par exemple)

     */
    public function setValue($varName,$value){
        if(preg_match("/\./",$varName)) {
            //si la variable contient l'uid du record au début on le suprime
            if (preg_match("@^" . $this->uid() . "\.(.*)$@", $varName, $m)) {
                $varName = $m[1];
            }
            pov()->utils->array->setValFromDotsVarName($this, $varName, $value);
        }else if(preg_match("/([A-Za-z0-9_]+)_id$/",$varName,$m)){
            //une association de type propriete_id
            $prop=$m[1];
            $model=null;
            if(preg_match("/([A-Za-z0-9_])*-([0-9]*)/",$value,$m)){
                $model=Classiqmodel::getByUid($value);
            }
            if($model){
                $this->$prop=$model->unbox();
            }else{
                $this->$prop=null;
            }
        }else if(is_array($value)){

            //$this->$varName=$value; //soucis avec celui-là c'est que ça supprime les variables non définies
            //$this->$varName=array_merge($value,$this->$varName); //soucis avec celui-ci c'est que ça ne supprime pas les entrées
            //donc...
            $clean=[];
            foreach ($value as $k=>$v){
                if(is_array($v) && isset($this->$varName[$k])){
                    $clean[$k]=array_merge($v,$this->$varName[$k]);
                }else{
                    $clean[$k]=$v;
                }
            }
            $this->$varName=$clean;

        }else{
            if(preg_match("/^shared([a-zA-Z0-9_]+)List$/",$varName,$matches)){
                //many to many association
                //db()->useWriterCache(false);
                $models=Classiqmodel::getByUids($value);
                //nom de la table d'associations many to many
                $localType=strtolower($this::_type());
                $foreignType=strtolower($matches[1]);
                $assocTable=db()->utils->getAssocTableName($localType,$foreignType,true);
                //efface les assocs existantes qui ne sont pas dans la nouvelle liste
                $assocs=db()->getAll("SELECT * FROM `$assocTable` WHERE ".$localType."_id = ".$this->id);
                $newIds=[];
                foreach ($models as $m){$newIds[]=$m->id;}
                foreach ($assocs as $assoc){
                    if(!in_array($assoc[$foreignType."_id"],$newIds)){
                        db()->getAll("DELETE FROM `$assocTable` WHERE id=".$assoc["id"]);
                    }
                }
                //va créer ou mettre à jour les assocs
                $order=0;
                foreach ($models as $m){
                    $assoc=db()->getAll("SELECT * FROM `$assocTable` WHERE $localType"."_id = ".$this->id." AND  $foreignType"."_id = ".$m->id);
                    if(!count($assoc)){
                        //si la liaison existe pas, on la crée
                        db()->getAll("INSERT INTO `$assocTable` (".$localType."_id, ".$foreignType."_id, order_$foreignType) VALUES ('".$this->id."','".$m->id."','$order')");
                        //$this->unbox()->link( $assocTable,["order_".$typeB."_in_".$typeA=>"999999","order_".$typeA."_in_".$typeB=>"999999"] )->$typeB=$m->unbox();
                    }else{
                        //met à jour le champ order_$foreignType
                        db()->getAll("UPDATE `$assocTable` SET order_$foreignType = '$order' WHERE id='".$assoc[0]["id"]."'");
                    }
                    $order++;
                }

            }else{
                $this->$varName=$value;
            }

        }

    }

    /**
     * Permet d'acceder aux methods wysiwyg de ce record
     * @param bool $active
     * @return Wysiwyg
     */
    public function wysiwyg($active=true){
        $w=new Wysiwyg($this,$active);
        return $w;
    }




    //-------------------------------------------

    /**
     * Retourne le record page par son nom
     * @param string $name
     * @param bool $create si défini sur true et que le record n'existe pas le crée
     * @return $this|null
     */
    public static function getByName($name,$create=false){
        $type=self::modelTypeStatic();
        $bean=db()->findOne($type,"name='$name'");
        if(!$bean && $create){
            $bean=db()->dispense($type);
            $bean->name=$name;
            db()->store($bean);
        }
        return $bean->box();
    }

    /**
     * Retourne le record par son uid ou bien un objet json si l'uid désigne un champ du record
     * @param string $uid modeltype-modelid ou modeltype-modelid.champ.key
     * @param bool $strictType si true vérifiera que le type correspond à la classe appelée
     * @return ClassiqModel|null|bool
     * @throws PovException
     */
    public static function getByUid($uid,$strictType=false)
    {
        if(!$uid){
            return null;
        }
        if(preg_match("/^([a-zA-Z0-9]+)-([0-9]+)\.?([^,]*)/",$uid,$m)){
            $type=$m[1];
            $id=$m[2];
            $varName=$m[3];
            if($strictType && $type !== self::modelTypeStatic()){
                return false;
            }
            $record=db()->findOne($type,"where id = '$id'");
            if($record){
                /** @var Classiqmodel $record */
                $record=$record->box();
                if(!$varName){
                    //retourne un record
                    return $record;
                }else{
                    //le champ sera un json/array à priori
                    return $record->getValue($varName);
                }
            }
        }
        return null;

    }
    /**
     * Retourne les records obtenus à partir d'une liste d'uids
     * @param string|String[] $uid modeltype-modelid ou modeltype-modelid.champ.key
     * @return ClassiqModel[]
     */
    public static function getByUids($uids)
    {
        $r=[];

        if($uids){
            $uids=pov()->utils->array->fromString($uids);
            foreach ($uids as $uid){
                $record=Classiqmodel::getByUid($uid);
                if($record){
                    $r[]=$record;
                }
            }
        }
        return $r;

    }



    //-------------------------------------------events cycle--------------------------------------------


    /**
     * Après enregistrement.
     * Notifie que l'enregistrement a été modifié
     */
    public function after_update()
    {
        //notifications
        if(!cq()->configPreventDbNotifications){
            $message=$this->views()->wysiwygPreview()->render()."<br>";
            if($this->changes()){
                pov()->log->warning("modifie changes".$this->uid(),[$this->changes()]);
                cq()->notify->admins->notify(self::EVENT_SSE_DB_CHANGE,$message." a été enregistré.",$this->apiData());
            }
        }
        pov()->events->dispatch(self::EVENT_UPDATE,[$this]);
        parent::after_update();
    }

    /**
     * Invoqué juste avant qu'on essaye d'effacer le record
     * Si ce n'est pas possible, une exception sera renvoyée
     * @throws PovException
     */
    public function delete()
    {
        if($this->conf_prevent_trash){
            throw new PovException("Il n'est pas possible de supprimer <b>$this->name</b>. L'enregistrement est protégé.");
        }
    }

    /**
     * Invoqué juste après que le modèle ait été effacé.
     *
     */
    public function after_delete()
    {

        $message="<b>$this->name</b> vient d'être supprimé.";
        $oldBean=clone $this->bean;
        $oldBean->id=$this->bean->old("id");
        $oldBean->uid=$this->modelType()."-".$oldBean->id;

        cq()->notify->admins->notify(self::EVENT_SSE_DB_COUNT_CHANGE,$message);
        cq()->notify->admins->notify(self::EVENT_SSE_DB_TRASH,$message,$oldBean);


    }

    public function open()
    {
        parent::open();
    }

    /**
     * @param string $prop utilsez _lang pour obtenir une variable traduite dans la langue courrante
     *
     * @return mixed
     */
    public function __get($prop)
    {
        $propClean=preg_replace("/_lang$/","",$prop);
        $noLangVal=parent::__get($propClean);
        if($propClean != $prop){
            //c'est un champ traduit
            $languageProp=$propClean."_".the()->project->langCode;
            $langVal=parent::__get($languageProp);
            if($langVal){
                return $langVal;
            }
            return $noLangVal;

        }else{
            //c'est un champ normal
            return $noLangVal;
        }

    }
    /**
     * @param string $prop utilsez _lang pour définir une variable traduite dans la langue courrante
     *
     */
    public function __set($prop, $value)
    {
        $prop=preg_replace("/_lang/","_".the()->project->langCode,$prop);
        parent::__set($prop, $value);
    }


}