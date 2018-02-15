<?
/**
 * Efface tous les files Filerecord qui n'ont pas de fichier relatif
 */

/** @var Filerecord[] $files */

use Classiq\Models\Filerecord;
$doit= the()->request("je-suis-certain") === "1";
?>
<?if(the()->human->isDev(true)):?>


    <?php
        $files=db()->find("filerecord");
        $totalTodelete=0;
        $totalOk=0;
        $total=count($files);
    ?>

    <h4><?=$total?> Fichiers</h4>
    <?foreach ($files as $f):?>

        <?=$f->name?><br>
        <code>(<?=$f->uid()?>)</code><br>
        <code><?=$f->localPath()?></code><br>
        <?if(!$f->isOk()):?>
            <?php $totalTodelete++ ?>
            <b style='color:red;'>fichier introuvable</b><br>
            <?if($doit):?>
                <? db()->trash($f) ?>
                <b style='color:red;'>Effacé</b><br>
            <?else:?>
                <b style='color:blue;'>fichier pas effacé</b><br>
            <?endif?>
        <?else:?>
            <?php $totalOk++ ?>
        <?endif;?>

        <hr>



    <?endforeach;?>




    <?if(!$doit):?>
        <hr>
        <?=$totalOk?> records sans soucis.<br>
        <?=$totalTodelete?> records à effacer.<br>
        <hr>
        <form method="post">
            <input type="hidden" name="je-suis-certain" value="1">
            <input type="submit" value="Effacer les <?=$totalTodelete?> fichiers">
        </form>
    <?endif?>

<?else:?>
Il faut être loggué en dev.
<?endif?>









