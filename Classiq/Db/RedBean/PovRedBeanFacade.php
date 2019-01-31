<?php

namespace Classiq\Db\RedBean;

use Pov\System\AbstractSingleton;
use RedBeanPHP\BeanCollection;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R as R;
use Pov\PovException;
use RedBeanPHP\QueryWriter as QueryWriter;
use RedBeanPHP\Adapter\DBAdapter as DBAdapter;
use RedBeanPHP\RedException\SQL as SQLException;
use RedBeanPHP\Logger as Logger;
use RedBeanPHP\Logger\RDefault as RDefault;
use RedBeanPHP\Logger\RDefault\Debug as Debug;
use RedBeanPHP\Adapter as Adapter;
use RedBeanPHP\QueryWriter\AQueryWriter as AQueryWriter;
use RedBeanPHP\RedException as RedException;
use RedBeanPHP\BeanHelper\SimpleFacadeBeanHelper as SimpleFacadeBeanHelper;
use RedBeanPHP\Driver\RPDO as RPDO;
use RedBeanPHP\SimpleModel;
use RedBeanPHP\ToolBox;
use RedBeanPHP\Util\MultiLoader as MultiLoader;
use RedBeanPHP\Util\Transaction as Transaction;
use RedBeanPHP\Util\Dump as Dump;
use RedBeanPHP\Util\DispenseHelper as DispenseHelper;
use RedBeanPHP\Util\ArrayTool as ArrayTool;
use RedBeanPHP\Util\QuickExport as QuickExport;
use RedBeanPHP\Util\MatchUp as MatchUp;
use RedBeanPHP\Util\Look as Look;
use RedBeanPHP\Util\Diff as Diff;

/**
 * RedBean Facade
 *
 * Version Information
 * RedBean Version @version 5
 *
 * This class hides the object landscape of
 * RedBeanPHP behind a single letter class providing
 * almost all functionality with simple static calls.
 *
 * @file    RedBeanPHP/Facade.php
 * @author  Gabor de Mooij and the RedBeanPHP Community
 * @license BSD/GPLv2
 *
 * @copyright
 * copyright (c) G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community
 * This source file is subject to the BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 *
 *
 * @method static PovRedBeanFacade inst()
 *
 */
class PovRedBeanFacade extends AbstractSingleton
{

    const DB_TYPE_PostgreSQL="PostgreSQL";
    const DB_TYPE_SQLiteT="SQLiteT";
    const DB_TYPE_CUBRID="CUBRID";
    const DB_TYPE_MySQL="MySQL";
    const DB_TYPE_SQLServer="SQLServer";

    public function dbType(){

        $writers = array(
            'pgsql'  => 'PostgreSQL',
            'sqlite' => 'SQLiteT',
            'cubrid' => 'CUBRID',
            'mysql'  => 'MySQL',
            'sqlsrv' => 'SQLServer',
        );

        $className=pov()->utils->phpAnalyzer->getClassWithoutNameSpaces($this->getWriter());
        return $className;

    }

    /**
     * POV only
     * Efface tous les beans dont le champ 'expires' est dépassé
     * @param string $type type de bean
     */
    public function trashExpires($type){

        $now=date("Y-m-d H:i:s");
        $expireds=R::find($type," expires < '$now'  OR expires IS NULL");
        R::trashAll($expireds);
    }

    /**
     * Pour savoir si un champ existe dans un bean
     * @param string $fieldName
     * @param OODBBean $bean
     * @return bool false si le champ existe pas
     */
    public function isExistingField($fieldName, $bean)
    {
        $fields=R::inspect($bean->getMeta("type"));
        return array_key_exists($fieldName,$fields);
    }
    /**
     * Pour savoir si un champ a un nom compatible
     * @param string $fieldName
     * @return bool false si le champ existe pas
     */
    public function isValidFieldName($fieldName)
    {
        return true;
    }

    /**
     * @param OODBBean $object
     * @return int|string
     */
    public function storeObject($object){
        $bean=self::dispenseObject($object);
        return R::store($bean);
    }

    /**
     * @param $object
     * @return array|OODBBean
     */
    public function dispenseObject($object){
        $ar=[];
        foreach (get_object_vars($object) as $k=>$v){
            switch ($k){
                case "id":
                    if(!empty($v)){
                        $ar[$k]=$v;
                    }
                    break;

                default:
                    $ar[$k]=$v;
            }
        }
        return R::dispense($ar);
    }



    //--------------------removed / replaced--------------------------

    /**
     * @var string
     */
    //public static $currentDB = '';
    /**
     * @return string
     */
    public function prop_currentDB(){
        return R::$currentDB;
    }

    //---------------------a partir de là c'est redbean en non statique----------------------------

    /**
     * RedBeanPHP version constant.
     */
    const C_REDBEANPHP_VERSION = '4.3';

    /**
     * @var ToolBox
     */
    public static $toolbox;

    /**
     * Not in use (backward compatibility SQLHelper)
     */
    public static $f;



    /**
     * @var array
     */
    public static $toolboxes = array();


    /**
     * Returns the RedBeanPHP version string.
     * The RedBeanPHP version string always has the same format "X.Y"
     * where X is the major version number and Y is the minor version number.
     * Point releases are not mentioned in the version string.
     *
     * @return string
     */
    public function getVersion()
    {
        return R::getVersion();
    }

    /**
     * Tests the connection.
     * Returns TRUE if connection has been established and
     * FALSE otherwise.
     *
     * @return boolean
     */
    public function testConnection()
    {
        return R::testConnection();
    }

    /**
     * Kickstarts redbean for you. This method should be called before you start using
     * RedBean. The Setup() method can be called without any arguments, in this case it will
     * try to create a SQLite database in /tmp called red.db (this only works on UNIX-like systems).
     *
     * @param string  $dsn      Database connection string
     * @param string  $username Username for database
     * @param string  $password Password for database
     * @param boolean $frozen   TRUE if you want to setup in frozen mode
     *
     * @return ToolBox
     */
    public function setup( $dsn = NULL, $username = NULL, $password = NULL, $frozen = FALSE )
    {

        return R::setup($dsn,$username,$password,$frozen);
    }
    public $setups=[];

