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
        $customMenu=$path.".custom-menu";
        $label=basename($path);
        if(!View::isValid($preview)){
            $preview="";
        }
        if(!View::isValid($config)){
            $config="";
        }
        if(!View::isValid($customMenu)){
            $customMenu="";
        }
    }else{
        $err="$vv n'est pas une vue valide";
    }
}else{
    $err="vv n'est pas String mais ".pov()->debug->type($vv);
}

?>
<?php if($path):?>
    <div cq-ico-txt path="<?php echo $path?>" config="<?php echo $config?>" custom-menu="<?php echo $customMenu?>">
            <?php if($preview):?>
                <?php echo $view->render($preview)?>
            <?php else: ?>
                <?php echo $view->render("./default-block.preview",$label)?>
            <?php endif; ?>
    </div>
<?php else: ?>
    <div>
        <div cq-box class="th-danger">oups... <?php echo $err?></div>
    </div>
<?php endif; ?>

