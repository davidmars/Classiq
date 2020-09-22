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
<?php foreach(C_robots::$disallow as $dis):?>
Disallow: <?php echo $dis."\n"?>
<?php endforeach; ?>

<?php if(the()->configProjectUrl->seoActive):?>
Sitemap: <?php echo C_sitemap_xml::index_url()->absolute()?>
<?php endif; ?>