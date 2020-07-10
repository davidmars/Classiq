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
    <h4><?php echo $db->prop_currentDB()?> / <?php echo pov()->debug->type($db->dbType())?> </h4>

<?php foreach (\Pov\Db\RedBean\PhpClassGenerator::genAll() as $c):?>
    <h2><?php echo $c->tableName?></h2>
    <code><?php echo $c->getFilePath()?></code>
    <pre class="prettyprint lang-php"><?php echo htmlentities($c->getPhpCode()).PHP_EOL?></pre>

    <h4>Indexes (à virer une fois que ce sera géré)</h4>
    <pre>
    <?php foreach ($c->generateRelations() as $idx):?>
    <?php ar_dump($idx)?>
    <?php endforeach; ?>
    </pre>
<?php endforeach; ?>





</div>


