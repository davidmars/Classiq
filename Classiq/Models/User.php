<?php

namespace Classiq\Models;

use Classiq\C_classiq;
use Pov\PovException;

/**
 * Représente un utilisateur
 * @package Models
 *
 * @method User box()
 * @method User[] boxAll($users) static
 *
 * @property String $password Le mot de passe en crypté
 * @property String $email
 * @property String $role
 * @property Session[] $ownSession La liste des session de cet utilisateur
 * @property String $token token pour récupérer son mot de passe
 * @property String $tokenexpires date d'expiration du  token
 */
class User extends Classiqmodel
{

    const ROLE_ADMIN = "admin";
    const ROLE_SIMPLE_HUMAN = "simple human";

    const EVENT_USER_CHANGE = "SSE_USER_CHANGE";

    static $icon = "cq-user-user";

    /**
     * @param bool $plural Si true retournera le nom du modèle au pluriel
     * @return string nom du type de record lisible par les humains
     */
    static function humanType($plural=false){
        if($plural){
            return "Comptes utilisateur";
        }
        return "Compte utilisateur";
    }



    /**
     * Envoie à l'utilisateur un email qui lui permettra de redéfinir son mot de passe via un token
     */
    public function sendRetrievePasswordEmail()
    {
        $token=pov()->utils->string->random();
        $tokenexpires=new \DateTime();
        $tokenexpires->add(new \DateInterval('PT1H'));
        $tokenexpires=$tokenexpires->format("Y-m-d H:i:s");
        $this->token=$token;
        $this->tokenexpires=$tokenexpires;
        db()->store($this);

        $message="Afin de réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous (valable une heure):<br>";
        $message.=C_classiq::login_url("edit-user")->absolute()."?token=".$this->token;

        cq()->sendMail($this->email,"Mot de passe oublié",$message);

    }

    /**
     * Retourne un utilisateru à partir de son token
     * il faut pour que ça marche que le champ tokenexpires ne soit pas périmé
     * @param string $token
     * @param bool $connectUserIfValid Si défini sur true et que le token fonctionne connectera l'utilisateur
     * @return User|null
     */
    public static function getUserByToken($token,$connectUserIfValid=false){
        /** @var User $u */
        $u=db()->findOne("user","token='$token' AND tokenexpires > '".pov()->now()."'");
        if($u && $connectUserIfValid){
            self::setConnected($u->box());
        }
        return $u;
    }

    /**
     * Renvoie un User depuis son email (ou null)
     * @param string $email
     * @return User
     */
    public static function getUserByEmail($email)
    {
        /** @var User $u */
        $u=db()->findOne("user","email='$email'");
        return $u;
    }

    /**
     * Renvoie les règles de validation
     * @return array ouù chaque entrée ressemble à "champ"=>"regle"
     * @see https://github.com/Wixel/GUMP#available-validators
     */
    protected function _validators(){

        $v=parent::_validators();
        if($this->id){ //les champs ne sont obligatoires qu'à la modification
            $v["email"]="required|valid_email";
            $v["password"]="required|max_len,100|min_len,6";
            $v["role"]="required";
        }
        return $v;
    }


    /**
     * à l'enregistrement
     */
    public function update()
    {
        parent::update();

        $sameName=db()->findOne("user", "name='$this->name' and id != '$this->id'");
        if($sameName){
            throw new PovException("Un utilisateur avec le même nom existe déjà");
        }

        if($this->id && $this->email){
            $existing = db()->find("user", "email='$this->email' and id != '$this->id'");
            if ($existing) {
                throw new PovException("Un compte avec l'email $this->email existe déjà");
            }
        }

        //premier utilisateur qu'on crée?
        $count = db()->count("user");
        if (!$count) {
            $this->role = self::ROLE_ADMIN;
        }
        if ($this->cleanPassword) {
            $this->password = md5($this->cleanPassword);
        }

    }

    /**
     * Notifie EVENT_USER_CHANGE
     */
    public function after_update(){
        cq()->notify->admins->notify(self::EVENT_USER_CHANGE, "Le compte utilisateur <b>" . $this->name . "</b> a été enregistré");
        parent::after_update();
    }


