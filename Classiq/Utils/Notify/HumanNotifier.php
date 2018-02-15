<?php

namespace Classiq\Utils\Notify;

use Classiq\Models\Session;
use Classiq\Models\Sessionotify;
use Classiq\Utils\NotifyManager;


/**
 * Class HumanNotifier
 * @package Classiq\Utils\Notify
 */
class HumanNotifier extends AbstractNotifier
{
    /**
     * @return Sessionotify[] Notifiaction vierge destinée à l'humain devant l'ordi
     */
    protected static function getNew(){
        $notif=Sessionotify::getNew();
        $notif->session_id=Session::getHumanSession()->id;
        return [$notif];
    }
    /**
     * notifie qu'un logout vient d'être fait
     */
    public function logout(){
        $this->trashPreviousLoginLogout();
        foreach (self::getNew() as $notif){
            $notif->event=NotifyManager::SSE_USER_LOGOUT;
            db()->store($notif);
        }
    }

    /**
     * notifie qu'un login vient d'être fait
     */
    public function login(){
        $this->trashPreviousLoginLogout();
        foreach (self::getNew() as $notif){
            $notif->event=NotifyManager::SSE_USER_LOGIN;
            db()->store($notif);
        }


    }

    /**
     * Efface toutes les notifs de login/logout pour la session actuelle
     */
    private function trashPreviousLoginLogout()
    {
        $selection=db()->find(Sessionotify::_type(),"session_id='".Session::getHumanSession()->id."' and event in ('user.login','user.logout')");
        db()->trashAll($selection);
    }

}