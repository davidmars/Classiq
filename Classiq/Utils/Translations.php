<?php
namespace Classiq\Utils;

use Pov\PovException;

/**
 * Permet de gérer un ensemble de traductions à partir d'un fichier CSV
 * @package Classiq\Utils
 */
class Translations
{
    /**
     * @var string Url du csv de traductions
     */
    private $config_translations_csv_url;
    /**
     * @var array Tableau de cache où se trouvent les traductions
     */
    private $_translations=[];
    /**
     * @var string La langue à utiliser par défaut
     */
    private $_defaultLangCode="";

    /**
     * Translations constructor.
     * @param string Url du csv de traductions
     * @param string $defaultLangCode La langue à utiliser par défaut
     */
    public function __construct($config_translations_csv_url,$defaultLangCode="fr"){
        $this->config_translations_csv_url=$config_translations_csv_url;
        $this->_defaultLangCode=$defaultLangCode;
    }


    /**
     * Renvoie un terme traduit
     * @see https://docs.google.com/spreadsheets/d/1m_vi4YTj2vAMwaJxvGWRIeJP4F9IOhE_FMftjruiDz0/edit#gid=0
     * @see $config_translations_csv_url
     * @param string $termsIdentifier
     * @param bool $nl2br si true remplacera les sauts de ligne par des <BR>
     * @param string $langCode Langue à utiliser pour traduire le terme
     * @return string
     */
    public function term($termsIdentifier="",$nl2br=false,$langCode=null){
        if(!$langCode){
            $langCode=$this->_defaultLangCode;
        }
        if(!$termsIdentifier){
            return "nothing to translate!!!";
        }
        $trads=$this->translations();
        if(isset($trads->{$termsIdentifier}->{$langCode})){
            $text= $trads->{$termsIdentifier}->{$langCode};
            if($nl2br){
                return nl2br($text);
            }
            return $text;

        }else{
            return $termsIdentifier;
        }

    }


    /**
     * Revoie (et télécharge au besoin) les traductions
     * @return array|mixed
     * @throws PovException
     */
    private function translations(){
        if(!$this->_translations){
            $jsonCache=the()->fileSystem->cachePath."/translations-".md5($this->config_translations_csv_url).".json";
            $csvCache=the()->fileSystem->cachePath."/translations-".md5($this->config_translations_csv_url).".csv";
            $delimiter=",";
            $utf8encode=false;
            $spreadsheetData=[];
            $forceDWD=the()->project->config_translations_debug;
            if(!file_exists($jsonCache) || $forceDWD){

                @$ok=copy($this->config_translations_csv_url,$csvCache);
                if(!$ok){
                    $m="Problème pour copier le csv de traductions $this->config_translations_csv_url. ";
                    if($forceDWD){
                        $m.="<br>Essayez <code>the()->project->config_translations_debug=false</code>.";
                    }
                    $m.="<br>Le fichier n'est peut être pas accessible ou vous n'y avez pas accès";
                    throw new PovException($m);
                }
                if (($handle = fopen($csvCache, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, null, $delimiter)) !== FALSE) {
                        if($utf8encode){
                            $data = array_map("utf8_encode", $data); //added
                        }
                        $spreadsheetData[]=$data;
                    }
                }
                /** @var array $langues La liste des langues*/
                $langues=array_shift($spreadsheetData);
                //convertit le csv en json
                $obj=[];
                foreach ($spreadsheetData as $line){
                    $obj[$line[0]]=[];
                    for($c=1;$c<count($langues);$c++){
                        $value="";
                        if(isset($line[$c])){
                            $value=trim($line[$c]);
                        }
                        $obj[$line[0]][$langues[$c]]=$value;
                    }
                }
                file_put_contents($jsonCache,json_encode($obj,JSON_PRETTY_PRINT));
            }
            $this->_translations=json_decode(file_get_contents($jsonCache));
        }
        return $this->_translations;
    }
}