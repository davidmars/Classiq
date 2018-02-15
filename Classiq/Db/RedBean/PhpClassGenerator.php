<?php


namespace Classiq\Db\RedBean;

use Pov\MVC\View;
use Pov\PovException;
use RedBeanPHP\SimpleModel;

/**
 * Class PhpClassGenerator est un module qui permet de générer des classes abstraites php à partir le base de donnée
 * @package Pov\Db\RedBean
 */
class PhpClassGenerator{

    /**
     * @var string répertoire où enregistrer les classes php
     */
    public static $outputDir="";
    /**
     * @var string Namespace à appliquer
     */
    public static $namespace="";

    /** @var PhpClassGenerator[] */
    public static $all=[];

    /**
     * @return PovRedBeanFacade
     */
    private static function db(){
        return PovRedBeanFacade::inst();
    }


    /**
     * @return PhpClassGenerator[]
     */
    public static function genAll(){
        $tables=self::db()->inspect();

        //on génére les classes en facile
        foreach ($tables as $t){
            self::$all[$t]=new PhpClassGenerator($t);
        }

        //on traite les associations
        foreach (self::$all as $c){
            $c->generateFieldsProps();
            $c->generateRelations();
        }
        if(self::$outputDir){
            foreach (self::$all as $c){
                $content=$c->getPhpCode();
                $file=$c->getFilePath();
                file_put_contents($file,$content);
            }
        }

        return self::$all;
    }



    //---------------------chaque table--------------------------

    /**
     * Chemin vers le fichier
     * @return string|null
     */
    public function getFilePath()
    {
        if(self::$outputDir){
            return $file=trim(self::$outputDir,'/\\')."/".$this->className.".php";
        }
        return null;
    }

    /**
     * @var string Nom du modèle
     */
    public $modelBoxClassName;

    /**
     * @var string
     */
    public $tableName;
    /**
     * @var string
     */
    public $className;

    public $oneToMany=[];
    public $manyToOne=[];
    /** @var PhpField[]  */
    public $props=[];
    /**
     * @var PhpField[] les champs de relations simple oneToMany et manyToOne
     */
    public $propsRelations=[];

    /**
     * Génére dans $props les champs simples
     */
    private function generateFieldsProps(){

        switch (self::db()->dbType()){

            case PovRedBeanFacade::DB_TYPE_MySQL:

                $fullFields=self::db()->getAll( "SHOW FULL COLUMNS FROM ".$this->tableName );
                foreach ($fullFields as $f){
                    $name=$f["Field"];
                    $this->props[$name]=new PhpField();
                    $this->props[$name]->name=$name;
                    $this->props[$name]->type=$this->mysqlTypeToPhpType($f["Type"]);;
                    $this->props[$name]->comment=$f["Comment"];
                }
                break;

            case PovRedBeanFacade::DB_TYPE_SQLiteT:

                $fullFields=self::db()->getAll( "PRAGMA table_info($this->tableName); " );
                foreach ($fullFields as $f){
                    $name=$f["name"];
                    $this->props[$name]=new PhpField();
                    $this->props[$name]->name=$name;
                    $this->props[$name]->type=$this->liteSqlTypeToPhpType($f["type"]);
                    //$this->props[$name]->comment=$f["Comment"];
                }
                break;

            default:
                throw new PovException(self::db()->dbType()." non prise en charge");
        }



        //sql lite


    }


    /**
     * Convertit un type de champ mysql (varchar int, datetime enum etc) en son équivalent php
     * @param string $mysqlType
     * @return string
     */
    private function mysqlTypeToPhpType($mysqlType){
        switch (true){
            case preg_match("/int\(/",$mysqlType):
                return "Int";
            case preg_match("/varchar\(/",$mysqlType):
                return "String";
            case preg_match("/datetime/",$mysqlType):
                return "\DateTime";

            default:
                return $mysqlType;
        }
    }
    /**
     * Convertit un type de champ mysql (varchar int, datetime enum etc) en son équivalent php
     * @param string $mysqlType
     * @return string
     */
    private function liteSqlTypeToPhpType($mysqlType){
        switch (true){
            case preg_match("/INTEGER/",$mysqlType):
                return "Int";
            case preg_match("/TEXT/",$mysqlType):
                return "String";
            case preg_match("/datetime/",$mysqlType):
                return "\DateTime";

            default:
                return $mysqlType;
        }
    }

