<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/12/2017
 * Time: 04:21
 */

namespace Classiq\Seo;


use Classiq\Wysiwyg\Wysiwyg;

/**
 * Class SEO_TRANSLATED les valeurs lisibles par les humains sont en français
 * @package Classiq\Seo
 */
class SEO_TRANSLATED extends SEO
{

    const CHANGE_FREQ_ALL=[
        "Jamais"=>self::CHANGE_FREQ_NEVER,
        "Une fois par an"=>self::CHANGE_FREQ_YEARLY,
        "Tous les mois"=>self::CHANGE_FREQ_MONTHLY,
        "Toutes les semaines"=>self::CHANGE_FREQ_WEEKLY,
        "Tous les jours"=>self::CHANGE_FREQ_DAILY,
        "Toutes les heures"=>self::CHANGE_FREQ_HOURLY,
    ];

    public static function CHANGE_FREQ_ALL(){
        $r=[];
        $r[cq()->tradWysiwyg("Jamais")]=self::CHANGE_FREQ_NEVER;
        $r[cq()->tradWysiwyg("Une fois par an")]=self::CHANGE_FREQ_YEARLY;
        $r[cq()->tradWysiwyg("Tous les mois")]=self::CHANGE_FREQ_MONTHLY;
        $r[cq()->tradWysiwyg("Toutes les semaines")]=self::CHANGE_FREQ_WEEKLY;
        $r[cq()->tradWysiwyg("Tous les jours")]=self::CHANGE_FREQ_DAILY;
        $r[cq()->tradWysiwyg("Toutes les heures")]=self::CHANGE_FREQ_HOURLY;
        return $r;
    }
}