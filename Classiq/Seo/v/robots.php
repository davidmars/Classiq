<?php
/**
 * The robots.txt template
 *
 */

use Classiq\Seo\C_robots;
use Classiq\Seo\C_sitemap_xml;
use Pov\System\Header;
the()->headerOutput->code=Header::TXT;
?>
User-agent: *
<?foreach(C_robots::$disallow as $dis):?>
Disallow: <?=$dis."\n"?>
<?endforeach?>

<?if(the()->configProjectUrl->seoActive):?>
Sitemap: <?=C_sitemap_xml::index_url()->absolute()?>
<?endif?>