    /**
     * Toggles Narrow Field Mode.
     * See documentation in QueryWriter.
     *
     * @param boolean $mode TRUE = Narrow Field Mode
     *
     * @return void
     */
    public function setNarrowFieldMode( $mode )
    {
        return R::setNarrowFieldMode($mode);
    }

    /**
     * Wraps a transaction around a closure or string callback.
     * If an Exception is thrown inside, the operation is automatically rolled back.
     * If no Exception happens, it commits automatically.
     * It also supports (simulated) nested transactions (that is useful when
     * you have many methods that needs transactions but are unaware of
     * each other).
     *
     * Example:
     *
     * <code>
     * $from = 1;
     * $to = 2;
     * $amount = 300;
     *
     * R::transaction(function() use($from, $to, $amount)
     * {
     *   $accountFrom = R::load('account', $from);
     *   $accountTo = R::load('account', $to);
     *   $accountFrom->money -= $amount;
     *   $accountTo->money += $amount;
     *   R::store($accountFrom);
     *   R::store($accountTo);
     * });
     * </code>
     *
     * @param callable $callback Closure (or other callable) with the transaction logic
     *
     * @return mixed
     */
    public function transaction( $callback )
    {
        return R::transaction($callback);
    }

    /**
     * Adds a database to the facade, afterwards you can select the database using
     * selectDatabase($key), where $key is the name you assigned to this database.
     *
     * Usage:
     *
     * <code>
     * R::addDatabase( 'database-1', 'sqlite:/tmp/db1.txt' );
     * R::selectDatabase( 'database-1' ); //to select database again
     * </code>
     *
     * This method allows you to dynamically add (and select) new databases
     * to the facade. Adding a database with the same key will cause an exception.
     *
     * @param string      $key    ID for the database
     * @param string      $dsn    DSN for the database
     * @param string      $user   user for connection
     * @param NULL|string $pass   password for connection
     * @param bool        $frozen whether this database is frozen or not
     *
     * @return void
     */
    public function addDatabase( $key, $dsn, $user = NULL, $pass = NULL, $frozen = FALSE )
    {
        return R::addDatabase($key, $dsn, $user , $pass , $frozen);
    }

    /**
     * Determines whether a database identified with the specified key has
     * already been added to the facade. This function will return TRUE
     * if the database indicated by the key is available and FALSE otherwise.
     *
     * @param string $key the key/name of the database to check for
     *
     * @return boolean
     */
    public function hasDatabase( $key )
    {
        return R::hasDatabase($key);
    }

    /**
     * Selects a different database for the Facade to work with.
     * If you use the R::setup() you don't need this method. This method is meant
     * for multiple database setups. This method selects the database identified by the
     * database ID ($key). Use addDatabase() to add a new database, which in turn
     * can be selected using selectDatabase(). If you use R::setup(), the resulting
     * database will be stored under key 'default', to switch (back) to this database
     * use R::selectDatabase( 'default' ). This method returns TRUE if the database has been
     * switched and FALSE otherwise (for instance if you already using the specified database).
     *
     * @param  string $key Key of the database to select
     *
     * @return boolean
     */
    public function selectDatabase( $key )
    {
        return R::selectDatabase($key);
    }

    /**
     * Toggles DEBUG mode.
     * In Debug mode all SQL that happens under the hood will
     * be printed to the screen and/or logged.
     * If no database connection has been configured using R::setup() or
     * R::selectDatabase() this method will throw an exception.
     *
     * There are 2 debug styles:
     *
     * Classic: separate parameter bindings, explicit and complete but less readable
     * Fancy:   interpersed bindings, truncates large strings, highlighted schema changes
     *
     * Fancy style is more readable but sometimes incomplete.
     *
     * The first parameter turns debugging ON or OFF.
     * The second parameter indicates the mode of operation:
     *
     * 0 Log and write to STDOUT classic style (default)
     * 1 Log only, class style
     * 2 Log and write to STDOUT fancy style
     * 3 Log only, fancy style
     *
     * This function always returns the logger instance created to generate the
     * debug messages.
     *
     * @param boolean $tf   debug mode (TRUE or FALSE)
     * @param integer $mode mode of operation
     *
     * @return RDefault
     * @throws RedException
     */
    public function debug( $tf = TRUE, $mode = 0 )
    {
        return R::debug($tf,$mode);
    }

    /**
     * Turns on the fancy debugger.
     * In 'fancy' mode the debugger will output queries with bound
     * parameters inside the SQL itself. This method has been added to
     * offer a convenient way to activate the fancy debugger system
     * in one call.
     *
     * @param boolean $toggle TRUE to activate debugger and select 'fancy' mode
     *
     * @return void
     */
    public function fancyDebug( $toggle = TRUE )
    {
        return R::fancyDebug($toggle);
    }

    /**
     * Inspects the database schema. If you pass the type of a bean this
     * method will return the fields of its table in the database.
     * The keys of this array will be the field names and the values will be
     * the column types used to store their values.
     * If no type is passed, this method returns a list of all tables in the database.
     *
     * @param string $type Type of bean (i.e. table) you want to inspect
     *
     * @return array
     */
    public function inspect( $type = NULL )
    {
        return R::inspect($type);
    }

    /**
     * Stores a bean in the database. This method takes a
     * OODBBean Bean Object $bean and stores it
     * in the database. If the database schema is not compatible
     * with this bean and RedBean runs in fluid mode the schema
     * will be altered to store the bean correctly.
     * If the database schema is not compatible with this bean and
     * RedBean runs in frozen mode it will throw an exception.
     * This function returns the primary key ID of the inserted
     * bean.
     *
     * The return value is an integer if possible. If it is not possible to
     * represent the value as an integer a string will be returned.
     *
     * @param OODBBean|SimpleModel $bean bean to store
     *
     * @return integer|string
     */
    public function store( $bean )
    {
        /*
        if($bean->unbox()){
            pov()->log->warning("appel a db->store(".$bean->unbox()->getMeta("type")."-".$bean->unbox()->getID().")",[$bean]);
        }else{
            pov()->log->warning("appel a db->store(".$bean->getMeta("type")."-".$bean->getID().")",[$bean]);
        }
        */


        return R::store($bean);
    }

