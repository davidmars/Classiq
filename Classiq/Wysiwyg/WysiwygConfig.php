<?php

namespace Classiq\Wysiwyg;


use Pov\System\AbstractSingleton;

/**
 * Class WysiwygConfig
 * @package Classiq\Wysiwyg
 *
 * @method static WysiwygConfig inst()
 */
class WysiwygConfig extends AbstractSingleton
{
    /**
     * @var string[] Les types de record que l'on peut créer (se configure en fonction du site)
     */
    public $recordsWeCanCreate=["Page"];
    /**
     * @var string[] Les types de record que l'on peut sélectionner (se configure en fonction du site)
     */
    public $recordsWeCanSelect=["Page"];
    /**
     * @var string[] Les types de record que l'on peut sélectionner (se configure en fonction du site)
     */
    public $recordsWeCanBrowse=["Page"];

}