<?php /**
 * @var FieldFile $vv
 */

use Classiq\Models\Filerecord;
use Classiq\Wysiwyg\FieldsTyped\FieldFile;

/** @var Filerecord $fileRecord */
$fileRecord=$vv->fileRecord();
$uid=uniqid("uploader")
?>
<div class="cq-cols cq-field-upload" <?php echo $vv->attr()?> >
    <div class="cq-col-12">
        <div data-progress-bar="<?php echo $uid?>" cq-progress-bar progress="0" min="0" max="100">
            <div class="bar"></div>
        </div>
    </div>
    <div class="cq-col-7">
        <?php if($fileRecord):?>
            <?php echo $fileRecord->views()->wysiwygPreview()->render()?>
        <?php else: ?>
            <?php echo Filerecord::getNew()->views()->wysiwygPreview()->render()?>
        <?php endif; ?>
    </div>
    <div class="cq-col-5" text-right>
        <button class="cq-btn cq-th-white small"><?php echo pov()->svg->use("cq-trash")?></button>
        <div class="input-file-wrap cq-btn-file-wrap">
            <button class="cq-btn cq-th-white small"><?php echo pov()->svg->use("cq-cloud-upload")?></button>
            <input data-related-progress="<?php echo $uid?>" type="file" <?php echo $vv->mimeTypeAccept?>>
        </div>
        <div class="cq-txt-center">
            <code data-progress-text="<?php echo $uid?>" class="js-upload-progress"></code>
        </div>

    </div>
</div>