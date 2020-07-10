<?php /**
 * Efface tous les files Filerecord qui n'ont pas de fichier relatif
 */

/** @var Filerecord[] $files */

use Classiq\Models\Filerecord;
$doit= the()->request("je-suis-certain") === "1";
?>
<?php if(the()->human->isDev(true)):?>


    <?php
        $files=db()->find("filerecord");
        $totalTodelete=0;
        $totalOk=0;
        $total=count($files);
    ?>

    <h4><?php echo $total?> Fichiers</h4>
    <?php foreach ($files as $f):?>

        <?php echo $f->name?><br>
        <code>(<?php echo $f->uid()?>)</code><br>
        <code><?php echo $f->localPath()?></code><br>
        <?php if(!$f->isOk()):?>
            <?php $totalTodelete++ ?>
            <b style='color:red;'>fichier introuvable</b><br>
            <?php if($doit):?>
                <?php db()->trash($f) ?>
                <b style='color:red;'>Effacé</b><br>
            <?php else: ?>
                <b style='color:blue;'>fichier pas effacé</b><br>
            <?php endif; ?>
        <?php else: ?>
            <?php $totalOk++ ?>
        <?php endif; ?>

        <hr>



    <?php endforeach; ?>




    <?php if(!$doit):?>
        <hr>
        <?php echo $totalOk?> records sans soucis.<br>
        <?php echo $totalTodelete?> records à effacer.<br>
        <hr>
        <form method="post">
            <input type="hidden" name="je-suis-certain" value="1">
            <input type="submit" value="Effacer les <?php echo $totalTodelete?> fichiers">
        </form>
    <?php endif; ?>

<?php else: ?>
Il faut être loggué en dev.
<?php endif; ?>









