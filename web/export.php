<?php
require("./creds.php");
require_once ('db_functions.php');

// Connect to Database
$db = new DBAccess($db_host, $db_user, $db_pass, $db_name);

if (isset($_GET["sid"])) {
    $session_id = $db->get_escape_string($_GET['sid']);
    // Get data for session
    $output = "";
	
	$sql = $db->get_data(
		$db_table,
		array("*"),
		null,
		"session=$session_id",
		"time DESC"
	);

    if ($_GET["filetype"] == "csv") {
        $columns_total = $db->get_field_count($sql);

        // Get The Field Name
        for ($i = 0; $i < $columns_total; $i++) {
			$heading = $db->get_field_name($sql, $i);
            $output .= '"'.$heading.'",';
        }
        $output .="\n";

        // Get Records from the table
        while ($row = $db->get_row_array_num($sql)) {
            for ($i = 0; $i < $columns_total; $i++) {
                $output .='"'.$row["$i"].'",';
            }
            $output .="\n";
        }

        // Download the file
        $csvfilename = "torque_session_".$session_id.".csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$csvfilename);

        echo $output;
        exit;
    }
    else if ($_GET["filetype"] == "json") {
        $rows = array();
        while($r = $db->get_assoc_row_data($sql)) {
            $rows[] = $r;
        }
        $jsonrows = json_encode($rows);

        // Download the file
        $jsonfilename = "torque_session_".$session_id.".json";
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename='.$jsonfilename);

        echo $jsonrows;
        exit;
    }
    else {
        exit;
    }
}
else {
    exit;
}

?>
