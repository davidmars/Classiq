<?php
/**
 * Permet d'éter un User ou d'en créer un
 */

use Classiq\Models\User;

$view->inside("login/login-layout");
$vv=new \Pov\System\ApiResponse();
$titre="Profil";
$form=false;
$token="";
$action=the()->request("action");
$delete=the()->request("delete");

/** @var User $user Le user à afficher*/
$user=db()->dispense("user")->box();
$id=the()->request("id");
if($id){

    $user=db()->findOne("user","id='$id'");
    if(!$user){
        $vv->addError("Impossible de trouver le profil");
    }
}else{
    $token=the()->request("token");
    if($token){
        db()->debug(true,1);
        $user=User::getUserByToken($token,true);

        //pov()->log->debug("user by token",[db()->getDatabaseAdapter()->getDatabase()->getLogger()->grep('SELECT')]);
        if(!$user){
            $vv->addError("Le lien utilisé pour récupérer votre mot de passe est périmé");
        }else{
            $vv->addMessage("Veuillez modifier votre mot de passe");
        }
    }
}




if($user && $user->id){
    $user=$user->box();
    $titre="Editer le profil";

    if($delete){
        if(the()->human->isAdmin){
            db()->trash($user);
            $vv->addError("Utilisateur supprimé !");
            $user=db()->dispense("user")->box();
        }
    }
}else{
    $form=true;
    $user=db()->dispense("user")->box();
    $titre="Créer votre profil";
}

if(User::connected()){
    if($user->id){
        if($user->isConnectedUser()){
            $form=true;
            $titre="Modifier votre compte";
            if($token){
                $titre="Veuillez modifier votre mot de passe";
            }
        }else{
            $form=true;
            $titre="Modifier le compte de $user->name";
        }
    }else{
        $form=true;
        $titre="Créer un nouveau compte";
    }
}else{
    if($user->id){
        $titre="...";
        $err="Vous devez vous connecter pour modifier un compte";
        $vv->addError($err);
    }else{
        $form=true;
        $titre="Créer un nouveau compte";
    }
}


if($action){
    $email=$vv->testAndGetRequest("email","Le mail obligatoire.",true);
    $name=$vv->testAndGetRequest("name","Le nom d'utilisateur est obligatoire.",true);
    $role=the()->request("role");
    $cleanPassword=the()->request("cleanPassword","");

    if($vv->success){
        $user->cleanPassword=$cleanPassword;
        $user->name=$name;
        $user->email=$email;
        if($role){
            $user->role=$role;
        }

        try{
            db()->store($user);
            $vv->addMessage("Vos modifications ont été entregistrées");

        }catch(\Pov\PovException $e){
            $vv->addError($e->getMessage());
        }

        //connecte l'utilisateur si il ne l'est pas
        if($user->id && !User::connected()){
            User::testLogin($email,$cleanPassword);
            $r=\Classiq\C_classiq::login_url()->absolute();
            the()->headerOutput->setRedirect($r);
            the()->boot->end();
        }


    }
}




?>




    <h4 class=""><?php echo $titre?></h4>



    <form method="post">

        <?php if($vv->errors):?>
            <div class="uk-alert uk-alert-danger">
                <?php foreach ($vv->errors as $err):?>
                    <div><?php echo $err?></div>
                <?php endforeach; ?>
            </div>
        <?php elseif($vv->messages):?>
            <div class="uk-alert uk-alert-success">
                <?php foreach ($vv->messages as $mess):?>
                    <div><?php echo $mess?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if($user->id):?>
            <div class="uk-text-right">
            <input type="hidden" name="id" value="<?php echo $user->id?>">
            </div>
        <?php endif; ?>

        <?php if($form):?>

            <div class="uk-form-controls uk-margin">
                <input class="uk-input" name="name" value="<?php echo $user->name?>" type="text" placeholder="Nom d'utilisateur">
            </div>

            <div class="uk-form-controls uk-margin">
                <input class="uk-input" name="email" value="<?php echo $user->email?>" type="email" placeholder="email">
            </div>

            <?php
            /**
            * Mot de passe si l'utilisateur connecté est l'utilisateur édité ou si on est entrain d'en créer un nouveau
            */
            ?>
            <?php if( ($user && $user->isConnectedUser()) || !$user->id):?>
                <div class="uk-form-controls uk-margin">
                    <input class="uk-input" name="cleanPassword" value="<?php echo $user->cleanPassword?>" type="password" placeholder="Mot de passe" autocomplete="new-password">
                </div>
            <?php endif; ?>


            <?php
            /**
             * Mot de passe si l'utilisateur connecté est l'utilisateur édité ou si on est entrain d'en créer un nouveau
             */
            ?>
            <?php if( (User::connected() && User::connected()->isAdmin())):?>
                <div class="uk-margin uk-text-left">
                    <label class="uk-form-label">Peut modifier le contenu du site?</label>
                    <div class="uk-form-controls">
                        <select class="uk-select" name="role">
                            <option value="">Choisissez</option>
                            <option <?php echo $user->role==User::ROLE_ADMIN ? "selected":""?> value="<?php echo User::ROLE_ADMIN?>">Oui</option>
                            <option <?php echo $user->role==User::ROLE_SIMPLE_HUMAN ? "selected":""?> value="<?php echo User::ROLE_SIMPLE_HUMAN?>">Non</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>



            <div class="uk-form-controls uk-margin">
                <input class="uk-button uk-button-secondary uk-width-1-1" type="submit" name="action" value="Enregistrer" >
            </div>

            <hr>

            <?php if($user->id && (User::connected() && User::connected()->isAdmin())):?>
                <div class="uk-form-controls uk-margin">
                    <input class="uk-button uk-button-danger uk-width-1-1" type="submit" name="delete" value="Supprimer ce compte !" >
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(User::connected()):?>
            <div class="uk-margin">
                <a class="uk-button uk-button-danger uk-width-1-1" href="<?php echo \Classiq\C_classiq::logout_url()?>">Déconnexion</a><br><br>
            </div>
        <?php else: ?>
            <div class="uk-margin">
                <a class="uk-button uk-button-default uk-width-1-1" href="<?php echo \Classiq\C_classiq::login_url()?>">Connexion</a><br><br>
            </div>
        <?php endif; ?>

        <div class=" uk-margin">
            <a class="uk-button uk-button-default uk-width-1-1" href="<?php echo \Classiq\C_classiq::index_url()?>">Retourner sur le site</a>
        </div>

    </form>





