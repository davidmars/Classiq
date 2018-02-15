<?php

use Classiq\Models\Classiqmodel;
use Classiq\Wysiwyg\WysiwygConfig;

$collections = [];
foreach (WysiwygConfig::inst()->recordsWeCanSelect as $type){
    $type=strtolower($type);
    $collections[$type]=db()->findAll($type,"ORDER BY name");
}

?>
<div class="js-list" <?=$view->attrRefresh()?>>
    <?foreach ($collections as $type=>$beans):?>
        <div class="cq-cols">
            <?foreach ($beans as $bean):?>
                <?php
                /** @var  ClassiqModel $bean */
                ?>
                <div class="cq-col-6">
                <?if( $bean->id):?>
                    <div class="fld-chk" record-type="<?=$type?>">
                        <input class="js-is-record-checker" id="<?=$bean->uid()?>" type="checkbox" name="records" value="<?=$bean->uid()?>">
                        <label class="unstyled" for="<?=$bean->uid()?>"><?=$bean->views()->wysiwygPreview()->render()?></label>
                    </div>
                <?endif?>
                </div>
            <?endforeach;?>
        </div>
    <?endforeach;?>
</div>