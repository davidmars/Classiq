<?php

use Classiq\Models\ClassicModelSchema;
use Classiq\Wysiwyg\WysiwygConfig;

/** @var  array $vv */

$types=$vv["types"];
$placeholder=$vv["placeholder"];

?>

<div class="cq-box" cq-new-record>
    <input type="text" name="name" class="fld" autocomplete='off' placeholder="<?=$placeholder?>">
    <div class="js-step-2">
        <?if(count($types)>1):?>

            <label>Type de page</label>
            <?foreach ($types as $type):?>
            <a href="#create-record" text-middle
               record-type="<?=$type?>">
                <?=pov()->svg->use(ClassicModelSchema::icon($type))->addClass("wysiwyg-icon")?>
                <?=ClassicModelSchema::humanType($type)?>
            </a>
            <?endforeach;?>

        <?elseif(count($types)==1):?>
            <div class="cq-txt-right">
                <a href="#create-record" class="cq-btn small"
                   record-type="<?=$types[0]?>">
                    <?=pov()->svg->use(ClassicModelSchema::icon($types[0]))->addClass("wysiwyg-icon")?>
                    <span>Ajouter</span>
                </a>
            </div>

        <?endif?>
    </div>
    <div class="js-link">
    </div>
</div>