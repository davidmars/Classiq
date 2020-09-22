<?php
/** @var \Classiq\Models\Urlpage $vv */

use Classiq\Seo\SEO_TRANSLATED;

?>

<div cq-display-if="seo" class="wysiwyg-config-seo cq-display-if-block">
    <div>
        <h4>SEO</h4>
        <div class="cq-box ">

            <fieldset>
                <label><?php echo cq()->tradWysiwyg("Titre de page (seo)")?></label>
                <?php foreach (the()->project->languages as $lang):?>
                    <?php echo $vv->wysiwyg()->field("meta_title_$lang")
                        ->string()
                        ->isTranslated($lang)
                        ->input("text",cq()->tradWysiwyg("Titre de la page..."))
                        ->setAttribute("oninput","document.title=this.value")
                    ?>
                <?php endforeach; ?>
            </fieldset>

            <fieldset>
                <label><?php echo cq()->tradWysiwyg("Description (seo)")?></label>
                <?php foreach (the()->project->languages as $lang):?>
                    <?php echo $vv->wysiwyg()->field("meta_description_$lang")
                        ->string()
                        ->isTranslated($lang)
                        ->textarea(cq()->tradWysiwyg(cq()->tradWysiwyg("Description de la page")))
                    ?>
                <?php endforeach; ?>
            </fieldset>

            <?php if(!$vv->is_homepage):?>
                <fieldset>

                    <label>Url</label>
                    <?php foreach (the()->project->languages as $lang):?>
                        <?php echo $vv->wysiwyg()->field("url_$lang")
                            ->string()
                            ->isTranslated($lang)
                            ->textarea(cq()->tradWysiwyg("url de la page"))
                        ?>
                    <?php endforeach; ?>

                    <?php echo $vv->wysiwyg()->field("stricturl")
                        ->string()
                        ->select([cq()->tradWysiwyg("avec id (recommandé)")=>"0",cq()->tradWysiwyg("sans id")=>"1"])
                    ?>
                    <small>Url réelle:</small>
                    <pre title="<?php echo $vv->href()?>"><?php echo $vv->href()?></pre>
                </fieldset>
            <?php endif; ?>

            <fieldset>
                <label><?php echo cq()->tradWysiwyg("Priorité (seo)")?></label>
                <?php echo $vv->wysiwyg()->field("seo_priority")
                    ->string()
                    ->setDefaultValue("0.0")
                    ->input("range")
                    ->setAttribute("min","0")
                    ->setAttribute("max","1")
                    ->setAttribute("step","0.1")
                //->setAttribute("list","seo_prority_labels")
                ?>
            </fieldset>

            <fieldset>
                <label><?php echo cq()->tradWysiwyg("Fréquence de mise à jour")?> (seo)</label>
                <?php echo $vv->wysiwyg()->field("seo_change_frequency")
                    ->string()
                    ->select(SEO_TRANSLATED::CHANGE_FREQ_ALL())
                //->setAttribute("list","seo_prority_labels")
                ?>
            </fieldset>

            <?php /* pas encore pris en charge par les navigateurs  :(
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
    </div>

</div>

