<?
/**
 * @var FieldFile $vv
 */

use Classiq\Models\Filerecord;
use Classiq\Wysiwyg\FieldsTyped\FieldFile;

/** @var Filerecord $fileRecord */
$fileRecord=$vv->fileRecord();
$uid=uniqid("uploader")
?>
<div class="cq-cols cq-field-upload" <?=$vv->attr()?> >
    <div class="cq-col-12">
        <div data-progress-bar="<?=$uid?>" cq-progress-bar progress="0" min="0" max="100">
            <div class="bar"></div>
        </div>
    </div>
    <div class="cq-col-9">
        <?if($fileRecord):?>
            <?=$fileRecord->views()->wysiwygPreview()->render()?>
        <?else:?>
            <?=Filerecord::getNew()->views()->wysiwygPreview()->render()?>
        <?endif?>
    </div>
    <div class="cq-col-3" text-right>
        <div class="input-file-wrap btn">
            <button class="cq-btn cq-th-white small"><?=cq()->icoWysiwyg("cloud-upload")?></button>
            <input data-related-progress="<?=$uid?>" type="file" <?=$vv->mimeTypeAccept?>>
        </div>
        <div class="cq-txt-center">
            <code data-progress-text="<?=$uid?>" class="js-upload-progress"></code>
        </div>

    </div>
</div>