<?php
namespace Classiq\Seo;
use Classiq\Models\Page;

/**
 * An url in an urls set list
 */
class SitemapXmlUrlItem
{



    /**
     * @param Page $record
     * @throws \Pov\PovException
     * @internal param string $controllerUrl The url of this xml.
     */
    public function __construct($record){
        $href=$record->href();
        if($href){
            $this->loc=$record->href()->absolute();
            $this->lasmod=\DateTime::createFromFormat("Y-m-d H:i:s",$record->date_modified);
            $this->priority=$record->urlpage->seo_priority;
        }else{
            die(pov()->debug->dump($record));
        }



        /*
         * TODO opti gérer des thumbnails dans le sitemap
        if($record->thumbnailFind()){
            $this->addImage($record->thumbnailFind());
        }
        */
        if($record->urlpage->seo_change_frequency){
            $this->changeFreq=$record->urlpage->seo_change_frequency;
        }

    }

    /**
     * @var string The url of the xml. Ready to use in a template (means absolute, optimized etc)
     */
    public $loc;
    /**
     * @var string A 0 to 1 value that represents the page priority.
     */
    public $priority="0.1";
    /**
     * @var string The change frequency, use the CHANGE_FREQ_ constants to get normalized values.
     */
    public $changeFreq="monthly";






    /**
     * @return string The change frequency value
     */
    public function getChangeFreq()
    {
        return $this->changeFreq;
    }

    /**
     * @var \DateTime
     */
    private $lasmod;
    /**
     * @param string $lastMod A date that can be interpreted by strtotime
     */
    public function setLasmod($lastMod)
    {
        $this->lasmod=new \DateTime($lastMod);
    }
    /**
     * @return string|bool The last modification date well formatted to be displayed in a sitemap xml (2010-10-11T09:24:33+02:00)
     */
    public function getLasmod()
    {
        if(!isset($this->lasmod) || !$this->lasmod){
            return false;
        }
        return $this->lasmod->format("Y-m-d\TH:i:sP");
    }

    /**
     * @var array The images urls
     */
    private $images= [];

    /**
     * @return array The images urls ready to use in a template
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add an image to the item
     * @param string $url
     */
    public function addImage($url){
        //todo add images in xml simetamp
        $f=Francis::get($url);
        if (  $f->exists()  && $f->isImage()) {
            $this->images[]=GiveMe::urlFile(Robert::fromImage($f->path)->jpg(80),true);
        }
    }
}