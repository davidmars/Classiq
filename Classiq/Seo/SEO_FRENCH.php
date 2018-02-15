<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/12/2017
 * Time: 04:21
 */

namespace Classiq\Seo;


/**
 * Class SEO_FRENCH les valeurs lisibles par les humains sont en français
 * @package Classiq\Seo
 */
class SEO_FRENCH extends SEO
{

    const CHANGE_FREQ_LABEL="Fréquence de mise à jour";

    const CHANGE_FREQ_ALL=[
        "Jamais"=>self::CHANGE_FREQ_NEVER,
        "Une fois par an"=>self::CHANGE_FREQ_YEARLY,
        "Tous les mois"=>self::CHANGE_FREQ_MONTHLY,
        "Toutes les semaines"=>self::CHANGE_FREQ_WEEKLY,
        "Tous les jours"=>self::CHANGE_FREQ_DAILY,
        "Toutes les heures"=>self::CHANGE_FREQ_HOURLY,
    ];
}