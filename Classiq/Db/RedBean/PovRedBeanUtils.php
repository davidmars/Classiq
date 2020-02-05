<?php


namespace Classiq\Db\RedBean;


use Pov\System\AbstractSingleton;
use RedBeanPHP\OODBBean;
use RedBeanPHP\SimpleModel;

/**
 * Class PovRedBeanUtils
 * @package Classiq\Db\RedBean
 *
 * @method static PovRedBeanUtils inst()
 */
class PovRedBeanUtils extends AbstractSingleton
{
    /**
     * To get ids extracted from the given selection
     * @param OODBBean[]|SimpleModel[] $records List of records
     * @return int[] list of ids
     */
    public function idsFromList($records){
        $ids=[];
        foreach ($records as $r){
            $ids[]=$r->id;
        }
        return $ids;
    }

    /**
     * Revoie le nom de la table d'association
     * @param string $recordType1
     * @param string $recordType2
     * @return string un truc comme "hashtag_project"
     */
    public function getAssocTableName($recordType1,$recordType2,$testStructure=false){
        $recordType1=strtolower($recordType1);
        $recordType2=strtolower($recordType2);
        $assocTable=[$recordType1,$recordType2];
        sort($assocTable); //pour etre certain que dans les deux sens on ait le même nom de table
        $assocTable=implode("_",$assocTable);
        if($testStructure){
            $this->initAssocTable($recordType1,$recordType2);
        }
        return $assocTable;
    }

    public $cache=[];

    /**
     * Génère la table d'association entre deux modèles et ajoute les champs order
     * @param string $recordType1
     * @param string $recordType2
     */
    public function initAssocTable($recordType1,$recordType2){
        //ne fait l'opération qu'une seule fois
        if(isset($this->cache["initAssocTable $recordType1 $recordType2"])){
            return;
        }
        $this->cache["initAssocTable $recordType1 $recordType2"]=true;

        cq()->configNotificationsOff();
        $recordType1=strtolower($recordType1);
        $recordType2=strtolower($recordType2);
        $tmp1=db()->dispense($recordType1);
        $tmp2=db()->dispense($recordType2);
        $tmp1->name="tmp $recordType1";
        $tmp2->name="tmp $recordType2";
        db()->store($tmp1);
        db()->store($tmp2);

        //créee la table d'association si elle existait pas encore
        $tmp1->{"shared".ucfirst($recordType2)."List"}=[$tmp2];
        db()->store($tmp1);

        //ajoute les colonnes
        $assocTable=$this->getAssocTableName($recordType1,$recordType2);
        $assoc=db()->findOne($assocTable,$recordType1."_id = ".$tmp1->id);
        $assoc->{"order_$recordType1"}=1;
        $assoc->{"order_$recordType2"}=1;
        db()->store($assoc);

        //nettoie le modèle temporaire
        db()->trash($tmp1);
        //nettoie le hashtag temporaire
        db()->trash($tmp2);
        cq()->configNotificationsReset();
    }
}