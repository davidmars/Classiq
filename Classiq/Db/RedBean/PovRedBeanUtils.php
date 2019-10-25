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
}