<?php


namespace Classiq;

use Pov\Defaults\Defaults;
use Pov\MVC\View;


/**
 * Un package qui permet d'avoir un site "classique"
 * BDD RedBean
 * Wyswyg
 *
 * @package Classiq
 */
class Classiq
{

    /**
     * @param bool $view installer les vues?
     * @param bool $controllers installer les controllers?
     */
    public static function install($view=true,$controllers=true){
        Defaults::install(true,false);
        require_once (__DIR__."/constants.php");
        if($view){
            //View::$possiblesPath=array_merge([__DIR__ . "/v"],View::$possiblesPath);
            View::$possiblesPath[]= __DIR__ . "/v";
            View::$possiblesPath[]= __DIR__ . "/_src";
        }
        if($controllers){
            C_classiq::install();
        }

        //installe les listeners PovApi
        include(__DIR__."/boot/boot.php");
        include(__DIR__."/boot/events.php");

        //ouvre la session
        if(the()->requestUrl->match("dev/page/logs")){
            pov()->disableLogging();
        }
        new SessionHandler();


    }

    /**
     * @return string Le chemin pour acceder aux assets
     */
    public static function assetsDir(){
        return realpath(__DIR__."/../dist/");
    }


    /**
     * Charge un record à partir de son uid
     * @param string $uid exemple page-789 correspond au bean de type page ayant pour id 789
     * @return OODBBean|null
     */
    public static function findUid($uid){
        list($type, $id) = explode("-",$uid);
        return db()->findOne($type,"id='$id'");
    }

}

