<?php

require_once("./creds.php");
require_once('db_functions.php');

// Connect to Database
$db = new DBAccess($db_host, $db_user, $db_pass, "INFORMATION_SCHEMA");

// Create array of column name/comments for chart data selector form
$colqry = $db->get_data(
    "COLUMNS",
    array(
        "COLUMN_NAME",
        "COLUMN_COMMENT",
        "DATA_TYPE"
    ),
    null,
    "TABLE_SCHEMA='" . $db_name . "' AND TABLE_NAME='" . $db_table . "'"
);

// Select the column name and comment for data that can be plotted.
while ($x = $db->get_row_array_assoc($colqry)) {
    if ((substr($x["COLUMN_NAME"], 0, 1) == "k") && ($x["DATA_TYPE"] == "float")) {
        $coldata[] = array("colname" => $x["COLUMN_NAME"], "colcomment" => $x["COLUMN_COMMENT"]);
    }
}

$numcols = strval(count($coldata) + 1);

//TODO: Do this once in a dedicated file
if (isset($_POST["id"])) {
    $session_id = preg_replace('/\D/', '', $_POST['id']);
} elseif (isset($_GET["id"])) {
    $session_id = preg_replace('/\D/', '', $_GET['id']);
}


// If we have a certain session, check which colums contain no information at all
$coldataempty = array();
if (isset($session_id)) {
    $db->change_db($db_name);

    //Count distinct values for each known column
    //TODO: Unroll loop into single query
    foreach ($coldata as $col) {
        $colname = $col["colname"];

        // Count number of different values for this specific field
        $colqry = $db->get_data(
            $db_table,
            array( "count(DISTINCT $colname)<2 as $colname" ),
            null,
            "session=$session_id",
            null
        );

        $colresult = $db->get_assoc_row_data($colqry);
        $coldataempty[$colname] = $colresult[$colname];
    }
}
