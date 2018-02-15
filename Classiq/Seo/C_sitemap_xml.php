<?php
namespace Classiq\Seo;

use Classiq\Seo\SitemapXmlIndex;
use Classiq\Seo\SitemapXmlUrlset;
use Pov\MVC\Controller;
use Pov\MVC\View;

View::$possiblesPath[]="Classiq/Seo/v";

/**
 *
 * manage the xml sitemap(s)
 */
Class C_sitemap_xml extends Controller
{
    /**
     * @var string[] Liste des types de records à indexer
     */
    public static $modelTypesToIndex=["page"];
    /**
     * @return \Pov\MVC\ControllerUrl|string
     */
    public static function index_url()
    {
        return self::genUrl("sitemap.xml",false);
    }
    /**
     * @return View
     */
    public function index_run(){
        $vv=new SitemapXmlIndex();
        foreach(self::$modelTypesToIndex as $type){
            $vv->addUrl($type);
        }
        return View::get("sitemap-index",$vv);
    }
    /**
     * Return the path controller to display a list of urls according a record type.
     *
     * @param string $recordType
     * @param int $start the slice index where start the xml
     * @return \Pov\MVC\ControllerUrl|string
     */
    public static function urlset_url($recordType,$start=0)
    {
        return self::genUrl("urlset/$recordType/$start");
    }
    /**
     * @param string $recordType
     * @param int $start
     * @return View
     */
    public function urlset_run($recordType,$start=0)
    {
        $vv=new SitemapXmlUrlset($recordType,$start);
        return  View::get("sitemap-urlset", $vv);
    }
}



