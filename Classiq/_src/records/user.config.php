<?php

use Classiq\Models\User;

/** @var User $vv */
?>

<div class="cq-box">

    <fieldset>
        <label>Image avatar</label>
        <?=$vv->wysiwyg()->field("thumbnail")
            ->file()
            ->setMimeAcceptImagesOnly()
            ->onSavedRefresh("$(this).closest('[data-pov-v-path]')")
            ->button()->render()
        ?>
    </fieldset>

    <fieldset>
        <label>email</label>
        <?=$vv->wysiwyg()
        ->field("email")
        ->string()
        ->input("email")?>
    </fieldset>

    <?if($vv->isConnectedUser() || (cq()->isAdmin() && !$vv->password)):?>
        <?/**
         *
         * si l'utilisateur dont on parle est l'utilisateur devant l'écran
         * OU
         * que l'utilisateur devant l'écran est un admin
         *
         */?>
        <fieldset>
            <label>Mot de passe</label>
            <?=$vv->wysiwyg()
                ->field("cleanPassword")
                ->string()
                ->input("password")?>
            <dfn>(si vous souhaitez en changer)</dfn>
        </fieldset>
    <?endif?>

    <fieldset>
        <label>Rôle</label>
        <?=$vv->wysiwyg()
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