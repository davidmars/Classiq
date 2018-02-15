<?php
namespace Classiq\Models;


/**
 * Une notification ratachée à une session
 * @package Classiq\Models
 *
 * @property String $session_id la session qui recevra la notification
 * @property String $message Le message à afficher
 * @property String $event Le type d'event
 * @property String $expires Date à laquelle le record sera automatiquement effacé
 *
 */
class Sessionotify extends Classiqbean
{
    /**
     * @var float|int durée max de vie des notifications
     */
    public static $expireDuration=60*60;

    public function update()
    {
        parent::update();
        $this->expires=date("Y-m-d H:i:s",time()+self::$expireDuration);
        db()->trashExpires(self::_type());
        if($this->mordedata && !is_string($this->mordedata)){
            $this->mordedata=json_encode($this->mordedata);
        }
    }

    /**
     * Retourne les notifications ratachées à un sessid
     * @param string $sessid
     * @return Sessionotify[]
     */
    public static function getAllBySessid($sessid)
    {
        $sess=Session::getHumanSession();
        return Classiqmodel::boxAll(
            $sess->xownSessionotifyList
        );
    }


    //


}