<?php

use Classiq\Models\Classiqmodel;
use Pov\MVC\View;

/** @var ClassiqModel $vv $calledModel */

//récupère la hiérarchie des modèles
$calledModel=get_class($vv);
$classesHierarchy=$vv->views()->getClassHierarchy();
$classesHierarchy=array_reverse($classesHierarchy);


?>
<div cq-edit-record-form big-menu-module="admin-page-<?=$calledModel?>-<?=$vv->id?>" <?=$view->attrRefresh($vv->uid())?>>

    <?//main class...ici on prend en compte les custom view?>
    <?if($vv->view):?>
        <?
            $path=$vv->views()->getViewPath("config");
            $box=View::get($path,$vv);
        ?>
        <?=$box->renderIfValid()?>
    <?endif;?>

    <?foreach ($classesHierarchy as $modelClassName):?>
        <?php
        $boxBefore=$vv->views()->configByClass($modelClassName.".before");
        ?>
        <?if($boxBefore):?>
            <?=$boxBefore->render()?>
        <?endif;?>

        <?php
            $box=$vv->views()->configByClass($modelClassName);
        ?>
        <?if($box):?>
            <?=$box->render()?>
        <?endif;?>

        <?php
        $boxAfter=$vv->views()->configByClass($modelClassName.".after");
        ?>
        <?if($boxAfter):?>
            <?=$boxAfter->render()?>
        <?endif;?>

    <?endforeach;?>
</div>    
