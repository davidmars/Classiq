<?php

use Classiq\Models\ClassicModelSchema;
use Classiq\Wysiwyg\WysiwygConfig;

/** @var  array $vv */

$types=$vv["types"];
$placeholder=$vv["placeholder"];

?>

<div class="cq-box" cq-new-record>
    <input type="text" name="name" class="fld" autocomplete='off' placeholder="<?php echo $placeholder?>">
    <div class="js-step-2">
        <?php if(count($types)>1):?>

            <label>Type de page</label>
            <?php foreach ($types as $type):?>
            <a href="#create-record" text-middle
               record-type="<?php echo $type?>">
                <?php echo pov()->svg->use(ClassicModelSchema::icon($type))->addClass("wysiwyg-icon")?>
                <?php echo ClassicModelSchema::humanType($type)?>
            </a>
            <?php endforeach; ?>

        <?php elseif(count($types)==1):?>
            <div class="cq-txt-right">
                <a href="#create-record" class="cq-btn small"
                   record-type="<?php echo $types[0]?>">
                    <?php echo pov()->svg->use(ClassicModelSchema::icon($types[0]))->addClass("wysiwyg-icon")?>
                    <span>Ajouter</span>
                </a>
            </div>

        <?php endif; ?>
    </div>
    <div class="js-link">
    </div>
</div>