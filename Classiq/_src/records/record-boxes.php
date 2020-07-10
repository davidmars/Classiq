<?php

use Classiq\Models\Classiqmodel;
use Pov\MVC\View;

/** @var ClassiqModel $vv $calledModel */

//récupère la hiérarchie des modèles
$calledModel=get_class($vv);
$classesHierarchy=$vv->views()->getClassHierarchy();
$classesHierarchy=array_reverse($classesHierarchy);


?>
<div cq-edit-record-form="<?php echo $vv->modelType()?>" big-menu-module="admin-page-<?php echo $calledModel?>-<?php echo $vv->id?>" <?php echo $view->attrRefresh($vv->uid())?>>

    <?php //main class...ici on prend en compte les custom view?>
    <?php if($vv->view):?>
        <?php
            $path=$vv->views()->getViewPath("config");
            $box=View::get($path,$vv);
        ?>
        <?php echo $box->renderIfValid()?>
    <?php endif; ?>

    <?php foreach ($classesHierarchy as $modelClassName):?>
        <?php
        $boxBefore=$vv->views()->configByClass($modelClassName.".before");
        ?>
        <?php if($boxBefore):?>
            <?php echo $boxBefore->render()?>
        <?php endif; ?>

        <?php
            $box=$vv->views()->configByClass($modelClassName);
        ?>
        <?php if($box):?>
            <?php echo $box->render()?>
        <?php endif; ?>

        <?php
        $boxAfter=$vv->views()->configByClass($modelClassName.".after");
        ?>
        <?php if($boxAfter):?>
            <?php echo $boxAfter->render()?>
        <?php endif; ?>

    <?php endforeach; ?>
</div>    
