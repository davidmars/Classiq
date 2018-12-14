<?php
namespace Classiq\Models;

use Classiq\Models\JsonModels\ListItem;
use Classiq\Utils\ModelViewsSolver;
use Classiq\Wysiwyg\Wysiwyg;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;

/**
 * Modèle redbean de base pour tous les modèles Classiq
 * @package Classiq\Models
 *
 * @property Int $id
 * @property String $date_modified Date de modification
 * @property String $date_created Date de création
 */
class Classiqbean extends \RedBeanPHP\SimpleModel
{
    /**
     * @var array Des variables custom si on veut qui seront enregistrées dans un json
     */
    public $vars=[];

    /**
     * Renvoie la date de création
     * @return bool|\DateTime
     */
    public function getDateCreated(){
        return \DateTime::createFromFormat("Y-m-d H:i:s",$this->date_created);
    }
    /**
     * Renvoie la date de modification
     * @return bool|\DateTime
     */
    public function getDateModified(){
        return \DateTime::createFromFormat("Y-m-d H:i:s",$this->date_modified);
    }

    /**
     * Lancé après ouverture du modèle (attention c'est en cela qu'on n'utilise pas la librairie redbean officielle mais davidmars/redbean).
     * Ici les champs json sont décodés et attribués au modèle.
     */
    public final function after_open(){
        //decode les jsons en array
        foreach ($this->utils_getObjectVars() as $k=>$v){
            if(is_array($this->$k) ){
                $this->bean->setMeta("tainted",true); //sinon on enregistrera pas...à améliorer sans doute
                $jsonName="json$k";
                $this->$k=json_decode($this->$jsonName,true);
            }
        }
    }

    /**
     * Juste avant l'enregistrement
     * à l'enregistrement met à jour date_modified et date_created et encode les json
     *
     */
    public function update() {
        $this->date_modified=date("Y-m-d H:i:s");
        if(!$this->id || !$this->date_created){
            $this->date_created=$this->date_modified;
        }
        //encode les arrays en json
        foreach ($this->utils_getObjectVars() as $k=>$v){
            if(is_array($this->$k)){
                $jsonName="json$k";
                $this->$jsonName=
                    json_encode(
                        $v,
                        JSON_PRETTY_PRINT
                    );
            }
        }
    }

    /**
     * Enregistre le record en ne modifiant que date_modified
     */
    public function touch(){
        $this->date_modified=date("Y-m-d H:i:s");
        db()->store($this);
    }

    /**
     * Type de la classe sans namesapace en minuscule
     * @return string si on est dans Classiq\Models\Bidule renverra bidule
     */
    public static function _type(){
        return strtolower(
            pov()->utils->phpAnalyzer->getClassWithoutNameSpaces(
                get_called_class()
            )
        );
    }

    /**
     *
     * @return OODBBean
     */
    public static function lastCreated(){

        return db()->findOne(self::_type(),"ORDER BY id DESC");
    }

    /**
     * Renvoie un nouveau modele et le renvoie (fait un dispense()->box)
     * @return $this
     */
    public static function getNew()
    {
        $r=db()->dispense(self::_type())->box();
        return $r;
    }

    /**
     * La m$eme chose que box() mais pour un array
     * @param OODBBean[] $beans
     * @return SimpleModel[]
     */
    public static function boxAll($beans)
    {
        $r=[];
        foreach ($beans as $bean){
            $r[]=$bean->box();
        }
        return $r;
    }


    /**
     * Liste des champs à ignorer quand on vérifie si l'enregistrement à été modifié
     * @return string[]
     */
    protected static function changesExcludeFields(){
        return ["date_modified"];
    }

    /**
     * Renvoie la liste des champs qui ont été modifiés depuis que l'enregistrement a été ouvert.
     * @return string[] La liste des champs qui ont été modifiés
     */
    public function changes(){
        $old=$this->unbox()->getMeta( 'sys.orig');
        $new=$this->apiData();
        $changes=[];
        foreach ($old as $k=>$v){
            if(!in_array($k,self::changesExcludeFields())){
                if(isset($new[$k])){
                    if($old[$k]!=$new[$k]){
                        $changes[]=$k;
                    }
                }
            }
        }
        return $changes;
    }

    /**
     * Le type de record
     * @return string
     */
    public function modelType(){
        return self::modelTypeStatic();
    }

    /**
     * Le type de record en minuscule
     * @return string
     */
    public static function modelTypeStatic(){
        return strtolower(
            pov()->utils->phpAnalyzer->getClassWithoutNameSpaces(
                get_called_class()
            )
        );
    }

    /**
     * Retourne les données à afficher quand on utilise l'api
     * permet de retourner des objets en plus et permet par la même de ne pas en retourner certaines
     * @return array
     */
    public function apiData(){
        $r=$this->unbox()->jsonSerialize();
        //rajoute ces champs au début
        $r = ['type' => $this->modelType()] + $r;
        $r = ['uid' => $this->uid()] + $r;
        foreach ($this->utils_getObjectVars() as $k=>$v){
            $r[$k]=$v;
        }
        return $r;
    }

    /**
     * @return array les variables et leurs valeurs déclarées dans le modèle et qui ne sont pas issues du bean.
     */
    protected function utils_getObjectVars(){
        $r=get_object_vars($this);
        unset($r["bean"]);
        return $r;
    }


    /**
     * Identifiant unique du modèle
     * @return string typedemodele-id
     */
    public function uid(){
        return $this->modelType()."-".$this->id;
    }

    /**
     * Invoqué après une modification de l'enregistrement
     * C'est l'endroit où faire les notifications
     */
    public function after_update()
    {
    }

    /**
     * Invoqué à l'ouverture du modèle
     */
    public function open()
    {
    }

    /**
     * Pour obtenir les vues associées automatiquement à ce modèle
     * @return ModelViewsSolver
     */
    public function views(){
        if(!$this->_modelViewsSolver){
            $this->_modelViewsSolver=new ModelViewsSolver($this);
        }
        return $this->_modelViewsSolver;
    }

    /**
     * @var ModelViewsSolver
     */
    private $_modelViewsSolver;


}