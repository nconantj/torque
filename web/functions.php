<?php

function convertToDateTime($timestamp, $millis = true)
{
    $sec = $timestamp;
    if ($millis) {
        $sec /= 1000;
    }

    $micro = ($sec - floor($sec)) * 1E6;

    return new DateTime(date('Y-m-d H:i:s.' . sprintf("%06d", $micro), $sec));
}
