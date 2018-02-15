<?php
/**
 * Permet à l'utilisateur de se logger
 */

use Classiq\Models\User;

$view->inside("login/login-layout");



$vv=new \Pov\System\ApiResponse();

if(the()->request("logout")){
    User::logout();
}

if(the()->request("action")){
    $passwordClean=$vv->testAndGetRequest("passwordClean","Veuillez saisir votre mot de passe",true);
    $email=$vv->testAndGetRequest("email","Veuillez saisir un email",true);
    if(!$vv->errors){
        try{
            $user=User::testLogin($email,$passwordClean);
        }catch(\Pov\PovException $e){
            $vv->addError($e->getMessage());
        }
    }
}

?>




    <form class="uk-form" method="post">

        <?if($vv->errors):?>
            <div class="uk-alert uk-alert-danger">
                <?foreach ($vv->errors as $err):?>
                    <div><?=$err?></div>
                <?endforeach;?>
            </div>
        <?endif;?>



        <?if(User::connected()):?>

            <?
            /**
             * Connecté....
             */
            ?>

            <div class="uk-alert uk-alert-<?=User::connected()->isAdmin()?"success":"warning"?>">
                <h4>Hello <?=ucwords(User::connected()->name)?>.</h4>
                <?if(User::connected()->isAdmin()):?>
                    Vous pouvez <a href="<?=\Classiq\C_classiq::index_url()?>">éditer le contenu du site</a>.
                <?else:?>
                    Vous ne pouvez pas éditer le contenu du site. Il faut qu'un administrateur vous en donne le droit.
                <?endif?>
            </div>

            <div class="uk-margin">
                <a class="uk-button uk-button-danger uk-width-1-1" href="<?=\Classiq\C_classiq::logout_url()?>">Déconnexion</a><br><br>
                <a class="uk-button uk-button-default uk-width-1-1" href="<?=\Classiq\C_classiq::login_url("edit-user")?>?id=<?=User::connected()->id?>">Modifier vos informations</a>
            </div>

        <?else:?>

            <?
            /**
             * Pas connecté....
             */
            ?>

            <div class="uk-form-controls uk-margin">
                <input class="uk-input" name="email" type="text" placeholder="Email">
            </div>

            <div class="uk-form-controls uk-margin">
                <input class="uk-input" name="passwordClean" type="password" placeholder="Mot de passe">
            </div>

            <div class="uk-form-controls uk-margin">
                <input type="submit" name="action" value="Connexion" class="uk-button uk-button-secondary uk-width-1-1">
            </div>


        <?endif?>

        <hr>
        <div class="uk-form-controls uk-margin">
            <a class="uk-button uk-button-default uk-width-1-1" href="<?=\Classiq\C_classiq::login_url("lost-password")?>">Mot de passe oublié?</a>
        </div>
        <div class="uk-form-controls uk-margin">
            <a class="uk-button uk-button-default uk-width-1-1" href="<?=\Classiq\C_classiq::login_url("edit-user")?>">Créer un nouveau compte</a>
        </div>
        <div class="uk-form-controls uk-margin">
            <a class="uk-button uk-button-default uk-width-1-1" href="<?=\Classiq\C_classiq::index_url()?>">Retourner sur le site</a>
        </div>







    </form>







