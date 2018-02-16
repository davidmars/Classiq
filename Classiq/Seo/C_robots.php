<?php
namespace Classiq\Seo;
use Pov\MVC\Controller;
use Pov\MVC\View;

View::$possiblesPath[]=__DIR__."/v";

/**
 *
 * manage robots.txt
 */
Class C_robots extends Controller {

    /**
     * @return string
     */
    public static function index_url(){
        return self::genUrl("",true,"txt");
    }
    public function index_run(){

       if(!the()->configProjectUrl->seoActive){
            self::$disallow[]="/";
       }
       return View::get("robots",null);
    }

    /**
     * @var string[] La listes des urls à ne pas indexer
     */
    public static $disallow=[
        "/admin/",
        "/login/"
    ];

}



