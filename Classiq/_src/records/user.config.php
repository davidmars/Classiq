<?php

use Classiq\Models\User;

/** @var User $vv */
?>

<div class="cq-box">

    <fieldset>
        <label>Image avatar</label>
        <?php echo $vv->wysiwyg()->field("thumbnail")
            ->file()
            ->setMimeAcceptImagesOnly()
            ->onSavedRefresh("$(this).closest('[data-pov-v-path]')")
            ->button()->render()
        ?>
    </fieldset>

    <fieldset>
        <label>email</label>
        <?php echo $vv->wysiwyg()
        ->field("email")
        ->string()
        ->input("email")?>
    </fieldset>

    <?php if($vv->isConnectedUser() || (cq()->isAdmin() && !$vv->password)):?>
        <?php /**
         *
         * si l'utilisateur dont on parle est l'utilisateur devant l'écran
         * OU
         * que l'utilisateur devant l'écran est un admin
         *
         */?>
        <fieldset>
            <label>Mot de passe</label>
            <?php echo $vv->wysiwyg()
                ->field("cleanPassword")
                ->string()
                ->input("password")?>
            <dfn>(si vous souhaitez en changer)</dfn>
        </fieldset>
    <?php endif; ?>

    <fieldset>
        <label>Rôle</label>
        <?php echo $vv->wysiwyg()
            ->field("role")
            ->string()
            ->select(
                [
                    "Non défini"=>   "",
                    "Ne peut pas modifier le site"=>    User::ROLE_SIMPLE_HUMAN,
                    "Peut modifier le site"=>           User::ROLE_ADMIN,

                ]
            )?>
    </fieldset>
</div>