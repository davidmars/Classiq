<?php
namespace Classiq\Models;


/**
 * Modèle redbean de base pour tous les modèles Classiq
 * @package Classiq\Models
 *
 * @property String $sessid id de session
 * @property String $data data encodées nativement par php
 * @property Sessionotify[] $xownSessionotifyList
 * @property bool $isadmin
 * @property User $user
 * @property int $user_id
 * @property string $ip_address
 * @property string browser
 * @property string platform
 * @property String $expires Date à laquelle la session sera automatiquement effacée
 */
class Session extends Classiqbean
{
    /**
     * @var int durée de vie en secondes des sessions (5 heures)
     */
    public static $expireDuration=3600 * 5;

    public function update(){
        parent::update();
        $this->expires=date("Y-m-d H:i:s",time()+self::$expireDuration);
        db()->trashExpires(self::_type());
    }

    /**
     * Enregistre le record en ne modifiant que date_modified et expires
     */
    public function touch(){
        $this->expires=date("Y-m-d H:i:s",time()+self::$expireDuration);
        parent::touch();
    }

    /**
     * Retourne un record par son $sessid
     * @param string $sessid
     * @param bool $useCache si défini sur false re-fera une requete mysql (avec un petit random) pour s'assurer de la fraicheur des infos de session.
     * @return Session
     */
    public static function getBySessid($sessid,$useCache=true)
    {
        $q="sessid='$sessid'";
        //teste et efface si sessions en double
        $count=db()->count("session",$q);
        if($count>1){
            $selection=db()->find("session",$q." ORDER BY id DESC LIMIT 1 $count");
            db()->trashAll($selection);
        }
        if(!$useCache){
            $q.=" AND id != 'notpossible".rand(0,999999)."'";
        }
        $bean=db()->findOne("session",$q);
        if(!$bean || !$bean->id){
            /** @var Session $bean */
            $bean=db()->dispense("session");
            $bean->sessid=$sessid;
            $bean->data="";
            $bean->ip_address=the()->computer()->ipAddress();
            $bean->browser=the()->computer()->browser();
            $bean->platform=the()->computer()->platform();
            pov()->log->warning("cree une session");
            db()->store($bean);
        }
        return $bean->box();
    }

    /**
     * Renvoie la Session de l'humain devant l'ordi
     * @param bool $useCache si défini sur false refera une requete mysql pour s'assurer de la fraicheur des infos de session.
     * @return Session Le record de session courrant
     */
    public static function getHumanSession($useCache=true){
        return self::getBySessid(session_id(),$useCache);
    }

    /**
     * @return Session[] Toutes les sessions admin
     */
    public static function allAdmin()
    {
        return db()->find("session","isadmin = '1' ");
    }

    /**
     *
     * @param float|int $seconds 5 minutes
     * @return bool true si la session va expirer dans moins de $seconds (5 minutes par defaut)
     */
    public function willExpire($seconds=60*5)
    {
        $now=time();
        $expire=strtotime($this->expires);
        $exp=$expire-$now;
        if($exp<$seconds){
            return true;
        }else{
            return false;
        }
    }
}