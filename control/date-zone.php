<?php
function dateZone(): array
{
    $return = array();
    $zone = new DateTime("now", new DateTimeZone('Asia/Tashkent'));
    $return['years'] = $zone->format('Y');
    $return['month'] = $zone->format('m');
    $return['date'] = $zone->format('d');
    $return['hours'] = $zone->format('H');
    $return['minutes'] = $zone->format('i');
    $return['seconds'] = $zone->format('s');

    return $return;
}
