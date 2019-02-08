<?php


namespace Classiq\Models;

use Classiq\C_classiq;
use Classiq\Seo\SEO;
use Pov\Html\Trace\HtmlTag;
use Pov\Image\ImgUrl;
use Pov\PovException;


/**
 * Une page dans sa version la plus simple
 * @package Classiq\Models
 *
 * @property String $view template à utiliser pour afficher la page
 * @property Urlpage $urlpage Objet d'url relatif
 * @property String $urlpage_id
 *
 *
 * @property String $jsonvars
 * @property String $jsonblocks

 *
 *
 *
 */
class Page extends Classiqmodel
{

    use WithFieldThumbnailTrait;

    static $icon="cq-tests-file-empty";
    static $isPage=true;





    //--------------------------validations---------------------------------------

    /**
     * Renvoie les règles de validation
     * @return array ouù chaque entrée ressemble à "champ"=>"regle"
     * @see https://github.com/Wixel/GUMP#available-validators
     */
    protected function _validators(){
        $v=parent::_validators();
        if($this->id){
            //$v["thumbnail"]="required";
        }
        return $v;
    }



    //---------------------------methodes propres----------------------------------


    /**
     * @var array
     */
    public $blocks=[];

    /**
     * Le lien vers la page à utiliser le plus souvent
     * @return \Pov\MVC\ControllerUrl|string
     */
    public function href(){
        return C_classiq::pageId_url($this->urlpage);
    }

    /**
     * @return HtmlTag une balise A href avec le nom de la page en contenu
     */
    public function htmlLink(){
        $a=new HtmlTag("a",$this->name_lang);
        $a->setAttribute("href",$this->href());
        return $a;
    }



    /**
     * Crée le Urlpage (si il n'existe pas déja) et l'associe à la page.
     * @return Urlpage
     * @throws PovException
     */
    private function createUrl(){
        db()->useWriterCache(false);
        if($this->hasUrl()){
            throw new PovException("Ce ".self::_type()." a déjà un url page");
        }
        if(!$this->bean->id){
            throw new PovException("La page n'a pas d'id on ne peut pas lui associer d'url");
        }

        $existing=db()->findOne("urlpage","related_id='".$this->bean->id."' AND related_type='".$this->modelType()."'");
        if($existing && $existing->id){
            return $existing;
        }

        $url=$this->defaultUrlmodel();
        if($url->id){
            throw new PovException("Attention defaultUrlmodel() est déjà un modèle d'Url enregistré dans la db réécrivez votre methode defaultUrlmodel().");
        }
        $url->related_id=$this->bean->id;
        $url->related_type=$this->modelType();
        db()->store($url);
        return $url;

    }

    /**
     * Retourne un modèle d'url non enregistré et non associé mais qui contient title, url, priority, frequency,
     * Cette fonction est est à overrider pour définir les paramètres par défaut d'un modèle de page en matière de SEO
     * @return Urlpage|\RedBeanPHP\OODBBean
     */
    protected function defaultUrlmodel(){
        /** @var Urlpage $u */
        $u=db()->dispense("urlpage");


        $u->meta_title=$this->name;

        foreach (the()->project->languages as $lang){
            $field="url_$lang";
            $u->$field= pov()->utils->string->clean($this->name,"/");
            $field="meta_title_$lang";
            $u->$field=$this->name;
        }

        $u->seo_priority=0.3;
        $u->seo_change_frequency=SEO::CHANGE_FREQ_YEARLY;
        return $u;
    }

    /**
     * Les données du modèle à renvoyer dans les jsons
     * + hrefRelative
     * + hrefAbsolute
     * + urlPage
     *
     * @return array
     */
    public function apiData()
    {
        $r=parent::apiData();
        $r["isPage"]=true; //permet de savoir par exemple si on doit tenter d'afficher

        if($this->hasUrl()){
            $r["hrefRelative"]=$this->href()->relative();
            $r["hrefAbsolute"]=$this->href()->absolute();
            $r["urlPage"]=$this->urlpage->apiData();
        }
        return $r;
    }

    /**
     * Compte les beans de Page (ou autres types de pages) dont seo_priority > 0
     * @param null $type type de bean souhaité
     * @return int
     */
    public static function countRobotIndexable($type=null)
    {
        if(!$type){
            $type=self::_type();
        }
        $type=strtolower($type);
        return db()->count($type,
            "INNER JOIN urlpage u ON ".$type.".urlpage_id = u.id 
                    WHERE u.seo_priority > 0.0"
        );
    }



    public function open(){
        if(!$this->hasUrl()){
            $this->urlpage=$this->createUrl()->unbox();
            $uid=$this->uid();
            $recursion=isset( self::$_hasUrlPreventRecursion[$uid] );
            if(!$recursion){
                self::$_hasUrlPreventRecursion[$uid]=true;
                db()->store($this);
            }
        }
    }
    private static $_hasUrlPreventRecursion=[];
    /**
     *
     */
    public function update(){
        parent::update();
    }
    /**
     * Après l'enregistrement crée l'url relative si elle n'existe pas
     */
    public function after_update() {
        if(!$this->hasUrl()){
            $this->urlpage=$this->createUrl()->unbox();
            db()->store($this);
        }
        parent::after_update();
    }

    /**
     * S'assure que le modèle a un modèle d'Url et qu'il est bien valide
     * @return bool
     */
    public function hasUrl(){
        if($this->_hasUrlPreventRecursion){
            return true;
        }
        if(!$this->urlpage){
            return false;
        }
        if(!$this->urlpage->meta_title){
            return false;
        }
        if(!$this->urlpage->id){
            return false;
        }
        return true;
    }


}