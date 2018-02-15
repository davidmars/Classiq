<?
the()->headerOutput->contentTypeXml();
/* @var $vv VV_sitemap_xml_index */
?>
<?='<?xml version="1.0" encoding="utf-8"?>'?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <? foreach($vv->urls as $urlSet): ?>
    <sitemap>
        <loc><?=$urlSet?></loc>
    </sitemap>
    <? endforeach; ?>
</sitemapindex>