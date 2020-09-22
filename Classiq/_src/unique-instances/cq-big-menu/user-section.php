<?php
use Classiq\Models\User;
?>
<?php if(cq()->isAdmin()):?>
<section cq-panel-is-section="user" <?php echo $view->attrRefresh()?>>

    <div text-center class="cq-box">
        <time><?php echo pov()->utils->date->nowMysql()?></time>
        <p>
            <?php echo cq()->tradWysiwyg("Vous êtes connecté en tant que")?>
            <br>
            <b><a title="modifiez votre profil"
                  cq-on-click="editRecord(<?php echo User::connected()->uid()?>)"
                  href="#"><?php echo ucwords(User::connected()->name)?></a>
            </b>
            <br>
            <small><?php echo User::connected()->email?></small>
        </p>
        <a target="_self" class="cq-btn cq-th-danger" href="<?php echo \Classiq\C_classiq::logout_url()?>">
            <?php echo pov()->svg->use("cq-sign-out")?>
            <span><?php echo cq()->tradWysiwyg("Déconnexion")?></span>
        </a>
    </div>


    <label><?php echo cq()->tradWysiwyg("Ajouter un utilisateur")?> :</label>

    <?php echo $view->render("cq-new-record/cq-new-record",[
        "types"=>["user"],
        "placeholder"=>cq()->tradWysiwyg("Nom de l'utilisateur")
    ])?>


    <?php //la liste d'utilisateurs qui sera injectée dans l'extension ?>
    <?php echo $view->render("cq-browse-records/cq-browse-records-list",["user"])?>

</section>
<?php endif; ?>