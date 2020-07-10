<?php
use Classiq\C_classiq;
use Classiq\Seo\C_robots;
use Classiq\Seo\C_sitemap_xml;
use Pov\Defaults\C_dev;

$svgCollections=glob("dist/svg-collection/*.html");
?>
<label><?php echo cq()->tradWysiwyg("Options")?></label>
<?php echo $view->render("cq-display-if/cq-display-control")?>

<?php if(count(the()->project->languages)>1):?>
    <?php
    $langArray=[];
    foreach (the()->project->languages as $code){
        $langArray[\Localization\Lang::getByCode($code)->name]=$code;
    }
    ?>
    <label><?php echo cq()->tradWysiwyg("Activer/désactiver des langues")?></label>
    <div class="cq-box">
        <?php echo cq()->configStorage()->wysiwyg()
            ->field("vars.langActives")
            ->listString()
            ->checkboxes($langArray);?>
    </div>
<?php endif; ?>


<label><?php echo cq()->tradWysiwyg("Liens utiles")?></label>
<div class="cq-box">

    <?php echo $view->renderIfValid("cq-admin/cq-big-menu/config/links.before")?>

    <?php //---------------icone, gfx etc.............?>

    <label><?php echo cq()->tradWysiwyg("icônes")?></label>
    <?php foreach ($svgCollections as $link):?>
    <div>
        <a class="cq-list-item" href="<?php echo the()->fmkHttpRoot."/$link"?>" target="_blank">
        <?php echo pov()->svg->use("cq-grid")->addClass("start")?>
        <span><?php echo $link?></span>
        </a>
    </div>
    <?php endforeach; ?>
    <?php /*
    <label>Charte graphique</label>
    <a class="cq-list-item" href="<?php echo C_classiq::quickView_url("gfx")?>" target="gfx">
        <?php echo pov()->svg->use("cq-art-typo")->addClass("start")?>
        <div>Style guide</div>
    </a>
    */?>
    <?php //---------------dev.............?>

    <div cq-display-if="dev">
        <label>Dev</label>
        <?php /*
        <a class="cq-list-item" href="<?php echo C_classiq::quickView_url("tests")?>" target="tests">
            <?php echo pov()->svg->use("cq-lab")->addClass("start")?>
            La page de tests
        </a>
        */?>
        <a class="cq-list-item" href="<?php echo C_dev::page_url("logs")?>" target="dev-logs">
            <?php echo pov()->svg->use("cq-device-terminal")->addClass("start")?>
            Logs
        </a>
        <a title="Efface les entrees de la DB où on ne trouve pas de fichier en relation." class="cq-list-item"
           href="<?php echo C_classiq::quickView_url("utils/clean-broken-filerecords")?>" target="dev-logs">
            <?php echo pov()->svg->use("cq-trash")->addClass("start")?>
            <?php echo cq()->tradWysiwyg("Trash Filrecord(s) corrompus")?>
        </a>
    </div>

    <?php //---------------seo.............?>

    <label>SEO</label>
    <a class="cq-list-item" href="<?php echo  C_sitemap_xml::index_url()?>" target="sitemap">
        <?php echo pov()->svg->use("cq-code")->addClass("start")?>
        SiteMap XML
    </a>
    <a class="cq-list-item" href="<?php echo  C_robots::index_url()?>" target="robots">
        <?php echo pov()->svg->use("cq-block")->addClass("start")?>
        Robots.txt
    </a>

    <?php echo $view->renderIfValid("cq-admin/cq-big-menu/config/links.after")?>

</div>