    /**
     * Notifie EVENT_USER_CHANGE
     */
    public function after_delete()
    {
        cq()->notify->admins->notify(self::EVENT_USER_CHANGE, "utilisateur supprimé");
        parent::after_delete();
    }

    /**
     * @var string le mot de passe en clair (set only)
     */
    public $cleanPassword;

    /**
     * @return bool true si l'utilisateur est un admin
     */
    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    /**
     * Teste si le login est correct, si oui connecte l'utilisateur.
     * Sinon renvoie une exception
     * @param string $email
     * @param string $cleanPassword
     * @return User|null
     * @throws PovException
     */
    public static function testLogin($email, $cleanPassword)
    {
        if ($cleanPassword && $email) {
            /** @var User $user */
            $user = db()->findOne("user", "email='$email'");
            if (!$user) {
                throw new PovException("Utilisateur introuvable");
            }
            if (md5($cleanPassword) == $user->password || $cleanPassword == $user->password) {
                return self::setConnected($user->box());
            } else {
                throw new PovException("Mauvais mot de passe");
            }
        }
        return null;
    }

    /**
     * Dé loggue l'utilisateur
     */
    public static function logout()
    {
        $b = Session::getHumanSession();
        $user = $b->user;
        pov()->log->debug("user deco", [$user]);
        $b->user = NULL;
        $b->isadmin = false;
        db()->store($b);
        self::$connected = null;
        if ($user && $user->isAdmin()) {
            cq()->notify->admins->notify(User::EVENT_USER_CHANGE, $user->name . " n'est plus connecté");
        }
        cq()->notify->human->logout();
    }

    /**
     * @param User $user
     * @return User
     */
    private static function setConnected($user)
    {
        if (!self::$connected || self::$connected->id != $user->id) {

            $b = Session::getHumanSession();
            if (!$b->user_id || $b->user_id != $user->id) {
                $b->user = $user->unbox();
                $b->isadmin = $user->isAdmin();
                db()->store($b);
                cq()->notify->human->login();
                cq()->notify->admins->notify(User::EVENT_USER_CHANGE, "$user->name vient de se connecter");
            }
            self::$connected = $user;
            the()->human->isAdmin = self::connected()->isAdmin();

        }
        return self::$connected;
    }

    /**
     * @var User
     */
    private static $connected = null;

    /**
     * L'utilisateur courrament connecté
     * @return User|null
     */
    public static function connected()
    {
        if (!self::$connected) {
            $s = Session::getHumanSession();
            if ($s->user) {
                self::setConnected($s->user->box());
            }
        }
        return self::$connected;
    }

    /**
     * @return bool true si l'utilisateur est l'utilisateur connecté
     */
    public function isConnectedUser()
    {
        return self::connected() && $this->id && self::connected()->id == $this->id;
    }


    //----------------------------------

    /**
     * Renvoie la liste des utilisateurs dont le role n'est pas défini
     * @return User[]
     */
    public static function findNoRole()
    {
        $nonAdmin = db()->find("user", "role is null or role = ''");
        if ($nonAdmin) {
            return self::boxAll($nonAdmin);
        }
        return [];

    }

    /**
     * Renvoie la liste des utilisateurs dont le role est admin
     * @return User[]
     */
    public static function findAdmins()
    {
        $admins = db()->find("user", "role role = '" . self::ROLE_ADMIN . "'");
        if ($admins) {
            return self::boxAll($admins);
        }
        return [];

    }

    /**
     * Renvoie l'identifiant d'icone svg correspondant au role de l'utilisateur
     * @return string
     */
    public function roleSvg()
    {
        switch ($this->role) {
            case "":
                return "cq-user-question";
            case self::ROLE_ADMIN:
                return "cq-user-admin";
            case self::ROLE_SIMPLE_HUMAN:
            default:
                return "cq-user-no-sign";

        }
    }

    /**
     * Teste si l'utilisateur est connecté (se base sur la session)
     * @param mixed $ifTrue Valeur à renvoyer si online
     * @param mixed $ifFalse Valeur à renvoyer si offline
     * @return mixed
     */
    public function online($ifTrue = true, $ifFalse = false)
    {
        foreach ($this->ownSession as $session) {
            if ($session->id && $session->isadmin) {
                return $ifTrue;
            }
        }
        return $ifFalse;

    }

}