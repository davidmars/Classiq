<?php

namespace Classiq\Models;

use Classiq\Classiq;
use Pov\PovException;
use RedBeanPHP\SimpleModel;

/**
 * Représente une url pour une page enregistrée dans la base de données
 * @package Classiq\Models
 * @property Int $id
 * @property Int $related_id id de la page relative
 * @property String $related_type type de la page relative
 * @property String $url l'url (sans slash, sans id et sans host)
 * @property String $meta_title Titre seo de la page
 * @property Number $seo_priority Priorité de la page
 * @property Bool $stricturl Si true l'url ne contiendra pas d'id
 * @property Bool $is_homepage Si true c'est que c'est la home page
 * @property String $seo_change_frequency monthly, daily etc...
 * @property String $meta_description seo description
 */
class Urlpage extends Classiqmodel
{

    static $icon="cq-link";

    /**
     * S'assure que l'url est bien unique
     */
    public function update(){
        if($this->urlExists($this->url)){
            $increment=1;
            $url=$this->url.'-'.$increment;
            while($this->urlExists($url)){
                $increment++;
                $url=$this->url.'-'.$increment;
            }
            $this->url=$url;
        }
        $this->url=strtolower($this->url);
        $this->url=pov()->utils->string->clean($this->url,"/");

        parent::update();

    }

    /**
     * @param $url
     * @return int
     */
    private function urlExists($url){
        return db()->count("urlpage","url='".$url."' and id != '".$this->id."' ");
    }


    /**
     * Notifie que la page relative a changé
     * @throws PovException
     */
    public function after_update(){
        if($this->getPage()->id && $this->getPage()->hasUrl()){

            //notifications
            if(!cq()->configPreventDbNotifications){
                //notifie que la page relative a changée
                $message=$this->getPage()->views()->wysiwygPreview()->render()."<br>";
                if($this->changes()){
                    cq()->notify->admins->notify(
                        self::EVENT_SSE_DB_CHANGE,
                        "Les données SEO de ".$message." on été modifiées.",
                        $this->getPage()->apiData()
                    );
                }
            }

        }

    }

    /**
     * Retourne la page relative
     * @param bool $exception si défini sur false ne renverra pas d'erreur
     * @return null|Page
     */
    public function getPage($exception=true){
        $bean=null;
        if($this->bean->related_type && $this->bean->related_id){
            $bean = db()->findOne($this->related_type,"id = '".$this->bean->related_id."'");
        }
        if(!$bean && $exception){
            throw new PovException("UrlPage sans Page ".pov()->debug->dump($this->bean));
        }
        if(!$bean){
            return null;
        }
        return $bean;

    }
    /**
     * Trouve une Urlpage par son url :)
     * @param string $url l'url sous forme de chaine
     * @return Urlpage|null Le modèle de l'url
     */
    public static function getByUrl($url){
        $bean=db()->findOne("urlpage","url_".the()->project->langCode." = '$url'");
        if(!$bean){
            $bean=db()->findOne("urlpage","url = '$url'");
        }
        return $bean;
    }

    /**
     * Retourne le modèle d'Url de la homepage
     * @param bool $tryToSolveProblems si défini sur true et que la home page n'est pas trouvée, essayera de la déduire.
     * @return Urlpage
     * @throws PovException
     */
    public static function homePage($tryToSolveProblems=false){
        $r=db()->find(self::_type(),"url='' and is_homepage='1'");
        if(count($r)!==1){
            if($tryToSolveProblems){
                self::checkHomePage();
            }else{
                throw new PovException("Attention il y a ".count($r)." home page trouvées!");
            }
        }
        //tout va bien on retourne la home page
        return reset($r);
    }
    /**
     * S'assure que la home page est bien existante, unique et configurée correctement
     * @return bool
     */
    private static function checkHomePage(){
        $home = db()->find(self::_type(),"is_homepage='1'");

        switch (count($home)){
            case 1:
                //ok tout va bien
                $r=reset($home);
                $r->url="";
                db()->store($r);
                throw new PovException("Un problème de home page a été résolu (1). Essayez de recharger.");
            case 0:
                //pas trouvé...
                break;
            default:
                //gros bobo
                throw new PovException("Attention plusieurs home pages possibles!");
        }
        //va essayer de déduire l'url de home page
        $emptyUrl = db()->find(self::_type(),"url=''");
        switch (count($emptyUrl)){
            case 1:
                //ok tout va bien
                $r=reset($emptyUrl);
                $r->is_homepage="1";
                db()->store($r);
                throw new PovException("Un problème de home page a été résolu (2). Essayez de recharger.");

            case 0:
                //pas trouvé...
                pov()->events->dispatch("EVENT_ERROR_MISSING_HOME_PAGE");
                throw new PovException("Pas de home page url trouvé et pas d'url vide qui pourrait aller");
            default:
                //gros bobo
                throw new PovException("Attention plusieurs urls sont vides et pourraient potentiellement être la home page");
        }

    }


}