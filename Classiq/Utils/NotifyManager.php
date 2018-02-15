<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 21/12/2017
 * Time: 17:27
 */

namespace Classiq\Utils;


use Classiq\Models\Session;
use Classiq\Models\Sessionotify;
use Classiq\Utils\Notify\AdminNotifier;
use Classiq\Utils\Notify\HumanNotifier;
use Pov\System\AbstractSingleton;
use Pov\System\ServerEvent;

/**
 * Class NotifyManager
 * @package Classiq\Utils
 *
 * @property HumanNotifier $human Pour notifier uniquement l'utilisateur courrant
 * @property AdminNotifier $admins Pour notifier uniquement les administrateurs
 */
class NotifyManager extends AbstractSingleton
{
    const SSE_USER_LOGOUT="SSE_USER_LOGOUT";
    const SSE_USER_LOGIN = "SSE_USER_LOGIN";
    const SSE_USER_IS_WYSIWYG = "SSE_USER_IS_WYSIWYG";

    /**
     * @var Sessionotify
     */
    private static $lastCreatedForLoop;
    /**
     * @var array ici on stocke tous les ids qui on déjà été renvoyés pour être certain de ne pas les renvoyer
     */
    private static $idsYetRenderedInLoop=[];

    /**
     * initialise qques truc avant une boucle de notifications sse
     */
    public static function SSEloopStart()
    {
        //derniere notification, donc on ne devra jamais afficher les antérieures dans cette boucle
        self::$lastCreatedForLoop=Sessionotify::lastCreated();
        self::$idsYetRenderedInLoop=[];
        //db()->useWriterCache(false);
    }

    /**
     * Remplis les ServerEvent avec les notifications adéquates.
     */
    public static function notifyToServerEvents(){
        /** @var Sessionotify[] $notifs */
        $notifs=[];

        $session=Session::getHumanSession();
        //recherche les nouvelles notifs
        $notifs=$session
            ->unbox()
            ->withCondition("id > '".self::$lastCreatedForLoop->id."' order by id asc")
            ->ownSessionotifyList;

        //renvoie les notfis
        foreach ($notifs as $notif){
            $notif=$notif->box();
            if(!in_array($notif->id,self::$idsYetRenderedInLoop)){ //on est certain de ne pas renvoyer deux fois le même id
                new ServerEvent(
                    $notif->id,
                    $notif->event,
                    $notif->message,
                    $notif->vars
                );
                self::$idsYetRenderedInLoop[]=$notif->id;
            }
        }
        /*
        new ServerEvent(
            Sessionotify::lastCreated()->getID(),
            ServerEvent::EVENT_DEBUG_LOG,
            rand(0,999)
        );
        */
        //last one is no more lastone
        //self::$lastCreatedForLoop=Sessionotify::lastCreated();


    }

    public function __get($name)
    {
        switch ($name){
            case "human":
                return new HumanNotifier();
            case "admins":
                return new AdminNotifier();
        }
    }
}