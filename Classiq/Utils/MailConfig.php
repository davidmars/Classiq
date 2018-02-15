<?php

namespace Classiq\Utils;

/**
 * Un modèle de configuration pour les envois de mail authentifiés
 * On l'utilise pour stocker proprement les variables à utiliser avec PHPMailer par exemple
 * @package Classiq\Utils
 */
class MailConfig
{
    /**
     * Specify main and backup SMTP servers
     * exemple : smtp1.example.com;smtp2.example.com
     * @var string
     */
    public $host = '';
    /**
     * Enable SMTP authentication
     * @var bool
     */
    public $SMTPAuth = true;
    /**
     * SMTP username
     * exemple : user@example.com
     * @var string
     */
    public $username = "";
    /**
     * Nom associé à l'adresse email
     * exemple : "noreply" ou "formulaire contact" ou encore "Nicolas Dupont"
     * @var string
     */
    public $displayName="noreply";
    /**
     * SMTP password
     * exemple
     * azertyuiop78:!%
     * @var string
     */
    public $password = 'secret';
    /**
     * Enable TLS encryption, `ssl` also accepted
     * exemple tls or ssl
     * @var string
     */
    public $SMTPSecure = 'tls';
    /**
     * TCP port to connect to
     * exemple 587
     * @var int
     */
    public $port = 587;


}