    /**
     * Toggles fluid or frozen mode. In fluid mode the database
     * structure is adjusted to accomodate your objects. In frozen mode
     * this is not the case.
     *
     * You can also pass an array containing a selection of frozen types.
     * Let's call this chilly mode, it's just like fluid mode except that
     * certain types (i.e. tables) aren't touched.
     *
     * @param boolean|array $trueFalse
     */
    public function freeze( $tf = TRUE )
    {
        return R::freeze($tf);
    }

    /**
     * Loads multiple types of beans with the same ID.
     * This might look like a strange method, however it can be useful
     * for loading a one-to-one relation.
     *
     * Usage:
     * list( $author, $bio ) = R::loadMulti( 'author, bio', $id );
     *
     * @param string|array $types the set of types to load at once
     * @param mixed        $id    the common ID
     *
     * @return OODBBean
     */
    public function loadMulti( $types, $id )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Loads a bean from the object database.
     * It searches for a OODBBean Bean Object in the
     * database. It does not matter how this bean has been stored.
     * RedBean uses the primary key ID $id and the string $type
     * to find the bean. The $type specifies what kind of bean you
     * are looking for; this is the same type as used with the
     * dispense() function. If RedBean finds the bean it will return
     * the OODB Bean object; if it cannot find the bean
     * RedBean will return a new bean of type $type and with
     * primary key ID 0. In the latter case it acts basically the
     * same as dispense().
     *
     * Important note:
     * If the bean cannot be found in the database a new bean of
     * the specified type will be generated and returned.
     *
     * @param string  $type    type of bean you want to load
     * @param integer $id      ID of the bean you want to load
     * @param string  $snippet string to use after select  (optional)
     *
     * @return OODBBean
     */
    public function load( $type, $id, $snippet = NULL )
    {
        return R::load($type,$id,$snippet);
    }

    /**
     * Same as load, but selects the bean for update, thus locking the bean.
     * @see Facade::load
     *
     * @param string  $type    type of bean you want to load
     * @param integer $id      ID of the bean you want to load
     * @param string  $snippet string to use after select
     *
     * @return OODBBean
     */
    public function loadForUpdate( $type, $id )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Removes a bean from the database.
     * This function will remove the specified OODBBean
     * Bean Object from the database.
     *
     * This facade method also accepts a type-id combination,
     * in the latter case this method will attempt to load the specified bean
     * and THEN trash it.
     *
     * @param string|OODBBean|SimpleModel $bean bean you want to remove from database
     * @param integer                     $id   ID if the bean to trash (optional, type-id variant only)
     *
     * @return void
     */
    public function trash( $beanOrType, $id = NULL )
    {
        return R::trash($beanOrType, $id);
    }



    /**
     * Dispenses a new RedBean OODB Bean for use with
     * the rest of the methods.
     *
     * @param string|array $typeOrBeanArray   type or bean array to import
     * @param integer      $number            number of beans to dispense
     * @param boolean      $alwaysReturnArray if TRUE always returns the result as an array
     *
     * @return array|OODBBean
     */
    public function dispense( $typeOrBeanArray, $num = 1, $alwaysReturnArray = FALSE )
    {
        return R::dispense($typeOrBeanArray, $num, $alwaysReturnArray);
    }

    /**
     * Takes a comma separated list of bean types
     * and dispenses these beans. For each type in the list
     * you can specify the number of beans to be dispensed.
     *
     * Usage:
     *
     * <code>
     * list( $book, $page, $text ) = R::dispenseAll( 'book,page,text' );
     * </code>
     *
     * This will dispense a book, a page and a text. This way you can
     * quickly dispense beans of various types in just one line of code.
     *
     * Usage:
     *
     * <code>
     * list($book, $pages) = R::dispenseAll('book,page*100');
     * </code>
     *
     * This returns an array with a book bean and then another array
     * containing 100 page beans.
     *
     * @param string  $order      a description of the desired dispense order using the syntax above
     * @param boolean $onlyArrays return only arrays even if amount < 2
     *
     * @return array
     */
    public function dispenseAll( $order, $onlyArrays = FALSE )
    {
        return R::dispenseAll($order, $onlyArrays);
    }

    /**
     * Convience method. Tries to find beans of a certain type,
     * if no beans are found, it dispenses a bean of that type.
     * Note that this function always returns an array.
     *
     * @param  string $type     type of bean you are looking for
     * @param  string $sql      SQL code for finding the bean
     * @param  array  $bindings parameters to bind to SQL
     *
     * @return array
     */
    public function findOrDispense( $type, $sql = NULL, $bindings = array() )
    {
        return R::findOrDispense($type,$sql,$bindings);
    }

    /**
     * Same as findOrDispense but returns just one element.
     *
     * @param  string $type     type of bean you are looking for
     * @param  string $sql      SQL code for finding the bean
     * @param  array  $bindings parameters to bind to SQL
     *
     * @return OODBBean
     */
    public function findOneOrDispense( $type, $sql = NULL, $bindings = array() )
    {
        return R::findOneOrDispense($type,$sql,$bindings);
    }

    /**
     * Finds a bean using a type and a where clause (SQL).
     * As with most Query tools in RedBean you can provide values to
     * be inserted in the SQL statement by populating the value
     * array parameter; you can either use the question mark notation
     * or the slot-notation (:keyname).
     *
     * @param string $type     the type of bean you are looking for
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return array
     */
    public function find( $type, $sql = NULL, $bindings = array() )
    {
        return R::find($type,$sql,$bindings);
    }

    /**
     * @see Facade::find
     *      The findAll() method differs from the find() method in that it does
     *      not assume a WHERE-clause, so this is valid:
     *
     * R::findAll('person',' ORDER BY name DESC ');
     *
     * Your SQL does not have to start with a valid WHERE-clause condition.
     *
     * @param string $type     the type of bean you are looking for
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return array
     */
    public function findAll( $type, $sql = NULL, $bindings = array() )
    {
        return R::findAll($type,$sql,$bindings);
    }

