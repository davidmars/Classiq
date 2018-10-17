<?php

namespace Classiq;

use Classiq\Models\Classiqmodel;
use Classiq\Models\Page;
use Classiq\Models\Urlpage;
use Classiq\Models\User;
use Classiq\Seo\SEO;
use Pov\Defaults\C_default;
use Pov\MVC\View;
use Pov\PovException;
use Pov\System\ApiResponse;

/**
 * Les contrôleurs utilisés par le système classiq
 * @package Pov\defaults\MVC\C
 */
class C_classiq extends C_default {

    /**
     * Installe les routes dans the()->project. ATTENTION il est conseillé de les installer à la fin afin que la 404 soit executée en dernier ordre.
     * Les routes installées sont index,v/* et err404
     *
     */
    public static function install(){
        the()->project->controllerNameSpaces[]="Classiq";
        the()->project->controllerNameSpaces[]="Classiq/Seo";
        the()->project->controllerNameSpaces[]="Pov/Defaults"; // pour avoir C_dev, C_povApi etc...
        foreach (self::$routesRules as $regle=>$controller){
            the()->project->routesPush($regle,$controller);
        }
    }

    /**
     * @var array Les règles d'url à utiliser pour ce contrôleur
     */
    public static $routesRules=[
      "^$"=>"classiq/index",
      "^favicon.ico$"=>"classiq/favicon",
      "^robots.txt$"=>"robots/index",
      "^sitemap.xml$"=>"sitemap_xml/index",
      "^login$"=>"classiq/login",
      "^dwd/(.*)/(.*)$"=>"default/dwd/$1/$2",
      "^v/(.*)$"=>"classiq/quickView/($1)", // le ($1) dit que les slashes à l'intérieur de la parenthèse sont préservés (toto/titi) ne donnera pas deux arguments mais un seul
      "^permalink-uid/([A-Za-z]+)-([0-9]+)$"=>"classiq/permalinkUid/$1/$2", //
      "^.*\.p([0-9]+)$"=>"classiq/pageId/$1", //   ce/que-tu_veux.p14 renverra vers la PageUrl@14
      "^(.*)$"=>"classiq/page" //qui renverra une 404 au besoin

    ];




    /**
     * @return View
     */
    public function index_run(){
        $homePageUrl=Urlpage::homePage(true);
        return $homePageUrl->getPage()->views()->page();
    }

    /**
     * Page issue de la db à partir de son $pageUrl-> uniquement
     * ...ou page d'erreur 404 si on trouve rien
     * @return View
     */
    public function page_run(){
        $u=trim(the()->requestUrl->routeString(),"/");
        $u=Urlpage::getByUrl($u);
        if($u){
            return $u->getPage()->views()->page();
        }else{
            return $this->err404_run();
        }
    }

    /**
     * Retourne l'url d'une page à partir de son type et de son nom
     * @param string $type
     * @param string $name
     * @return mixed
     * @throws PovException erreur si la page n'est pas trouvée
     */
    public static function pageByName_url($type, $name){
        /** @var Page $p */
        $p=db()->findOne(strtolower($type),"name='$name'");
        if($p){
          return $p->box()->href();
        }else{
            throw (new PovException("Pas de page $type ayant pour nom '$name'"));
        }
    }
    /**
     * Page issue de la db ou page d'erreur 404 si on trouve rien
     * @param Urlpage $urlpage
     * @return \Pov\MVC\ControllerUrl|string|null
     */
    public static function pageId_url($urlpage){
        //return self::genUrl("blurps",false);
        $page=$urlpage->getPage();
        if(!$page){
            throw new PovException("pageId_url pas de page ".pov()->debug->dump($urlpage));
        }

        if(!$urlpage->stricturl){
            $url=$urlpage->translatedUrl(the()->project->langCode,true);
        }else{
            $url=$urlpage->translatedUrl(the()->project->langCode,false);
        }
        return self::genUrl($url,false);
    }
    /**
     * Affiche une page issue de la db à partir de son $pageUrlId
     * ...ou page d'erreur 404 si on trouve rien
     * @return View
     */
    public function pageId_run($urlpageId){
        /** @var Urlpage $urlpage */
        $urlpage=db()->load("urlpage",$urlpageId);
        if($urlpage){
            /** @var Page $page */
            $page=$urlpage->getPage(false);
            if($page){
                return $page->views()->page();
            }else{
                return $this->err404_run();
            }
        }else{
            return $this->err404_run();
        }
        return null;
    }
    /**
     * La page d'erreur 404
     * @return View
     */
    public function err404_run($message="La page n'existe pas"){
        if(the()->requestUrl->isAjax){
            $v=View::get("404",[]);
            //renvoie un json qui sera utilisé par PovHistory.js
            $obj=new ApiResponse();
            $obj->addToJson("meta",the()->htmlLayout()->meta);
            $obj->addToJson("pageInfo",the()->htmlLayout()->pageInfo);
            $obj->html=$v->render();
            $v->inside("json",$obj);
            return View::get("json",$obj,true);
        }else{
            the()->headerOutput->set404($message);
        }

        return new View("404",$message);
    }

    /**
     * Lien vers une page suivant le modèle $type/$id
     * @param string $type
     * @param int $id
     * @return \Pov\MVC\ControllerUrl|string
     */
    public static function permalinkUid_url($type,$id){
        return self::genUrl("permalink-uid/$type-$id",false);
    }

    /**
     * Lien vers une page suivant le modèle $type/$id
     * @param string $type
     * @param int $id
     * @return null|View
     * @throws \Exception
     */
    public function permalinkUid_run($type,$id){
        /** @var Urlpage $urlpage */
        $page=db()->load($type,$id);
        if($page){
            /** @var Page $page */
            return $page->views()->page();
        }
        return null;
    }


    public static function login_url($page=null)
    {
        if($page){
            return self::genUrl("login/$page");
        }else{
            return self::genUrl("login");
        }
    }


    /**
     * La page de login
     * @return View
     */
    public function login_run($page=null){
        if($page){
            return View::get("login/$page");
        }
        return View::get("login/login");
    }

    /**
     * Lien pour éditer un profil utilisateur
     * @param null|User $user L'utilisateur qu'on souhaite éditer
     * @return \Pov\MVC\ControllerUrl|string
     */
    public static function login_edit_profile_url($user=null)
    {
        $userId="";
        if($user==null && User::connected()){
            $user=User::connected();
        }
        if($user){
            $userId=$user->id;
        }
        return self::genUrl("login_edit_profile/$userId");
    }
    public function login_edit_profile_run($userId="")
    {
        if($userId){
            $_REQUEST["id"]=$userId;
        }

        return View::get("login/edit-user");
    }

    /**
     * Dé loggue le user et le renvoie vers la page de login
     * @return \Pov\MVC\ControllerUrl|string
     */
    public static function logout_url()
    {
        return self::genUrl("logout");
    }
    public function logout_run()
    {
        User::logout();
        return View::get("login/login");
    }


} 