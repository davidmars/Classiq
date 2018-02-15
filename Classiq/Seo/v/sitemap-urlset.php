<?
use Classiq\Seo\SitemapXmlUrlset;
the()->headerOutput->contentTypeXml();
/* @var $vv SitemapXmlUrlset */
?>
<?='<?xml version="1.0" encoding="utf-8"?>'?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" total="<?=$vv->total?>">

    <? foreach($vv->urls as $url): ?>
    <url>
        <loc><![CDATA[<?=$url->loc?>]]></loc>
        <?if($url->getLasmod()):?>
            <lastmod><?=$url->getLasmod()?></lastmod>
        <?endif?>
        <changefreq><?=$url->getChangeFreq()?></changefreq>
        <priority><?=$url->priority?></priority>


        <? foreach($url->getImages() as $im): ?>
            <image:image>
                <image:loc><?=$im?></image:loc>
            </image:image>
        <? endforeach; ?>


    </url>
    <? endforeach; ?>
</urlset>