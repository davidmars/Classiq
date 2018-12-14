<?php
use Classiq\Models\ClassicModelSchema;
use Classiq\Wysiwyg\WysiwygConfig;
?>
<?if(cq()->isAdmin()):?>
<section class="browse-records" cq-panel-is-section="browse" <?=$view->attrRefresh()?>>

    <div class="cq-box">
        <?foreach (WysiwygConfig::inst()->recordsWeCanBrowse as $type):?>

            <div class="fld-chk" record-type="<?=$type?>">
            <input class="js-is-record-type" id="browse-record-<?=$type?>" type="radio" name="records" value="<?=$type?>">
            <label class="unstyled" for="browse-record-<?=$type?>">

                <span class="cq-list-item">
                    <?=pov()->svg->use(ClassicModelSchema::icon($type))->addClass("start ")?>
                    <span class="text"><?=ClassicModelSchema::humanType($type,true)?></span>
                    <time class="end cq-fg-disabled"><?=ClassicModelSchema::count($type)?></time>
                </span>

            </label>
        </div>

        <?endforeach;?>
    </div>

    <?=$view->render("./cq-browse-records-list",WysiwygConfig::inst()->recordsWeCanBrowse)?>

</section>
<?endif?>