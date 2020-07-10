<?php

use Classiq\Models\Classiqmodel;
use Classiq\Wysiwyg\WysiwygConfig;

$collections = [];
foreach (WysiwygConfig::inst()->recordsWeCanSelect as $type){
    $type=strtolower($type);
    $collections[$type]=db()->findAll($type,"ORDER BY name");
}

?>
<div class="js-list" <?php echo $view->attrRefresh()?>>
    <?php foreach ($collections as $type=>$beans):?>
        <div class="cq-cols">
            <?php foreach ($beans as $bean):?>
                <?php
                /** @var  ClassiqModel $bean */
                ?>
                <div class="cq-col-6">
                <?php if( $bean->id):?>
                    <div class="fld-chk" record-type="<?php echo $type?>">
                        <input class="js-is-record-checker" id="<?php echo $bean->uid()?>" type="checkbox" name="records" value="<?php echo $bean->uid()?>">
                        <label class="unstyled" for="<?php echo $bean->uid()?>"><?php echo $bean->views()->wysiwygPreview()->render()?></label>
                    </div>
                <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>