    /**
     * @see Facade::find
     * The variation also exports the beans (i.e. it returns arrays).
     *
     * @param string $type     the type of bean you are looking for
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return array
     */
    public function findAndExport( $type, $sql = NULL, $bindings = array() )
    {
        return R::findAndExport($type,$sql,$bindings);
    }

    /**
     * @see Facade::find
     * This variation returns the first bean only.
     *
     * @param string $type     the type of bean you are looking for
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return OODBBean
     */
    public function findOne( $type, $sql = NULL, $bindings = array() )
    {
        return R::findOne($type,$sql,$bindings);
    }

    /**
     * @see Facade::find
     * This variation returns the last bean only.
     *
     * @param string $type     the type of bean you are looking for
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return OODBBean
     */
    public function findLast( $type, $sql = NULL, $bindings = array() )
    {
        return R::findLast($type,$sql,$bindings);
    }

    /**
     * Finds a bean collection.
     * Use this for large datasets.
     *
     * @param string $type     the type of bean you are looking for
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return BeanCollection
     */
    public function findCollection( $type, $sql = NULL, $bindings = array() )
    {
        return R::findCollection($type,$sql,$bindings);
    }

    /**
     * Finds multiple types of beans at once and offers additional
     * remapping functionality. This is a very powerful yet complex function.
     * For details see Finder::findMulti().
     *
     * @see Finder::findMulti()
     *
     * @param array|string $types      a list of bean types to find
     * @param string|array $sqlOrArr   SQL query string or result set array
     * @param array        $bindings   SQL bindings
     * @param array        $remappings an array of remapping arrays containing closures
     *
     * @return array
     */
    public function findMulti( $types, $sql, $bindings = array(), $remappings = array() )
    {
        return R::findMulti($types, $sql, $bindings, $remappings );
    }

