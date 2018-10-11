<?
/**
 * @var FieldRecordPicker $vv
 */

use Classiq\Wysiwyg\FieldsTyped\FieldRecordPicker;
$pages=$vv->field->valueAsRecords();
if($pages && $vv->multiple===false){
    $pages=[$pages[0]];
}

?>
<div cq-field-records class="cq-cols" >

    <div cq-sortable class="cq-col-9" context-menu-size="small">
        <?if($pages):?>
            <?foreach ($pages as $page):?>
                <?=$page->views()->wysiwygPreview()->render()?>
            <?endforeach;?>
        <?else:?>
            ...
        <?endif?>
    </div>

    <div class="cq-col-3" text-right>
        <div class="input-file-wrap cq-btn-file-wrap" >
            <button class="cq-btn cq-th-white small" <?=$vv->attr()?>><?=pov()->svg->use("cq-cloud-upload")?></button>
            <input type="file" <?=$vv->mimeTypeAccept?> <?=$vv->multiple?"multiple":""?>
            >
        </div>
    </div>
</div>