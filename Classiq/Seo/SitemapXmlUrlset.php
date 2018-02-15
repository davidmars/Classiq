<?php
namespace Classiq\Seo;
use Classiq\Models\Page;

/**
 * Represents an urlset xml page
 */
class SitemapXmlUrlset
{
    /**
     * @param string $recordType Something like R_post, R_tag, R_etc
     * @param int $start the slice index where start the xml
     */
    public function __construct($recordType,$start=0){
        $this->total=Page::countRobotIndexable($recordType);
        $records=db()->findAll($recordType,
            "INNER JOIN urlpage u ON ".$recordType.".urlpage_id = u.id 
                 WHERE u.seo_priority > 0.0 
                 ORDER BY date_created DESC LIMIT $start, ".SitemapXmlIndex::$howManyByXml);

        /** @var Page $rec */
        foreach($records as $rec){
            $this->addUrl($rec);
        }
    }

    public $total=0;

    /**
     * @var SitemapXmlUrlItem[] The list of pages
     */
    public $urls= [];

    /**
     * @param Page $record
     * @return SitemapXmlUrlItem Return the SitemapXmlUrlItem created object.
     */
    public function addUrl($record){
        $urlSet=new SitemapXmlUrlItem($record);
        $this->urls[]=$urlSet;
        return $urlSet;
    }
}