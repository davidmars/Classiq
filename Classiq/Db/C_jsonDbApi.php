<?php
namespace Pov\Db;

use Pov\MVC\Controller;
use Pov\MVC\ControllerUrl;
use Pov\MVC\View;
use Pov\System\ApiResponse;

/**
 * Class C_jsonDbApi permet d'interagir avec une bdd JsonDb
 *
 * @package Pov\Db
 */
class C_jsonDbApi extends Controller{
    /**
     * @var string url de base
     */
    protected static $name="jsonDbApi";

    /**
     * Permet de modifier une record JsonDb
     * @param string $dbName nom de la JsonDb
     * @param string $uid identifiant du record
     *
     * @return ControllerUrl
     */
    public function save_url($dbName,$uid){
        return new ControllerUrl(self::$name."/save/$dbName/$uid");
    }
    /**
     * Permet de modifier une record JsonDb
     * @param string $dbName nom de la JsonDb
     * @param string $uid identifiant du record
     * @requestParam stdClass recordDatas Objet json qui contient le valeurs du record
     * @return \Pov\MVC\View
     */
    public function save_run($dbName,$uid){

        $return = new ApiResponse();

        $errs=[];
        $recordDatas=$return->testAndGetRequest("recordDatas",null,true);

        $db=JsonDb::getDb($dbName);
        if(!$db){
            $return->addError("Database not found: $dbName");
        }
        if(!$uid){
            $return->addError("Il nous faut un uid");
        }

        if($return->success){
            $record=$db->getModelByUid($uid);
            if(!$record){
                //cree un nouveau
                if(!isset($recordDatas["type"])){
                    $return->addError("pas de type pour ce nouveau record....");
                }
                if($return->success){
                    $type=$recordDatas["type"];
                    if($type){
                        $record=new $type();
                        $record->uid=$uid;
                    }
                }
            }
            if($record){
                pov()->events->dispatch("C_jsonDbApi.save",[$dbName,$uid,$record,$return]);
            }
            if($record && $return->success){
                foreach($recordDatas as $k=>$v){
                    if(property_exists($record,$k)){
                        $record->$k=$v;
                    }
                }
                $db->saveModel($record,$uid);
                $return->record=$record;
            }

        }

        return new View("json",$return);
    }



}