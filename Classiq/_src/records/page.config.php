<?php
/** @var \Classiq\Models\Page $vv */

use Classiq\Seo\SEO_FRENCH;

?>

<div class="cq-box">

    <fieldset>
    <label>Image de prévisualisation</label>
    <?=$vv->wysiwyg()->field("thumbnail")
        ->file()
        ->setMimeAcceptImagesOnly()
        //->onSavedRefresh("$(this).closest('[data-pov-v-path]')")
        ->button()->render()
    ?>
    </fieldset>

    <fieldset cq-display-if="dev">
    <label>View</label>
    <?=$vv->wysiwyg()->field("view")
        ->string()
        ->input("text","template spécifique")
    ?>
    </fieldset>

    <fieldset cq-display-if="seo">
    <label>Titre de page (seo)</label>
    <?foreach (the()->project->languages as $lang):?>
        <?=$vv->urlpage->wysiwyg()->field("meta_title_$lang")
            ->string()
            ->isTranslated($lang)
            ->input("text","Titre de la page...")
            ->setAttribute("oninput","document.title=this.value")
        ?>
    <?endforeach;?>
    </fieldset>

    <fieldset cq-display-if="seo">
    <label>Description (seo)</label>
    <?foreach (the()->project->languages as $lang):?>
    <?=$vv->urlpage->wysiwyg()->field("meta_description_$lang")
        ->string()
        ->isTranslated($lang)
        ->textarea("Description de la page")
    ?>
    <?endforeach;?>
    </fieldset>

    <?if(!$vv->urlpage->is_homepage):?>
        <fieldset cq-display-if="seo">
        <label>Url</label>
        <?=$vv->urlpage->wysiwyg()->field("url")
            ->string()
            ->textarea("url de la page")
        ?>
        <?=$vv->urlpage->wysiwyg()->field("stricturl")
        ->string()
        ->select(["avec id (recommandé)"=>"0","sans id"=>"1"])
        ?>
        <small>Url réelle:</small>
        <pre title="<?=$vv->href()?>"><?=$vv->href()?></pre>
        </fieldset>
    <?endif?>

    <fieldset cq-display-if="seo">
    <label>Priorité (seo)</label>
    <?=$vv->urlpage->wysiwyg()->field("seo_priority")
        ->string()
        ->input("range")
        ->setAttribute("min","0")
        ->setAttribute("max","1")
        ->setAttribute("step","0.1")
        //->setAttribute("list","seo_prority_labels")
    ?>
    </fieldset>

    <fieldset cq-display-if="seo">
    <label><?=SEO_FRENCH::CHANGE_FREQ_LABEL?> (seo)</label>
    <?=$vv->urlpage->wysiwyg()->field("seo_change_frequency")
        ->string()
        ->select(SEO_FRENCH::CHANGE_FREQ_ALL)
        //->setAttribute("list","seo_prority_labels")
    ?>
    </fieldset>
    <?/* pas encore pris en charge par les navigateurs  :(
    <datalist id="seo_prority_labels">
        <option value="0" label="Pas référencé">
        <option value="10" label="Page sans intérêt">
        <option value="20">
        <option value="30">
        <option value="40">
        <option value="50">
        <option value="60">
        <option value="70">
        <option value="80">
        <option value="90" label="Une de principales page du site">
        <option value="100" label="Page la plus importante">
    </datalist>
    */?>



</div>


