<?php

namespace Classiq\Utils;


use Classiq\C_classiq;
use Classiq\Models\Page;
use Classiq\Models\Session;
use Classiq\Models\Urlpage;
use Classiq\Wysiwyg\Wysiwyg;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Pov\MVC\View;
use Pov\PovException;
use Pov\System\AbstractSingleton;
use Classiq\Models\Classiqmodel;

/**
 * Class ClassiqUtils
 * @package Classiq\Utils
 *
 * @property NotifyManager $notify
 */
class ClassiqUtils extends AbstractSingleton
{
    /**
     * @var bool Quand défini sur false n'enregistrera pas de notification lors des updates de la DB
     */
    public $configPreventDbNotifications=false;

    /**
     * Désactive les notifications
     */
    public function configNotificationsOff(){
        $this->_configNotificationsReset=$this->configPreventDbNotifications;
        $this->configPreventDbNotifications=true;
    }
    /**
     * Remet les notifications comme elle étaient avant l'appel à configNotificationsOff()
     */
    public function configNotificationsReset(){
        $this->configPreventDbNotifications=$this->_configNotificationsReset;
    }
    private $_configNotificationsReset=false;

    /**
     * Renvoie un tag svg avec la classe css .wysiwyg-icon
     * @deprecated Mieux vaut utiliser pov()->svg->use() directement
     * @param string $svgId identifiant d'une ressources svg cq-etc... (ene pas mettre le prefixe "cq-"
     * @return \Pov\Html\Trace\HtmlTag
     */
    public function icoWysiwyg($svgId){
        return pov()->svg->use("cq-$svgId")->addClass("wysiwyg-icon");
    }

    /**
     * Pour savoir si le wysiwyg est activé.
     * @param bool $useCache si défini sur false refera une requete mysql pour s'assurer de la fraicheur des infos de session.
     * @return bool true si le wysiwyg est activé
     */
    public function wysiwyg($useCache=true)
    {
        if($useCache){
            return Wysiwyg::$enabled;
        }else{
            $wasEnabled=Wysiwyg::$enabled;
            $s=Session::getHumanSession(false);
            if($s->user && $s->user->isAdmin()){
                the()->human->isAdmin=Wysiwyg::$enabled=true;
            }else{
                the()->human->isAdmin=Wysiwyg::$enabled=false;
            }

            if($wasEnabled!=Wysiwyg::$enabled){
                if(Wysiwyg::$enabled){
                    cq()->notify->human->login();
                }else{
                    cq()->notify->human->logout();
                }
            }
            return Wysiwyg::$enabled;
        }

    }
    /**
     * @return bool true si l'utilsateur courrant est un admin
     */
    public function isAdmin()
    {
        return the()->human->isAdmin;
    }

    /**
     * @var bool cache pour isModeDev()
     */
    private $_isModeDev=false;

    /**
     * nous dit si on est en mode developpement ou production
     * c'est le fichier myproject-dir/mode.txt qui définit ça. Ce fichier est mis à jour en fonction de la compilation grunt choisie.
     */
    public function isModeDev(){
        //die(the()->fileSystem->projectPath."/mode.txt");
        $this->_isModeDev=@file_get_contents(the()->fileSystem->projectPath."/mode.txt")==="dev";
        return $this->_isModeDev;
    }

    /**
     * Renvoie tout ce qui est utile pour faire marcher Classiq:
     * A utiliser une seule fois dans le layout (non ajax)
     * intègre :
     * - pov-boot.js (donc jquery)
     * - si connecté en wysiwyg intègre les js et css du wysiwyg
     * - fait passer les variables json (accessibles en js via window.pov.history.currentPageInfo ou window.LayoutVars)
     *
     * @return String Tout le code html, svg etc nécessaire
     */
    public function _layoutStuff()
    {
        $r="";
        if(the()->request("editUid")){
            the()->htmlLayout()->layoutVars->editUid=the()->request("editUid");
            if(!the()->human->isAdmin){
                the()->htmlLayout()->redirectJS=C_classiq::login_url()->absolute();
            }
        }
        the()->htmlLayout()->layoutVars->isModeDev=$this->isModeDev();
        //pov-fmk + jquery
        the()->htmlLayout()->addJsToFooter("vendor/davidmars/pov-2018/dist/pov-boot.js",true);
        if($this->wysiwyg()){
            the()->htmlLayout()->addJsToFooter(\Classiq\Classiq::assetsDir()."/wysiwyg.js");
            the()->htmlLayout()->addCssToHeader(\Classiq\Classiq::assetsDir()."/wysiwyg.css");
            $r=View::get("unique-instances/layout-stuff")->render();
        }
        return $r;
    }

    public function __get($name)
    {
        switch ($name){
            case "notify":
                return NotifyManager::inst();
                break;
            default:
                throw new PovException("$name pas pris en charge");
        }
    }

    /**
     * @return Page|null
     * @throws PovException
     */
    public function homePage()
    {
        return Urlpage::homePage()->getPage();
    }

    /**
     * @param string $emailTo Email à qui envoyer le mail
     * @param string $subject Objet du mail
     * @param string $htmlMessage Message formaté en html
     * @return bool true si ça a marché
     * @throws Exception Si une erreur de mail s'est produite
     * @throws PovException Si le mail par défaut n'est pas configuré
     */
    public function sendMail($emailTo,$subject,$htmlMessage){
        if(!$this->defaultMailSender){
            throw new PovException("Configuration manquante; Il faut configurer le mail système <code>(cq()->defaultMailSender=etc...)</code>");
        }else{
            $mail = new PHPMailer(true);                    // Passing `true` enables exceptions
            try {
                $m=$this->defaultMailSender;
                //Server settings
                $mail->SMTPDebug = 0;                                 // pas d'output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = $m->host;  // Specify main and backup SMTP servers
                $mail->SMTPAuth = $m->SMTPAuth;                               // Enable SMTP authentication
                $mail->Username = $m->username;                 // SMTP username
                $mail->Password = $m->password;                           // SMTP password
                $mail->SMTPSecure = $m->SMTPSecure;                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = $m->port;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom($m->username, $m->username);
                $mail->addAddress($emailTo, $emailTo);     // Add a recipient
                //$mail->addReplyTo($emailFrom, $nom); //le reply to met le message dans les spams

                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->CharSet = "UTF-8";
                $mail->Subject = $subject;
                $mail->Body    = $htmlMessage;
                $mail->AltBody = strip_tags($htmlMessage);
                $mail->send();
                return true;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * @var MailConfig Le compte mail par défaut à utiliser pour envoyer des email (à configurer au boot)
     */
    public $defaultMailSender;


    /**
     * Retourne un enregistrement dans lequel on stoque des variables globales telles que les lnagues activées par exemple
     * @return Classiqmodel
     */
    public function configStorage(){
        /** @var Classiqmodel $conf */
        $conf=Classiqmodel::getByName("config storage",true);
        return $conf;
    }

    /**
     * Retourne la liste des langues marquées comme actives depuis la backfice de config.
     * Si aucune langue n'est signalée active on retournera la liste des langues par défaut
     * @param boolean $allIfAdmin si défini sur true et que l'utilisateur est admin renverra la liste complète des langues
     * @return string[] Les langCodes
     * @throws PovException
     */
    public function langActives($allIfAdmin=false){
        $langs=$this->configStorage()->getValueAsStringArray("vars.langActives");
        $default=the()->project->languages;
        if(!$langs || ( cq()->isAdmin() && $allIfAdmin ) ){
            return $default;
        }
        return $langs;
    }

}