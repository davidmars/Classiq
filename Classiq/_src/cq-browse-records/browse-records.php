<?php
use Classiq\Models\ClassicModelSchema;
use Classiq\Wysiwyg\WysiwygConfig;
?>
<?php if(cq()->isAdmin()):?>
<section class="browse-records" cq-panel-is-section="browse" <?php echo $view->attrRefresh()?>>

    <div class="cq-box">
        <?php foreach (WysiwygConfig::inst()->recordsWeCanBrowse as $type):?>

            <div class="fld-chk" record-type="<?php echo $type?>">
            <input class="js-is-record-type" id="browse-record-<?php echo $type?>" type="radio" name="records" value="<?php echo $type?>">
            <label class="unstyled" for="browse-record-<?php echo $type?>">

                <span class="cq-list-item">
                    <?php echo pov()->svg->use(ClassicModelSchema::icon($type))->addClass("start ")?>
                    <span class="text"><?php echo ClassicModelSchema::humanType($type,true)?></span>
                    <time class="end cq-fg-disabled"><?php echo ClassicModelSchema::count($type)?></time>
                </span>

            </label>
        </div>

        <?php endforeach; ?>
    </div>

    <?php echo $view->render("./cq-browse-records-list",WysiwygConfig::inst()->recordsWeCanBrowse)?>

</section>
<?php endif; ?>