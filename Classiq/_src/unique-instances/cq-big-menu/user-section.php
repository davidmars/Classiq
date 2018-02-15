<?php
use Classiq\Models\User;
?>
<?if(cq()->isAdmin()):?>
<section cq-panel-is-section="user" <?=$view->attrRefresh()?>>

    <div text-center class="cq-box">
        <time><?=pov()->utils->date->nowMysql()?></time>
        <p>
            Vous êtes connecté en tant que<br>
            <b><a title="modifiez votre profil"
                  cq-on-click="editRecord(<?=User::connected()->uid()?>)"
                  href="#"><?=ucwords(User::connected()->name)?></a>
            </b>
            <br>
            <small><?=User::connected()->email?></small>
        </p>
        <a target="_self" class="cq-btn cq-th-danger" href="<?=\Classiq\C_classiq::logout_url()?>">
            <?=cq()->icoWysiwyg("sign-out")?>
            <span>Déconnexion</span>
        </a>
    </div>


    <label>Ajouter un utilisateur :</label>

    <?=$view->render("cq-new-record/cq-new-record",[
        "types"=>["user"],
        "placeholder"=>"Nom de l'utilisateur"
    ])?>


    <?//la liste d'utilisateurs qui sera injectée dans l'extension ?>
    <?=$view->render("cq-browse-records/cq-browse-records-list",["user"])?>

</section>
<?endif?>