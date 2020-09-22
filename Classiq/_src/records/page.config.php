<?php
/** @var \Classiq\Models\Page $vv */

use Classiq\Seo\SEO_TRANSLATED;

?>

<div class="cq-box wysiwyg-config-page">

    <fieldset>
        <label><?php echo cq()->tradWysiwyg("Image de prévisualisation")?></label>
        <?php echo $vv->wysiwyg()->field("thumbnail")
            ->file()
            ->setMimeAcceptImagesOnly()
            //->onSavedRefresh("$(this).closest('[data-pov-v-path]')")
            ->button()->render()
        ?>
    </fieldset>

    <fieldset cq-display-if="dev">
        <label>View</label>
        <?php echo $vv->wysiwyg()->field("view")
            ->string()
            ->input("text","template spécifique")
        ?>
    </fieldset>

</div>

<?php echo $view->render("records/page.config.seo",$vv->urlpage)?>


