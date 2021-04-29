<?php

require_once("./creds.php");
require_once('db_functions.php');

session_set_cookie_params(0, dirname($_SERVER['SCRIPT_NAME']));
session_start();
$timezone = $_SESSION['time'];

// Connect to Database
$db = new DBAccess($db_host, $db_user, $db_pass, $db_name);

// Get list of unique session IDs
$sessionqry = $db->get_data(
    $db_table,
    array(
        "COUNT(*) as `Session Size`",
        "MIN(time) as `MinTime`",
        "MAX(time) as `MaxTime`",
        "session"
    ),
    array("session"),
    null,
    "time DESC"
);

// Create an array mapping session IDs to date strings
$seshdates = array();
$seshsizes = array();
while ($row = $db->get_assoc_row_data($sessionqry)) {
    $session_size = $row["Session Size"];
    $session_duration = $row["MaxTime"] - $row["MinTime"];
    $session_duration_str = gmdate("H:i:s", $session_duration / 1000);

    // Drop sessions smaller than 60 data points
    if ($session_size >= 60) {
        $sid = $row["session"];
        $sids[] = preg_replace('/\D/', '', $sid);
        $seshdates[$sid] = date("F d, Y  h:ia", substr($sid, 0, -3));
        $seshsizes[$sid] = " (Length $session_duration_str)";
    } else {
    }
}