    /**
     * Returns an array of beans. Pass a type and a series of ids and
     * this method will bring you the corresponding beans.
     *
     * important note: Because this method loads beans using the load()
     * function (but faster) it will return empty beans with ID 0 for
     * every bean that could not be located. The resulting beans will have the
     * passed IDs as their keys.
     *
     * @param string $type type of beans
     * @param array  $ids  ids to load
     *
     * @return array
     */
    public function batch( $type, $ids )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * @see Facade::batch
     *
     * Alias for batch(). Batch method is older but since we added so-called *All
     * methods like storeAll, trashAll, dispenseAll and findAll it seemed logical to
     * improve the consistency of the Facade API and also add an alias for batch() called
     * loadAll.
     *
     * @param string $type type of beans
     * @param array  $ids  ids to load
     *
     * @return array
     */
    public function loadAll( $type, $ids )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql       SQL query to execute
     * @param array  $bindings  a list of values to be bound to query parameters
     *
     * @return integer
     */
    public function exec( $sql, $bindings = [])
    {
        return exec($sql,$bindings);
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getAll( $sql, $bindings = array() )
    {
        return R::getAll($sql,$bindings);
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return string
     */
    public function getCell( $sql, $bindings = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getRow( $sql, $bindings = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getCol( $sql, $bindings = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     * Results will be returned as an associative array. The first
     * column in the select clause will be used for the keys in this array and
     * the second column will be used for the values. If only one column is
     * selected in the query, both key and value of the array will have the
     * value of this field for each row.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getAssoc( $sql, $bindings = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     * Results will be returned as an associative array indexed by the first
     * column in the select.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getAssocRow( $sql, $bindings = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns the insert ID for databases that support/require this
     * functionality. Alias for R::getAdapter()->getInsertID().
     *
     * @return mixed
     */
    public function getInsertID()
    {
        return R::getInsertID();
    }

    /**
     * Makes a copy of a bean. This method makes a deep copy
     * of the bean.The copy will have the following features.
     * - All beans in own-lists will be duplicated as well
     * - All references to shared beans will be copied but not the shared beans themselves
     * - All references to parent objects (_id fields) will be copied but not the parents themselves
     * In most cases this is the desired scenario for copying beans.
     * This function uses a trail-array to prevent infinite recursion, if a recursive bean is found
     * (i.e. one that already has been processed) the ID of the bean will be returned.
     * This should not happen though.
     *
     * Note:
     * This function does a reflectional database query so it may be slow.
     *
     * @deprecated
     * This function is deprecated in favour of R::duplicate().
     * This function has a confusing method signature, the R::duplicate() function
     * only accepts two arguments: bean and filters.
     *
     * @param OODBBean $bean  bean to be copied
     * @param array    $trail for internal usage, pass array()
     * @param boolean  $pid   for internal usage
     * @param array    $white white list filter with bean types to duplicate
     *
     * @return array
     */
    public function dup( $bean, $trail = array(), $pid = FALSE, $filters = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Makes a deep copy of a bean. This method makes a deep copy
     * of the bean.The copy will have the following:
     *
     * * All beans in own-lists will be duplicated as well
     * * All references to shared beans will be copied but not the shared beans themselves
     * * All references to parent objects (_id fields) will be copied but not the parents themselves
     *
     * In most cases this is the desired scenario for copying beans.
     * This function uses a trail-array to prevent infinite recursion, if a recursive bean is found
     * (i.e. one that already has been processed) the ID of the bean will be returned.
     * This should not happen though.
     *
     * Note:
     * This function does a reflectional database query so it may be slow.
     *
     * Note:
     * This is a simplified version of the deprecated R::dup() function.
     *
     * @param OODBBean $bean  bean to be copied
     * @param array    $white white list filter with bean types to duplicate
     *
     * @return array
     */
    public function duplicate( $bean, $filters = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Exports a collection of beans. Handy for XML/JSON exports with a
     * Javascript framework like Dojo or ExtJS.
     * What will be exported:
     *
     * * contents of the bean
     * * all own bean lists (recursively)
     * * all shared beans (not THEIR own lists)
     *
     * @param    array|OODBBean $beans   beans to be exported
     * @param    boolean        $parents whether you want parent beans to be exported
     * @param    array          $filters whitelist of types
     *
     * @return array
     */
    public function exportAll( $beans, $parents = FALSE, $filters = array())
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Selects case style for export.
     * This will determine the case style for the keys of exported beans (see exportAll).
     * The following options are accepted:
     *
     * * 'default' RedBeanPHP by default enforces Snake Case (i.e. book_id is_valid )
     * * 'camel'   Camel Case   (i.e. bookId isValid   )
     * * 'dolphin' Dolphin Case (i.e. bookID isValid   ) Like CamelCase but ID is written all uppercase
     *
     * @warning RedBeanPHP transforms camelCase to snake_case using a slightly different
     * algorithm, it also converts isACL to is_acl (not is_a_c_l) and bookID to book_id.
     * Due to information loss this cannot be corrected. However if you might try
     * DolphinCase for IDs it takes into account the exception concerning IDs.
     *
     * @param string $caseStyle case style identifier
     *
     * @return void
     */
    public function useExportCase( $caseStyle = 'default' )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Converts a series of rows to beans.
     * This method converts a series of rows to beans.
     * The type of the desired output beans can be specified in the
     * first parameter. The second parameter is meant for the database
     * result rows.
     *
     * Usage:
     *
     * <code>
     * $rows = R::getAll( 'SELECT * FROM ...' )
     * $beans = R::convertToBeans( $rows );
     * </code>
     *
     * As of version 4.3.2 you can specify a meta-mask.
     * Data from columns with names starting with the value specified in the mask
     * will be transferred to the meta section of a bean (under data.bundle).
     *
     * <code>
     * $rows = R::getAll( 'SELECT FROM... COUNT(*) AS extra_count ...' );
     * $beans = R::convertToBeans( $rows );
     * $bean = reset( $beans );
     * $data = $bean->getMeta( 'data.bundle' );
     * $extra_count = $data['extra_count'];
     * </code>
     *
     * @param string $type type of beans to produce
     * @param array  $rows must contain an array of array
     *
     * @return array
     */
    public function convertToBeans( $type, $rows, $metamask = NULL )
    {
        return R::convertToBeans( $type, $rows, $metamask);
    }

    /**
     * Just like converToBeans, but for one bean.
     * @see convertToBeans for more details.
     *
     * @param string $type type of beans to produce
     * @param array  $row  one row from the database
     *
     * @return array
     */
    public function convertToBean( $type, $row, $metamask = NULL )
    {
        return R::convertToBeans($type, $row, $metamask);
    }

    /**
     * Part of RedBeanPHP Tagging API.
     * Tests whether a bean has been associated with one ore more
     * of the listed tags. If the third parameter is TRUE this method
     * will return TRUE only if all tags that have been specified are indeed
     * associated with the given bean, otherwise FALSE.
     * If the third parameter is FALSE this
     * method will return TRUE if one of the tags matches, FALSE if none
     * match.
     *
     * @param  OODBBean $bean bean to check for tags
     * @param  array    $tags list of tags
     * @param  boolean  $all  whether they must all match or just some
     *
     * @return boolean
     */
    public function hasTag( $bean, $tags, $all = FALSE )
    {
        return R::hasTag($bean, $tags, $all );
    }

    /**
     * Part of RedBeanPHP Tagging API.
     * Removes all specified tags from the bean. The tags specified in
     * the second parameter will no longer be associated with the bean.
     *
     * @param  OODBBean $bean    tagged bean
     * @param  array    $tagList list of tags (names)
     *
     * @return void
     */
    public function untag( $bean, $tagList )
    {
        R::untag($bean, $tagList );
    }

    /**
     * Part of RedBeanPHP Tagging API.
     * Tags a bean or returns tags associated with a bean.
     * If $tagList is NULL or omitted this method will return a
     * comma separated list of tags associated with the bean provided.
     * If $tagList is a comma separated list (string) of tags all tags will
     * be associated with the bean.
     * You may also pass an array instead of a string.
     *
     * @param OODBBean $bean    bean to tag
     * @param mixed    $tagList tags to attach to the specified bean
     *
     * @return string
     */
    public function tag( OODBBean $bean, $tagList = NULL )
    {
        return R::tag($bean,$tagList);
    }

    /**
     * Part of RedBeanPHP Tagging API.
     * Adds tags to a bean.
     * If $tagList is a comma separated list of tags all tags will
     * be associated with the bean.
     * You may also pass an array instead of a string.
     *
     * @param OODBBean $bean    bean to tag
     * @param array    $tagList list of tags to add to bean
     *
     * @return void
     */
    public function addTags( OODBBean $bean, $tagList )
    {
        R::addTags($bean,$tagList);
    }

    /**
     * Part of RedBeanPHP Tagging API.
     * Returns all beans that have been tagged with one of the tags given.
     *
     * @param string $beanType type of bean you are looking for
     * @param array  $tagList  list of tags to match
     * @param string $sql      additional SQL query snippet
     * @param array  $bindings a list of values to bind to the query parameters
     *
     * @return array
     */
    public function tagged( $beanType, $tagList, $sql = '', $bindings = array() )
    {
        return R::tagged($beanType, $tagList, $sql, $bindings);
    }

    /**
     * Part of RedBeanPHP Tagging API.
     * Returns all beans that have been tagged with ALL of the tags given.
     *
     * @param string $beanType type of bean you are looking for
     * @param array  $tagList  list of tags to match
     * @param string $sql      additional SQL query snippet
     * @param array  $bindings a list of values to bind to the query parameters
     *
     * @return array
     */
    public function taggedAll( $beanType, $tagList, $sql = '', $bindings = array() )
    {
        return R::taggedAll($beanType,$tagList,$sql,$bindings);
    }

    /**
     * Wipes all beans of type $beanType.
     *
     * @param string $beanType type of bean you want to destroy entirely
     *
     * @return boolean
     */
    public function wipe( $beanType )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Counts the number of beans of type $type.
     * This method accepts a second argument to modify the count-query.
     * A third argument can be used to provide bindings for the SQL snippet.
     *
     * @param string $type     type of bean we are looking for
     * @param string $addSQL   additional SQL snippet
     * @param array  $bindings parameters to bind to SQL
     *
     * @return integer
     */
    public function count( $type, $addSQL = '', $bindings = array() )
    {
        return R::count($type, $addSQL, $bindings);
    }

    /**
     * Configures the facade, want to have a new Writer? A new Object Database or a new
     * Adapter and you want it on-the-fly? Use this method to hot-swap your facade with a new
     * toolbox.
     *
     * @param ToolBox $tb toolbox to configure facade with
     *
     * @return ToolBox
     */
    public function configureFacadeWithToolbox( ToolBox $tb )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Facade Convience method for adapter transaction system.
     * Begins a transaction.
     *
     * @return bool
     */
    public function begin()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Facade Convience method for adapter transaction system.
     * Commits a transaction.
     *
     * @return bool
     */
    public function commit()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Facade Convience method for adapter transaction system.
     * Rolls back a transaction.
     *
     * @return bool
     */
    public function rollback()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns a list of columns. Format of this array:
     * array( fieldname => type )
     * Note that this method only works in fluid mode because it might be
     * quite heavy on production servers!
     *
     * @param  string $table name of the table (not type) you want to get columns of
     *
     * @return array
     */
    public function getColumns( $table )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Generates question mark slots for an array of values.
     *
     * @param array  $array array to generate question mark slots for
     *
     * @return string
     */
    public function genSlots( $array, $template = NULL )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Flattens a multi dimensional bindings array for use with genSlots().
     *
     * @param array $array array to flatten
     *
     * @return array
     */
    public function flat( $array, $result = array() )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Nukes the entire database.
     * This will remove all schema structures from the database.
     * Only works in fluid mode. Be careful with this method.
     *
     * @warning dangerous method, will remove all tables, columns etc.
     *
     * @return void
     */
    public function nuke()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Short hand function to store a set of beans at once, IDs will be
     * returned as an array. For information please consult the R::store()
     * function.
     * A loop saver.
     *
     * @param array $beans list of beans to be stored
     *
     * @return array
     */
    public function storeAll( $beans )
    {
        return self::storeAll($beans);
    }

    /**
     * Short hand function to trash a set of beans at once.
     * For information please consult the R::trash() function.
     * A loop saver.
     *
     * @param array $beans list of beans to be trashed
     *
     * @return void
     */
    public function trashAll( $beans )
    {
        R::trashAll($beans);
    }

    /**
     * Toggles Writer Cache.
     * Turns the Writer Cache on or off. The Writer Cache is a simple
     * query based caching system that may improve performance without the need
     * for cache management. This caching system will cache non-modifying queries
     * that are marked with special SQL comments. As soon as a non-marked query
     * gets executed the cache will be flushed. Only non-modifying select queries
     * have been marked therefore this mechanism is a rather safe way of caching, requiring
     * no explicit flushes or reloads. Of course this does not apply if you intend to testPerfs
     * or simulate concurrent querying.
     *
     * @param boolean $yesNo TRUE to enable cache, FALSE to disable cache
     *
     * @return void
     */
    public function useWriterCache( $yesNo )
    {
        R::useWriterCache($yesNo);
    }

    /**
     * A label is a bean with only an id, type and name property.
     * This function will dispense beans for all entries in the array. The
     * values of the array will be assigned to the name property of each
     * individual bean.
     *
     * @param string $type   type of beans you would like to have
     * @param array  $labels list of labels, names for each bean
     *
     * @return array
     */
    public function dispenseLabels( $type, $labels )
    {
        return R::dispenseLabels( $type, $labels );
    }

    /**
     * Generates and returns an ENUM value. This is how RedBeanPHP handles ENUMs.
     * Either returns a (newly created) bean respresenting the desired ENUM
     * value or returns a list of all enums for the type.
     *
     * To obtain (and add if necessary) an ENUM value:
     *
     * <code>
     * $tea->flavour = R::enum( 'flavour:apple' );
     * </code>
     *
     * Returns a bean of type 'flavour' with  name = apple.
     * This will add a bean with property name (set to APPLE) to the database
     * if it does not exist yet.
     *
     * To obtain all flavours:
     *
     * <code>
     * R::enum('flavour');
     * </code>
     *
     * To get a list of all flavour names:
     *
     * <code>
     * R::gatherLabels( R::enum( 'flavour' ) );
     * </code>
     *
     * @param string $enum either type or type-value
     *
     * @return array|OODBBean
     */
    public function enum( $enum )
    {
        return  R::enum( $enum );
    }

    /**
     * Gathers labels from beans. This function loops through the beans,
     * collects the values of the name properties of each individual bean
     * and stores the names in a new array. The array then gets sorted using the
     * default sort function of PHP (sort).
     *
     * @param array $beans list of beans to loop
     *
     * @return array
     */
    public function gatherLabels( $beans )
    {
        return R::gatherLabels( $beans );
    }

    /**
     * Closes the database connection.
     *
     * @return void
     */
    public function close()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Simple convenience function, returns ISO date formatted representation
     * of $time.
     *
     * @param mixed $time UNIX timestamp
     *
     * @return string
     */
    public function isoDate( $time = NULL )
    {
        return R::isoDate($time);
    }

    /**
     * Simple convenience function, returns ISO date time
     * formatted representation
     * of $time.
     *
     * @param mixed $time UNIX timestamp
     *
     * @return string
     */
    public function isoDateTime( $time = NULL )
    {
       return R::isoDateTime( $time = NULL );
    }

    /**
     * Optional accessor for neat code.
     * Sets the database adapter you want to use.
     *
     * @param Adapter $adapter Database Adapter for facade to use
     *
     * @return void
     */
    public function setDatabaseAdapter( Adapter $adapter )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Optional accessor for neat code.
     * Sets the database adapter you want to use.
     *
     * @param QueryWriter $writer Query Writer instance for facade to use
     *
     * @return void
     */
    public function setWriter( QueryWriter $writer )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Optional accessor for neat code.
     * Sets the database adapter you want to use.
     *
     * @param OODB $redbean Object Database for facade to use
     */
    public function setRedBean( OODB $redbean )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Optional accessor for neat code.
     * Sets the database adapter you want to use.
     *
     * @return DBAdapter
     */
    public function getDatabaseAdapter()
    {
        return R::getDatabaseAdapter();
    }

    /**
     * In case you use PDO (which is recommended and the default but not mandatory, hence
     * the database adapter), you can use this method to obtain the PDO object directly.
     * This is a convenience method, it will do the same as:
     *
     * <code>
     * R::getDatabaseAdapter()->getDatabase()->getPDO();
     * </code>
     *
     * If the PDO object could not be found, for whatever reason, this method
     * will return NULL instead.
     *
     * @return NULL|PDO
     */
    public function getPDO()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns the current duplication manager instance.
     *
     * @return DuplicationManager
     */
    public function getDuplicationManager()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Optional accessor for neat code.
     * Sets the database adapter you want to use.
     *
     * @return QueryWriter
     */
    public function getWriter()
    {
        return R::getWriter();
    }

    /**
     * Optional accessor for neat code.
     * Sets the database adapter you want to use.
     *
     * @return OODB
     */
    public function getRedBean()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns the toolbox currently used by the facade.
     * To set the toolbox use R::setup() or R::configureFacadeWithToolbox().
     * To create a toolbox use Setup::kickstart(). Or create a manual
     * toolbox using the ToolBox class.
     *
     * @return ToolBox
     */
    public function getToolBox()
    {
        return R::getToolBox();
    }

    /**
     * Mostly for internal use, but might be handy
     * for some users.
     * This returns all the components of the currently
     * selected toolbox.
     *
     * Returns the components in the following order:
     *
     * # OODB instance (getRedBean())
     * # Database Adapter
     * # Query Writer
     * # Toolbox itself
     *
     * @return array
     */
    public function getExtractedToolbox()
    {
        return R::getExtractedToolbox();
    }

    /**
     * Facade method for AQueryWriter::renameAssociation()
     *
     * @param string|array $from
     * @param string       $to
     *
     * @return void
     */
    public function renameAssociation( $from, $to = NULL )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Little helper method for Resty Bean Can server and others.
     * Takes an array of beans and exports each bean.
     * Unlike exportAll this method does not recurse into own lists
     * and shared lists, the beans are exported as-is, only loaded lists
     * are exported.
     *
     * @param array $beans beans
     *
     * @return array
     */
    public function beansToArray( $beans )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Sets the error mode for FUSE.
     * What to do if a FUSE model method does not exist?
     * You can set the following options:
     *
     * * OODBBean::C_ERR_IGNORE (default), ignores the call, returns NULL
     * * OODBBean::C_ERR_LOG, logs the incident using error_log
     * * OODBBean::C_ERR_NOTICE, triggers a E_USER_NOTICE
     * * OODBBean::C_ERR_WARN, triggers a E_USER_WARNING
     * * OODBBean::C_ERR_EXCEPTION, throws an exception
     * * OODBBean::C_ERR_FUNC, allows you to specify a custom handler (function)
     * * OODBBean::C_ERR_FATAL, triggers a E_USER_ERROR
     *
     * <code>
     * Custom handler method signature: handler( array (
     * 	'message' => string
     * 	'bean' => OODBBean
     * 	'method' => string
     * ) )
     * </code>
     *
     * This method returns the old mode and handler as an array.
     *
     * @param integer       $mode mode, determines how to handle errors
     * @param callable|NULL $func custom handler (if applicable)
     *
     * @return array
     */
    public function setErrorHandlingFUSE( $mode, $func = NULL )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Simple but effective debug function.
     * Given a one or more beans this method will
     * return an array containing first part of the string
     * representation of each item in the array.
     *
     * @param OODBBean|array $data either a bean or an array of beans
     *
     * @return array
     */
    public function dump( $data )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Binds an SQL function to a column.
     * This method can be used to setup a decode/encode scheme or
     * perform UUID insertion. This method is especially useful for handling
     * MySQL spatial columns, because they need to be processed first using
     * the asText/GeomFromText functions.
     *
     * Example:
     *
     * <code>
     * R::bindFunc( 'read', 'location.point', 'asText' );
     * R::bindFunc( 'write', 'location.point', 'GeomFromText' );
     * </code>
     *
     * Passing NULL as the function will reset (clear) the function
     * for this column/mode.
     *
     * @param string $mode     mode for function: i.e. read or write
     * @param string $field    field (table.column) to bind function to
     * @param string $function SQL function to bind to specified column
     *
     * @return void
     */
    public function bindFunc( $mode, $field, $function )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Sets global aliases.
     * Registers a batch of aliases in one go. This works the same as
     * fetchAs and setAutoResolve but explicitly. For instance if you register
     * the alias 'cover' for 'page' a property containing a reference to a
     * page bean called 'cover' will correctly return the page bean and not
     * a (non-existant) cover bean.
     *
     * <code>
     * R::aliases( array( 'cover' => 'page' ) );
     * $book = R::dispense( 'book' );
     * $page = R::dispense( 'page' );
     * $book->cover = $page;
     * R::store( $book );
     * $book = $book->fresh();
     * $cover = $book->cover;
     * echo $cover->getMeta( 'type' ); //page
     * </code>
     *
     * The format of the aliases registration array is:
     *
     * {alias} => {actual type}
     *
     * In the example above we use:
     *
     * cover => page
     *
     * From that point on, every bean reference to a cover
     * will return a 'page' bean. Note that with autoResolve this
     * feature along with fetchAs() is no longer very important, although
     * relying on explicit aliases can be a bit faster.
     *
     * @param array $list list of global aliases to use
     *
     * @return void
     */
    public function aliases( $list )
    {
       R::aliases( $list );
    }

    /**
     * Tries to find a bean matching a certain type and
     * criteria set. If no beans are found a new bean
     * will be created, the criteria will be imported into this
     * bean and the bean will be stored and returned.
     * If multiple beans match the criteria only the first one
     * will be returned.
     *
     * @param string $type type of bean to search for
     * @param array  $like criteria set describing the bean to search for
     *
     * @return OODBBean
     */
    public function findOrCreate( $type, $like = array() )
    {
        return R::findOrCreate($type,$like);
    }
    /**
     * Toggles JSON column features.
     * Invoking this method with boolean TRUE causes 2 JSON features to be enabled.
     * Beans will automatically JSONify any array that's not in a list property and
     * the Query Writer (if capable) will attempt to create a JSON column for strings that
     * appear to contain JSON.
     *
     * Feature #1:
     * AQueryWriter::useJSONColumns
     *
     * Toggles support for automatic generation of JSON columns.
     * Using JSON columns means that strings containing JSON will
     * cause the column to be created (not modified) as a JSON column.
     * However it might also trigger exceptions if this means the DB attempts to
     * convert a non-json column to a JSON column.
     *
     * Feature #2:
     * OODBBean::convertArraysToJSON
     *
     * Toggles array to JSON conversion. If set to TRUE any array
     * set to a bean property that's not a list will be turned into
     * a JSON string. Used together with AQueryWriter::useJSONColumns this
     * extends the data type support for JSON columns.
     *
     * So invoking this method is the same as:
     *
     * AQueryWriter::useJSONColumns( $flag );
     * OODBBean::convertArraysToJSON( $flag );
     *
     * Unlike the methods above, that return the previous state, this
     * method does not return anything (void).
     *
     * @param boolean $flag feature flag (either TRUE or FALSE)
     *
     * @return void
     */
    public function useJSONFeatures($flag)
    {
       R::useJSONFeatures($flag);
    }

    /**
     * Tries to find beans matching the specified type and
     * criteria set.
     *
     * If the optional additional SQL snippet is a condition, it will
     * be glued to the rest of the query using the AND operator.
     *
     * @param string $type type of bean to search for
     * @param array  $like optional criteria set describing the bean to search for
     * @param string $sql  optional additional SQL for sorting
     *
     * @return array
     */
    public function findLike($type, $like = [], $sql = '' )
    {
        return R::findLike( $type, $like, $sql );
    }

    /**
     * Starts logging queries.
     * Use this method to start logging SQL queries being
     * executed by the adapter.
     *
     * @note you cannot use R::debug and R::startLogging
     * at the same time because R::debug is essentially a
     * special kind of logging.
     *
     * @return void
     */
    public function startLogging()
    {
        R::startLogging();
    }

    /**
     * Stops logging, comfortable method to stop logging of queries.
     *
     * @return void
     */
    public function stopLogging()
    {
        R::stopLogging();
    }

    /**
     * Returns the log entries written after the startLogging.
     *
     * @return array
     */
    public function getLogs()
    {
        return R::getLogs();
    }

    /**
     * Resets the Query counter.
     *
     * @return integer
     */
    public function resetQueryCount()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns the number of SQL queries processed.
     *
     * @return integer
     */
    public function getQueryCount()
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns the current logger instance being used by the
     * database object.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return R::getLogger();
    }

    /**
     * Alias for setAutoResolve() method on OODBBean.
     * Enables or disables auto-resolving fetch types.
     * Auto-resolving aliased parent beans is convenient but can
     * be slower and can create infinite recursion if you
     * used aliases to break cyclic relations in your domain.
     *
     * @param boolean $automatic TRUE to enable automatic resolving aliased parents
     *
     * @return void
     */
    public function setAutoResolve( $automatic = TRUE )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Toggles 'partial bean mode'. If this mode has been
     * selected the repository will only update the fields of a bean that
     * have been changed rather than the entire bean.
     * Pass the value TRUE to select 'partial mode' for all beans.
     * Pass the value FALSE to disable 'partial mode'.
     * Pass an array of bean types if you wish to use partial mode only
     * for some types.
     * This method will return the previous value.
     *
     * @param boolean|array $list List of type names or 'all'
     *
     * @return mixed
     */
    public function usePartialBeans( $yesNoBeans )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Exposes the result of the specified SQL query as a CSV file.
     *
     * @param string  $sql      SQL query to expose result of
     * @param array   $bindings parameter bindings
     * @param array   $columns  column headers for CSV file
     * @param string  $path     path to save CSV file to
     * @param boolean $output   TRUE to output CSV directly using readfile
     *
     * @return void
     */
    public function csv( $sql = '', $bindings = array(), $columns = NULL, $path = '/tmp/redexport_%s.csv', $output = true )
    {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Productivity method to quickly find-and-update a bean.
     * @see RedBeanPHP\Util\MatchUp
     *
     * @param string   $type         type of bean you're looking for
     * @param string   $sql          SQL snippet (starting at the WHERE clause, omit WHERE-keyword)
     * @param array    $bindings     array of parameter bindings for SQL snippet
     * @param array    $onFoundDo    task list to be considered on finding the bean
     * @param array    $onNotFoundDo task list to be considered on NOT finding the bean
     * @param OODBBean &$bean        reference to obtain the found bean
     *
     * @return mixed
     */
    public function matchUp( $type, $sql, $bindings = array(), $onFoundDo = NULL, $onNotFoundDo = NULL, &$bean = NULL 	) {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Returns an instance of the Look Helper class.
     * The instance will be configured with the current toolbox.
     *
     * @return Look
     */
    public function getLook() {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Calculates a diff.
     * @see Diff::diff
     *
     * @return array
     */
    public function diff( $bean, $other, $filters = array( 'created', 'modified' ), $pattern = '%s.%s.%s' ) {
        throw new PovException("Redbean facade pas implémenté");
    }

    /**
     * Dynamically extends the facade with a plugin.
     * Using this method you can register your plugin with the facade and then
     * use the plugin by invoking the name specified plugin name as a method on
     * the facade.
     *
     * Usage:
     *
     * <code>
     * R::ext( 'makeTea', function() { ... }  );
     * </code>
     *
     * Now you can use your makeTea plugin like this:
     *
     * <code>
     * R::makeTea();
     * </code>
     *
     * @param string   $pluginName name of the method to call the plugin
     * @param callable $callable   a PHP callable
     *
     * @return void
     */
    public function ext( $pluginName, $callable )
    {
        throw new PovException("Redbean facade pas implémenté");
    }




}

