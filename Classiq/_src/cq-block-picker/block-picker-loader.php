<?php
/**
 * Item dans la librairaie de template
 */
/** @var string $vv Chemin vers le template */
//todo opti charger les templates tous d'un coup

use Classiq\Models\JsonModels\ListItem;
use Pov\MVC\View;


$path="";
$label="...";
$config="";
$preview="";
$err="";

if(is_string($vv)){
    if(!View::isValid($vv)){
        if(isset(ListItem::$debugPath[$vv])){
            $vv=ListItem::$debugPath[$vv];
        }
    }
    if(View::isValid($vv)){
        $path=$vv;

        $preview=$path.".preview";
        $config=$path.".config";
        $options=$path.".options";
        $label=basename($path);
        if(!View::isValid($preview)){
            $preview="";
        }
        if(!View::isValid($config)){
            $config="";
        }
        if(!View::isValid($options)){
            $options="";
        }
    }else{
        $err="$vv n'est pas une vue valide";
    }
}else{
    $err="vv n'est pas String mais ".pov()->debug->type($vv);
}

?>
<?if($path):?>
    <div cq-ico-txt path="<?=$path?>" config="<?=$config?>" options="<?=$options?>">
            <?if($preview):?>
                <?=$view->render($preview)?>
            <?else:?>
                <?// preview par défaut?>
                <?=$view->render("./default-block.preview",$label)?>
            <?endif?>
    </div>
<?else:?>
    <div>
        <div cq-box class="th-danger">oups... <?=$err?></div>
    </div>
<?endif;?>

