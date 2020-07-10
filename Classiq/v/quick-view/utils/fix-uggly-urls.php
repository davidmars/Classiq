<?php
/**
 * Fait le tour des urls pour nettoyer les pas jolies
 */

/** @var Urlpage[] $urls */

use Classiq\Models\Urlpage;
$doit= the()->request("je-suis-certain") === "1";

/**
 * @param string $url
 * @param stran $lang
 * @param Urlpage $bean
 * @return string
 */
function uggly($url,$lang,$bean){
    $moche=false;
    if($bean && $bean->is_homepage){
        return "";
    }

    if(!preg_match("/[a-z]+/",$url,$m)){
        $moche=true;
    }
    if($moche && $bean && $bean->getPage(false)){
        $var="name_$lang";
        $repl=$bean->getPage(false)->$var;
        if(!$repl){
            $repl=$bean->getPage(false)->name;
        }
        if($repl){
            $repl=strtolower(utils()->string->clean($repl));
            $repl=trim($repl,"-/ _");
            return $repl;
        }

    }


    return "";
}

?>
<?php if(the()->human->isDev(true)):?>


    <?php echo $view->render("./menu")?>

    <?php
        $urls=db()->find("urlpage");
        $totalChange=0;
        $totalOk=0;
        $total=count($urls);
    ?>

    <h4><?php echo $total?> Urls</h4>
    <table>

        <tr>
            <th>Name de la page</th>
            <th>uid</th>
            <th>type</th>
            <th>priorité</th>
            <?php foreach (the()->project->languages as $lang):?>
                <th>url <?php echo $lang?></th>
                <th class="replacement">remplacement <?php echo $lang?></th>
            <?php endforeach; ?>
        </tr>


        <?php foreach ($urls as $f):?>
            <tr>
                <td>
                    <?php if(!$f->getPage(false)):?>
                        <b style='color:red;'>Page introuvable</b><br>
                    <?php else: ?>
                        <?php echo $f->getPage(false)->name?>
                    <?php endif; ?>
                </td>
                <td><?php echo $f->uid()?></td>
                <td><?php echo $f->related_type?></td>
                <td><?php echo $f->seo_priority?></td>
            <?php foreach (the()->project->languages as $lang):?>
                <td>
                    <?php
                    $var="url_$lang";
                    ?>
                    <?php echo $f->$var?>
                </td>
                <td class="replacement">
                    <?php
                    $remplacement=uggly($f->$var,$lang,$f);
                    ?>
                    <?php echo $remplacement?>
                    <?php if($remplacement):?>
                        <?php
                            $totalChange++;
                        ?>
                        <?php if($doit):?>
                            <?php
                                $f->$var=$remplacement;
                                db()->store($f);
                            ?>
                            <br>L'url A été remplacée !
                        <?php else: ?>
                            <br>L'url n'a pas été remplacée
                        <?php endif; ?>
                    <?php else: ?>
                        <?php
                        $totalOk++;
                        ?>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>


            </tr>

        <?php endforeach; ?>
    </table>

    <style>
        table{
            border: 1px solid #666;
            border-collapse: collapse;
        }
        td,th{
            font-family: monospace;
            font-size: 12px;
            border: 1px solid #666;
            padding: 5px;
        }
        .replacement{
            color: #f00;
        }
    </style>



    <?php if(!$doit):?>
        <hr>
        <?php echo $totalOk?> urls sans soucis.<br>
        <?php echo $totalChange?> urls à modifier.<br>
        <hr>
        <form method="post">
            <input type="hidden" name="je-suis-certain" value="1">
            <input type="submit" value="Modifier les <?php echo $totalChange?> url">
        </form>
    <?php endif; ?>

<?php else: ?>
Il faut être loggué en dev.
<?php endif; ?>









