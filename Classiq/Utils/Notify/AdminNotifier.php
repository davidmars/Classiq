<?php

namespace Classiq\Utils\Notify;

use Classiq\Models\Session;
use Classiq\Models\Sessionotify;


/**
 * Notifie les admins
 * @package Classiq\Utils\Notify
 */
class AdminNotifier extends AbstractNotifier
{
    /**
     * @return Sessionotify[] Nouvelles notifications vierges à l'attention des administrateurs
     */
    protected static function getNew(){
            $notifs=[];
            foreach (Session::allAdmin() as $session){
                $notif=Sessionotify::getNew();
                $notif->session_id = $session->id;
                $notifs[]=$notif;
            }
            return $notifs;
    }

    /**
     * Notifie un évènement aux admins avec un message
     * @param string $eventType
     * @param $message
     * @param mixed $moreData données additionelles transmises à l'event
     */
    public function notify($eventType,$message,$moreData=null){
        self::trashPrevious($eventType);

        foreach (self::getNew() as $notif){
            $notif->event=$eventType;
            $notif->message=$message;
            if($moreData){
                $notif->vars=pov()->utils->array->fromObject($moreData);
            }

            db()->store($notif);
        }
    }

    /**
     * Efface toutes les notifs avec ce type d'event
     */
    private static function trashPrevious($eventType)
    {
        $selection=db()->find(Sessionotify::_type(),"event='$eventType'");
        //db()->trashAll($selection); todo important remettre en place sans doute
    }

}