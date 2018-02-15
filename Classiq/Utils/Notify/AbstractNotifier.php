<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 21/12/2017
 * Time: 17:38
 */

namespace Classiq\Utils\Notify;


use Classiq\Models\Sessionotify;
use Pov\System\ServerEvent;

/**
 * Utilitaire pour faire des notifications
 * @package Classiq\Utils\Notify
 */
abstract class AbstractNotifier
{

    /**
     * @return Sessionotify[] nouvelles notification vierge
     */
    protected static function  getNew(){
        return [];
    }

    /**
     * Envoie un debug log
     */
    public function debugLog($logMessage){
        $notifs=static::getNew();
        foreach ($notifs as $notif){
            $notif->event=ServerEvent::EVENT_DEBUG_LOG;
            $notif->message=$logMessage;
            db()->store($notif);
        }

    }
}