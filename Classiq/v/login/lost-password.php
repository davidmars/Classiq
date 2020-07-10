<?php
/**
 * Permet d'éter un User ou d'en créer un
 */


use Classiq\Models\User;

$view->inside("login/login-layout");
$vv=new \Pov\System\ApiResponse();

$action=the()->request("action");
$email=the()->request("email");

$titre="Mot de passe oublié?";
$form=true;

if($action && $email){
    /** @var User $user */
    $user=db()->findOne("user","email='$email'");
    if($user){
        $user->sendRetrievePasswordEmail();
        $titre="Vous avez reçu un email de récupération de mot de passe";
        $form=false;
    }else{
        $vv->addError("Désolé, cet email n'est rattaché à aucun utilisateur");
    }
}

?>
<h4><?php echo $titre?></h4>
<form method="post">
    <?php if($vv->errors):?>
        <div class="uk-alert uk-alert-danger">
            <?php foreach ($vv->errors as $err):?>
                <div><?php echo $err?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if($form):?>
        <div class="uk-form-controls uk-margin">
            <input class="uk-input" name="email" value="" type="email" placeholder="saisissez votre email">
        </div>

        <div class="uk-form-controls uk-margin">
            <input class="uk-button uk-button-secondary uk-width-1-1" type="submit" name="action" value="Modifier mon mot de passe" >
        </div>
    <?php endif; ?>
    <hr>
    <div class="uk-margin">
        <a class="uk-button uk-button-default uk-width-1-1" href="<?php echo \Classiq\C_classiq::login_url()?>">Connexion</a><br><br>
        <a class="uk-button uk-button-default uk-width-1-1" href="<?php echo \Classiq\C_classiq::index_url()?>">Retourner sur le site</a>
    </div>
</form>