    public function generateRelations(){
        $indexes=[];
        $r=[];
        switch (self::db()->dbType()){
            case PovRedBeanFacade::DB_TYPE_MySQL:
                $indexes= self::db()->getAll( "SHOW INDEX FROM ".$this->tableName );
                foreach ($indexes as $idx){
                    if(preg_match("/foreignkey/",$idx["Key_name"])){
                        $r[]=$idx;
                        $fieldSansId=preg_replace("/_id$/","",$idx["Column_name"]);
                        $relativeTable=explode("_",$idx["Key_name"]);
                        $relativeTable=array_pop($relativeTable);

                        //$this->oneToMany[$fieldSansId]=$relativeTable;

                        if($fieldSansId==$relativeTable){
                            $f=new PhpField();
                            $f->name=$fieldSansId;
                            $f->type="R_".$relativeTable;
                            $f->comment="Le $f->type relatif";
                            $this->propsRelations[$f->name]=$f;

                            $own=new PhpField();
                            $own->name="own".ucfirst($this->tableName)."List";
                            $own->type=$this->className."[]";
                            $own->comment="Les ".$this->className." relatifs";
                            self::$all[$relativeTable]->propsRelations[$own->name]=$own;
                        }
                    }
                }
                break;

            case PovRedBeanFacade::DB_TYPE_SQLiteT:
                $indexes= self::db()->getAll( "select * FROM sqlite_master WHERE type='index' and tbl_name='$this->tableName'" );
                pov()->log->info("indexes $this->tableName",$indexes);
                foreach ($indexes as $idx){
                    if(preg_match("/foreignkey/",$idx["name"])){

                        $r[]=$idx;
                        $fieldSansId=preg_replace("/.*\(([a-z0-9]+)_id\) *$/","$1",$idx["sql"]);
                        $relativeTable=explode("_",$idx["name"]);
                        $relativeTable=array_pop($relativeTable);
                        pov()->log->info("ok",[$fieldSansId,$relativeTable]);
                        //$this->oneToMany[$fieldSansId]=$relativeTable;

                        if($fieldSansId==$relativeTable && array_key_exists($relativeTable,self::$all)){
                            $f=new PhpField();
                            $f->name=$fieldSansId;
                            $f->type="R_".$relativeTable;
                            $f->comment="Le $f->type relatif";
                            $this->propsRelations[$f->name]=$f;

                            $own=new PhpField();
                            $own->name="own".ucfirst($this->tableName)."List";
                            $own->type=$this->className."[]";
                            $own->comment="Les ".$this->className." relatifs";
                            self::$all[$relativeTable]->propsRelations[$own->name]=$own;
                        }

                    }
                }
                break;

            default:
                throw new PovException(self::db()->dbType()." non prise en charge");
        }






        return $r;
    }


    public function __construct($tableName){
        $this->tableName=$tableName;
        $this->className="R_".$tableName;
        $bean=self::db()->dispense($tableName);
        $box=$bean->box();
        $this->modelBoxClassName=get_class($box);
        if($this->modelBoxClassName==__CLASS__){
            $this->modelBoxClassName=SimpleModel::class;
        }
    }

    /**
     * @return String le code html pour afficher le code php de la classe en question
     */
    public function getPhpCode(){
        $v=new \Pov\MVC\View("redbean-php-class-generator/phpclass",$this);
        return $v->render();
    }


}




View::$possiblesPath[]= __DIR__ . "/v";
