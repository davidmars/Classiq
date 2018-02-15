<?php
use Classiq\C_classiq;
use Classiq\Seo\C_robots;
use Classiq\Seo\C_sitemap_xml;
use Pov\Defaults\C_dev;

$svgCollections=glob("dist/svg-collection/*.html");
?>
<label>Options</label>
<?=$view->render("cq-display-if/cq-display-control")?>

<label>Liens utiles</label>
<div class="cq-box">

    <?//---------------icone, gfx etc.............?>

    <label>icônes</label>
    <?foreach ($svgCollections as $link):?>
    <div>
        <a class="cq-list-item" href="<?=the()->fmkHttpRoot."/$link"?>" target="_blank">
        <?=pov()->svg->use("cq-grid")->addClass("start")?>
        <span><?=$link?></span>
        </a>
    </div>
    <?endforeach;?>

    <label>Charte graphique</label>
    <a class="cq-list-item" href="<?=C_classiq::quickView_url("gfx")?>" target="gfx">
        <?=pov()->svg->use("cq-art-typo")->addClass("start")?>
        <div>Style guide</div>
    </a>

    <?//---------------dev.............?>

    <div cq-display-if="dev">
        <label>Dev</label>
        <a class="cq-list-item" href="<?=C_classiq::quickView_url("tests")?>" target="tests">
            <?=pov()->svg->use("cq-lab")->addClass("start")?>
            La page de tests
        </a>
        <a class="cq-list-item" href="<?=C_dev::page_url("logs")?>" target="dev-logs">
            <?=pov()->svg->use("cq-device-terminal")->addClass("start")?>
            Logs
        </a>
        <a title="Efface les entrees de la DB où on ne trouve pas de fichier en relation." class="cq-list-item"
           href="<?=C_classiq::quickView_url("utils/clean-broken-filerecords")?>" target="dev-logs">
            <?=pov()->svg->use("cq-trash")->addClass("start")?>
            Trash Filrecord(s) corrompus
        </a>
    </div>

    <?//---------------seo.............?>

    <label>SEO</label>
    <a class="cq-list-item" href="<?= C_sitemap_xml::index_url()?>" target="sitemap">
        <?=pov()->svg->use("cq-code")->addClass("start")?>
        SiteMap XML
    </a>
    <a class="cq-list-item" href="<?= C_robots::index_url()?>" target="robots">
        <?=pov()->svg->use("cq-block")->addClass("start")?>
        Robots.txt
    </a>
</div>
