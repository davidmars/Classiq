<?php /**
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
        <?php if($pages):?>
            <?php foreach ($pages as $page):?>
                <?php echo $page->views()->wysiwygPreview()->render()?>
            <?php endforeach; ?>
        <?php else: ?>
            ...
        <?php endif; ?>
    </div>

    <div class="cq-col-3" text-right>
        <?php echo $vv->button(
                "...",
                "cq-btn small cq-th-white")
                ->setInnerHTML(
                        pov()->svg->use("cq-search")
                )?>
    </div>
</div>