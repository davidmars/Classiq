<?php
/**
 * Efface tous les urlpage qui n'ont pas de pages relative
 */

/** @var Urlpage[] $urls */

use Classiq\Models\Urlpage;
$doit= the()->request("je-suis-certain") === "1";
?>
<?php if(the()->human->isDev(true)):?>


    <?php echo $view->render("./menu")?>

    <?php
        $urls=db()->find("urlpage");
        $totalTodelete=0;
        $totalOk=0;
        $total=count($urls);
    ?>

    <h4><?php echo $total?> Urls</h4>
    <?php foreach ($urls as $f):?>

        <?php echo $f->url_lang?><br>
        <code>(<?php echo $f->uid()?>)</code><br>
        Type de page : <code>(<?php echo $f->related_type?>)</code><br>
        <code><?php echo $f->localPath()?></code><br>
        <?php if(!$f->getPage(false)):?>
            <?php $totalTodelete++ ?>
            <b style='color:red;'>Page introuvable</b><br>
            <?php if($doit):?>
                <?php db()->trash($f) ?>
                <b style='color:red;'>Effacé</b><br>
            <?php else: ?>
                <b style='color:blue;'>url pas effacée</b><br>
            <?php endif; ?>
        <?php else: ?>
            <?php $totalOk++ ?>
        <?php endif; ?>

        <hr>



    <?php endforeach; ?>




    <?php if(!$doit):?>
        <hr>
        <?php echo $totalOk?> urls sans soucis.<br>
        <?php echo $totalTodelete?> urls à effacer.<br>
        <hr>
        <form method="post">
            <input type="hidden" name="je-suis-certain" value="1">
            <input type="submit" value="Effacer les <?php echo $totalTodelete?> url">
        </form>
    <?php endif; ?>

<?php else: ?>
Il faut être loggué en dev.
<?php endif; ?>









