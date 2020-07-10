<?php use Classiq\Seo\SitemapXmlUrlset;
the()->headerOutput->contentTypeXml();
/* @var $vv SitemapXmlUrlset */
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" total="<?php echo $vv->total?>">

    <?php foreach($vv->urls as $url): ?>
    <url>
        <loc><![CDATA[<?php echo $url->loc?>]]></loc>
        <?php if($url->getLasmod()):?>
            <lastmod><?php echo $url->getLasmod()?></lastmod>
        <?php endif; ?>
        <changefreq><?php echo $url->getChangeFreq()?></changefreq>
        <priority><?php echo $url->priority?></priority>


        <?php foreach($url->getImages() as $im): ?>
            <image:image>
                <image:loc><?php echo $im?></image:loc>
            </image:image>
        <?php endforeach; ?>


    </url>
    <?php endforeach; ?>
</urlset>