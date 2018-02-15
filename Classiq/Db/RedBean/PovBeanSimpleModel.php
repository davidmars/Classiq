<?php
namespace Classiq\Db\RedBean;


use RedBeanPHP\SimpleModel;

/**
 * La classe PovBeanSimpleModel est une classe de base pour étendre les moèles Redbean et leur attribuer quelques options supplémentaires
 * @package Pov\Db\RedBean
 */
class PovBeanSimpleModel extends SimpleModel
{

    /**
     * Renvoie les attributs html qui permettront d'identifier ce modèle
     * rb-uid='type/id'
     *
     * @return string Les code html des attributs
     */
    public function htmlAttr(){
        $arr=[
            "rb-uid"=>$this->bean->getMeta("type")."/".$this->id
        ];
        return pov()->utils->html->arrayToAttr($arr);

    }

    /**
     * Teste si la table a des champs
     */
    public function hasFields(){
        $type=$this->bean->getMeta('type');
        $fields=bdd()->inspect($type);
        return count($fields)>0;

    }
}