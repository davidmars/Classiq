<?php
/**
 * @var Pov\Db\RedBean\PhpClassGenerator $vv
 */
echo '<?php'.PHP_EOL;
?>

<?php if(\Pov\Db\RedBean\PhpClassGenerator::$namespace):?>
namespace <?php echo \Pov\Db\RedBean\PhpClassGenerator::$namespace?>;
<?php endif; ?>


/**
 * Class <?php echo $vv->className.PHP_EOL?>
 *
 *
<?php foreach ($vv->props as $fieldName=>$field):?>
 * @property <?php echo $field->type?> $<?php echo $field->name." ".$field->comment.PHP_EOL?>
<?php endforeach; ?>
 *
<?php foreach ($vv->propsRelations as $fieldName=>$field):?>
 * @property <?php echo $field->type?> $<?php echo $field->name." ".$field->comment.PHP_EOL?>
<?php endforeach; ?>
<?php /*
 *
 * ------------------------One to many------------------
 *
<?php foreach ($vv->oneToMany as $fieldName=>$fieldType):?>
 * @relation <?php echo $fieldType?> $<?php echo $fieldName?><?php echo PHP_EOL?>
 * @relationSet <?php echo $fieldType?> $<?php echo $fieldName?><?php echo PHP_EOL?>
 *  <?php echo PHP_EOL?>
 * @relationGet $this->fetchAs( 'userprofile' )->userprofile;<?php echo PHP_EOL?>
 * @relationGet $this->fetchAs( 'person' )-><?php echo $fieldName?>;<?php echo PHP_EOL?>
<?php endforeach; ?>
 *
 *
 *
 * ------------------------Many to one------------------
 *
<?php foreach ($vv->manyToOne as $fieldName=>$fieldType):?>
 * @relation <?php echo $fieldType?> $<?php echo $fieldName.PHP_EOL?>
 * $c->fetchAs( 'person' )->teacher;
<?php endforeach; ?>
 *
*/
?>
 *
 */
abstract class <?php echo $vv->className?> extends \RedBeanPHP\OODBBean
{
    /**
    * @inheritdoc
    * @return <?php echo $vv->className?><?php echo PHP_EOL?>
    */
    public function with( $sql, $bindings = [] )
    {
    }
    /**
    * Box the bean to its model.
    * @return \<?php echo $vv->modelBoxClassName?><?php echo PHP_EOL?>
    */
    public function box()
    {
    }

}