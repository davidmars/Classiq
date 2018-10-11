<?php

namespace Classiq\Wysiwyg\FieldsTyped;

/**
 * Methodes spécifiques pour les uploads de fichiers
 * @package Classiq\Wysiwyg\FieldsTyped
 */
trait TraitUploadOptions
{
    /**
     * @var string attribut accept placé sur le champ d'upload
     */
    public $mimeTypeAccept="";
    /**
     * Permet de n'uploader QUE des images
     * @return $this
     */
    public function setMimeAcceptImagesOnly()
    {
        $this->mimeTypeAccept="accept='image/*'";
        return $this;
    }
    /**
     * Permet de n'uploader QUE des vidéos
     * @return $this
     */
    public function setMimeAcceptVideoOnly()
    {
        $this->mimeTypeAccept="accept='video/*'";
        return $this;
    }
    /**
     * Permet de n'uploader QUE des audios
     * @return $this
     */
    public function setMimeAcceptAudioOnly()
    {
        $this->mimeTypeAccept="accept='.mp3,audio/*'";
        return $this;
    }
}