<?php
namespace Classiq\Seo;
use Classiq\Seo\C_sitemap_xml;
use Classiq\Models\Page;

/**
 * Represents a sitemapindex page
 */
class SitemapXmlIndex
{
    /**
     * @var array The list of urlset urls to display
     */
    public $urls= [];

    /**
     * @var int number of xml in each sitemap urlset
     */
    public static $howManyByXml=200;

    /**
     * Add an urlset item to the list if there is records related to this record type.
     * @param string $type A record type (something like R_post, R_tag R_etc...)
     */
    public function addUrl($type){
        $count=Page::countRobotIndexable($type);
        //$count=theDb()->select()->whereType($r_type)->_and()->whereOver("seoPriority",0)->count();
        if($count>0){
            for($start=0;$start<$count;$start+=self::$howManyByXml){
                $this->urls[]=C_sitemap_xml::urlset_url($type,$start)->absolute();
            }

        }
    }
}