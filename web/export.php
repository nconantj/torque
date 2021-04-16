<?php
require("./creds.php");

// Connect to Database
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die(mysqli_error());

if (isset($_GET["sid"])) {
    $session_id = mysqli_real_escape_string($con, $_GET['sid']);
    // Get data for session
    $output = "";
    $sql = mysqli_query($con, "SELECT * FROM $db_table WHERE session=$session_id ORDER BY time DESC;") or die(mysqli_error());

    if ($_GET["filetype"] == "csv") {
        $columns_total = mysqli_field_count($sql);

        // Get The Field Name
        for ($i = 0; $i < $columns_total; $i++) {
            $heading = mysqli_fetch_field_direct($sql, $i)['name'];
            $output .= '"'.$heading.'",';
        }
        $output .="\n";

        // Get Records from the table
        while ($row = mysqli_fetch_array($sql)) {
            for ($i = 0; $i < $columns_total; $i++) {
                $output .='"'.$row["$i"].'",';
            }
            $output .="\n";
        }

        mysqli_free_result($sql);
        mysqli_close($con);

        // Download the file
        $csvfilename = "torque_session_".$session_id.".csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$csvfilename);

        echo $output;
        exit;
    }
    else if ($_GET["filetype"] == "json") {
        $rows = array();
        while($r = mysqli_fetch_assoc($sql)) {
            $rows[] = $r;
        }
        $jsonrows = json_encode($rows);

        mysqli_free_result($sql);
        mysqli_close($con);

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
