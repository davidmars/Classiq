<?php


namespace Classiq\Seo;

/**
 * Class SEO
 * @package Classiq\Seo
 */
class SEO
{

    const CHANGE_FREQ_LABEL="Change frequency";

    const CHANGE_FREQ_NEVER="never";
    const CHANGE_FREQ_YEARLY="yearly";
    const CHANGE_FREQ_MONTHLY="monthly";
    const CHANGE_FREQ_WEEKLY="weekly";
    const CHANGE_FREQ_DAILY="daily";
    const CHANGE_FREQ_HOURLY="hourly";

    const CHANGE_FREQ_ALL=[
        "Never"=>self::CHANGE_FREQ_NEVER,
        "Yearly"=>self::CHANGE_FREQ_YEARLY,
        "Monthly"=>self::CHANGE_FREQ_MONTHLY,
        "Weekly"=>self::CHANGE_FREQ_WEEKLY,
        "Daily"=>self::CHANGE_FREQ_DAILY,
        "Hourly"=>self::CHANGE_FREQ_HOURLY,
    ];


}