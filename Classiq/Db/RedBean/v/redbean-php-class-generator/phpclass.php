<?php
/**
 * @var Pov\Db\RedBean\PhpClassGenerator $vv
 */
echo '<?php'.PHP_EOL;
?>

<?if(\Pov\Db\RedBean\PhpClassGenerator::$namespace):?>
namespace <?=\Pov\Db\RedBean\PhpClassGenerator::$namespace?>;
<?endif?>


/**
 * Class <?=$vv->className.PHP_EOL?>
 *
 *
<?foreach ($vv->props as $fieldName=>$field):?>
 * @property <?=$field->type?> $<?=$field->name." ".$field->comment.PHP_EOL?>
<?endforeach;?>
 *
<?foreach ($vv->propsRelations as $fieldName=>$field):?>
 * @property <?=$field->type?> $<?=$field->name." ".$field->comment.PHP_EOL?>
<?endforeach;?>
<?/*
 *
 * ------------------------One to many------------------
 *
<?foreach ($vv->oneToMany as $fieldName=>$fieldType):?>
 * @relation <?=$fieldType?> $<?=$fieldName?><?=PHP_EOL?>
 * @relationSet <?=$fieldType?> $<?=$fieldName?><?=PHP_EOL?>
 *  <?=PHP_EOL?>
 * @relationGet $this->fetchAs( 'userprofile' )->userprofile;<?=PHP_EOL?>
 * @relationGet $this->fetchAs( 'person' )-><?=$fieldName?>;<?=PHP_EOL?>
<?endforeach;?>
 *
 *
 *
 * ------------------------Many to one------------------
 *
<?foreach ($vv->manyToOne as $fieldName=>$fieldType):?>
 * @relation <?=$fieldType?> $<?=$fieldName.PHP_EOL?>
 * $c->fetchAs( 'person' )->teacher;
<?endforeach;?>
 *
*/
?>
 *
 */
abstract class <?=$vv->className?> extends \RedBeanPHP\OODBBean
{
    /**
    * @inheritdoc
    * @return <?=$vv->className?><?=PHP_EOL?>
    */
    public function with( $sql, $bindings = [] )
    {
    }
    /**
    * Box the bean to its model.
    * @return \<?=$vv->modelBoxClassName?><?=PHP_EOL?>
    */
    public function box()
    {
    }

}