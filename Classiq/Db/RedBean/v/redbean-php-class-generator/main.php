<?php
the()->htmlLayout()->meta->title="R_models";
the()->htmlLayout()->install()->googleCodePrettify("desert");
$db=Classic\Db\RedBean\PovRedBeanFacade::inst();
?>
<style>
    pre.prettyprinted{
        padding: 15px;
        background-color: #fffefc;
        border: 1px solid #f9f7f1;
    }
</style>
<div class="uk-container">

    <h1>Modèles de la base de données</h1>
    <h4><?=$db->prop_currentDB()?> / <?=pov()->debug->type($db->dbType())?> </h4>

<?foreach (\Pov\Db\RedBean\PhpClassGenerator::genAll() as $c):?>
    <h2><?=$c->tableName?></h2>
    <code><?=$c->getFilePath()?></code>
    <pre class="prettyprint lang-php"><?=htmlentities($c->getPhpCode()).PHP_EOL?></pre>

    <h4>Indexes (à virer une fois que ce sera géré)</h4>
    <pre>
    <?foreach ($c->generateRelations() as $idx):?>
    <?var_dump($idx)?>
    <?endforeach;?>
    </pre>
<?endforeach;?>





</div>


