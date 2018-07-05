<?php
namespace Classiq\Models\JsonModels;
use Classiq\Models\Classiqmodel;
use Classiq\Wysiwyg\Wysiwyg;
use Classiq\Wysiwyg\WysiwygListitem;
use Pov\MVC\View;

/**
 * Class ListItem
 * @package Classiq\Models\JsonModel
 */
class ListItem
{
    /**
     * Permet de remplacer un vieux template par un nouveau
     * @var string[] Les clés sot les vieux path, les valeurs sont les nouveaux path
     */
    public static $debugPath=[];
    /**
     * @var array Le tableau associaciatif qui contient les données
     */
    public $data;
    /**
     * @var Classiqmodel
     */
    public $record;
    /**
     * @var String champ qui designe la liste dans le record
     */
    public $fieldName;

    /**
     * ListItem constructor.
     * @param array $data Tableau de données pour construire ce champ
     * @param string $key
     * @param string $fieldName champ qui designe la liste dans le record
     * @param Classiqmodel $record Le record d'où provient cet item
     */
    public function __construct($data,$key,$fieldName,$record=null){
        $this->data=$data;
        $this->key=$key;
        $this->fieldName=$fieldName;
        $this->record=$record;
    }

    /**
     * @return WysiwygListitem
     */
    public function wysiwyg()
    {
        return new WysiwygListitem($this);
    }

    /**
     * @return string
     */
    public function path()
    {
        if(!isset($this->data["path"])){
            $this->data["path"]="defaults/list-item-default";
        }
        $path=$this->data["path"];
        if(!View::isValid($path)){
            if(isset(self::$debugPath[$path])){
                $path=self::$debugPath[$path];
            }
        }
        return $path;
    }

    /**
     * @return View
     */
    public function view()
    {

        return View::get($this->path(),$this);
    }

    /**
     * Identifiant unique du modèle
     * @return string typedemodele-id.champ.key
     */
    public function uid(){
        //return $this->modelType()."-".$this->id;
        if($this->record){
            return $this->record->uid().".".$this->fieldName.".".$this->key;
        }
        return "";

    }

    /**
     * @param string $varName
     * @param string $ifNull Valeur à renvoyer si non défini ou null
     * @return mixed|string
     */
    public function getData($varName,$ifNull="")
    {
        $varName=preg_replace("/_lang$/","_".the()->project->langCode,$varName);
        if(!empty($this->data[$varName])){
            return $this->data[$varName];
        }else{
            return $ifNull;
        }
    }
    /**
     * Renvoie la listes des records trouvés dans la variable
     * @param string $varName
     * @return Classiqmodel[]
     */
    public function getDataAsRecords($varName)
    {
        return Classiqmodel::getByUids($this->getData($varName));

    }
    /**
     * Renvoie le record relatif à l'uid qu'on trouvera dans cette variable
     * Si la variable contien plusieurs uids, renverra le premier
     * @param string $varName Nom de la variable.
     * @return Classiqmodel
     */
    public function getDataAsRecord($varName)
    {
        return Classiqmodel::getByUid($this->getData($varName));
    }

    /**
     * Renvoie une liste de ListItem à partir du tableau
     * @param string $varName nom de la variable dans le record principal
     * @param Classiqmodel $mainRecord
     * @return ListItem[]
     */
    public static function getList($varName,$mainRecord){
        $r=[];
        $items=$mainRecord->getValue($varName);
        if($items){
            foreach ($items as $key=>$data){
                if(is_array($data)){
                    $item=new ListItem($data,$key,$varName,$mainRecord);
                    $r[$key]=$item;
                }
            }
        }

        return $r;
    }

    /**
     * Pour obtenir le record rattaché à la variable targetUid
     * @param bool $asRecord si true alors retournera le record et non son uid
     * @return Classiqmodel|null|string null si le record n'existe pas, sinon l'uid ou le record lui même.
     */
    public function targetUid($asRecord=false)
    {
        $uid=$this->getData("targetUid");
        $record=Classiqmodel::getByUid($uid);
        if($record){
            if($asRecord){
                return $record;
            }else{
                return $record->uid();
            }
        }
        return null;

    }

    /**
     * Renvoie la valeur de la variable données sous forme de list items.
     * @param string $varName Le nom de la variable
     * @return ListItem[]
     */
    public function getDataAsListItems($varName)
    {
        /** @var ListItem[] $items */
        $items= ListItem::getList($this->fieldName.".".$this->key.".".$varName,$this->record);
        return $items;
    }

}