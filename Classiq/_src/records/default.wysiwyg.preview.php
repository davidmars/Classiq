<?php
/**
 * @var Classiqmodel $vv
 */

use Classiq\Models\ClassicModelSchema;
use Classiq\Models\Classiqmodel;

?>
<div class="preview-record" <?=$view->attrRefresh($vv->uid())?>>
    <?if($vv->id):?>
        <span class="icon"><?= pov()->svg->use(ClassicModelSchema::icon($vv))?></span>
        <div>
            <div class="title" title="<?=$vv->name?>"><?=$vv->name?></div>
            <div class="type"><?=$vv->modelType()?>@<?=$vv->id?></div>
        </div>
    <?else:?>
        ...
    <?endif?>
</div>